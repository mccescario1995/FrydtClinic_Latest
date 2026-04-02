<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Prescription;
use App\Models\Service;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Display payment form
     */
    public function create(Request $request, $prescriptionId = null)
    {
        $appointmentId = $request->get('appointment_id');
        $prescriptionId = $prescriptionId ?: $request->get('prescription_id');
        $serviceIds = $request->get('services', []);

        $appointment = null;
        $prescription = null;
        $services = collect();
        $total = 0;

        // If payment is for an appointment
        if ($appointmentId) {
            $appointment = Appointment::with('service')->findOrFail($appointmentId);
            $services->push($appointment->service);
            $total = $appointment->service->base_price;
            // dd($appointment->service);
        }

        // If payment is for a prescription
        if ($prescriptionId) {
            $prescription = Prescription::with('inventory', 'treatment')->findOrFail($prescriptionId);
            // Ensure prescription belongs to current user
            if ($prescription->treatment->patient_id !== Auth::id()) {
                abort(403, 'Unauthorized access to prescription.');
            }
            // Ensure prescription is dispensed and has a total price
            if (! $prescription->isFullyDispensed || ! $prescription->total_price) {
                return redirect()->back()->with('error', 'Prescription is not eligible for payment.');
            }
            $total = $prescription->total_price;
        }

        // If payment is for individual services
        if (! empty($serviceIds)) {
            $selectedServices = Service::whereIn('id', $serviceIds)->get();
            // Merge and ensure unique services by ID
            $services = $services->merge($selectedServices)->unique('id');
            $total += $selectedServices->sum('base_price');
        }

        if ($services->isEmpty() && ! $prescription) {
            return redirect()->back()->with('error', 'No services or prescription selected for payment.');
        }

        return view('patient.payments.create', compact('appointment', 'prescription', 'services', 'total'));
    }

    /**
     * Process payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'payment_method' => 'required|in:paypal,gcash',
        ]);

        // Ensure at least one of appointment, prescription, or services is provided
        if (! $request->appointment_id && ! $request->prescription_id && empty($request->services)) {
            return redirect()->back()->with('error', 'No payment item selected.');
        }

        try {
            DB::beginTransaction();

            $total = 0;
            $services = collect();

            // Handle appointment payment
            if ($request->appointment_id) {
                $appointment = Appointment::with('service')->findOrFail($request->appointment_id);

                // Only add the appointment service if NOT inside selected service IDs
                if (! in_array($appointment->service_id, $request->services ?? [])) {
                    $services->push($appointment->service);
                    $total += $appointment->service->base_price;
                }
            }

            // Handle prescription payment
            if ($request->prescription_id) {
                $prescription = Prescription::with('treatment')->findOrFail($request->prescription_id);
                // Ensure prescription belongs to current user
                if ($prescription->treatment->patient_id !== Auth::id()) {
                    abort(403, 'Unauthorized access to prescription.');
                }
                // Ensure prescription is dispensed and has a total price
                if (! $prescription->isFullyDispensed || ! $prescription->total_price) {
                    return redirect()->back()->with('error', 'Prescription is not eligible for payment.');
                }
                $total += $prescription->total_price;
            }

            // Handle individual services
            if (! empty($request->services)) {
                $selectedServices = Service::whereIn('id', $request->services)->get();
                $services = $services->merge($selectedServices)->unique('id');
                $total += $selectedServices->sum('base_price');
            }

            // Create payment record
            $payment = Payment::create([
                'patient_id' => Auth::id(),
                'appointment_id' => $request->appointment_id,
                'prescription_id' => $request->prescription_id,
                'amount' => $total,
                'paid_amount' => 0,
                'remaining_balance' => $total,
                'currency' => 'PHP',
                'payment_method' => $request->payment_method,
                'description' => $request->prescription_id ? 'Payment for Prescription' : 'Payment for FRYDT Clinic Services',
                'status' => 'pending',
            ]);

            // Create payment items for services (ensure no duplicates)
            $uniqueServices = $services->unique(fn ($s) => $s->id)->values();

            foreach ($uniqueServices as $service) {
                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => 1,
                    'unit_price' => $service->base_price,
                    'total_price' => $service->base_price,
                ]);
            }

            // Update prescription status if payment is for prescription
            if ($request->prescription_id) {
                $prescription = Prescription::findOrFail($request->prescription_id);
                $prescription->update(['status' => 'pending_payment']);
            }

            DB::commit();

            // Handle different payment methods
            if ($request->payment_method === 'paypal') {
                return $this->processPayPalPayment($payment);
            } elseif ($request->payment_method === 'gcash') {
                return $this->processGcashPayment($payment);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to create payment. Please try again.');
        }
    }

    /**
     * Process PayPal payment
     */
    private function processPayPalPayment(Payment $payment)
    {
        try {
            $order = $this->paypalService->createOrder($payment);

            // Find the approval URL
            $approvalUrl = null;
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if ($approvalUrl) {
                return redirect($approvalUrl);
            } else {
                throw new \Exception('PayPal approval URL not found');
            }

        } catch (\Exception $e) {
            Log::error('PayPal payment error: '.$e->getMessage());
            $payment->markAsFailed();

            return redirect()->route('patient.payments.show', $payment)
                ->with('error', 'PayPal payment failed. Please try again or contact support.');
        }
    }

    /**
     * Process GCash payment
     */
    private function processGcashPayment(Payment $payment)
    {
        try {
            // Generate GCash reference
            $gcashReference = 'GCASH-'.$payment->payment_reference;

            // Store reference in payment and set initial amounts
            $payment->update([
                'gcash_reference' => $gcashReference,
                'paid_amount' => 0,
                'remaining_balance' => $payment->amount,
                'status' => 'pending',
            ]);

            return redirect()->route('patient.payments.show', $payment)
                ->with('success', 'GCash payment initiated. Please upload proof of payment after completing the transaction.');

        } catch (\Exception $e) {
            Log::error('GCash payment error: '.$e->getMessage());
            $payment->markAsFailed();

            return redirect()->route('patient.payments.show', $payment)
                ->with('error', 'GCash payment failed. Please try again or contact support.');
        }
    }

    /**
     * Handle successful PayPal payment
     */
    public function success(Request $request)
    {
        $orderId = $request->get('token');
        $payerId = $request->get('PayerID');

        if (! $orderId || ! $payerId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Invalid payment response from PayPal.');
        }

        try {
            // Find payment by PayPal order ID
            $payment = Payment::where('paypal_order_id', $orderId)->firstOrFail();

            // Capture the payment
            $captureResponse = $this->paypalService->captureOrder($orderId);

            if ($captureResponse['status'] === 'COMPLETED') {
                // Update payment record
                $payment->update([
                    'status' => 'completed',
                    'paypal_payer_id' => $payerId,
                    'paypal_payment_id' => $captureResponse['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                    'paypal_response' => $captureResponse,
                    'paid_amount' => $payment->amount,
                    'remaining_balance' => 0,
                    'paid_at' => now(),
                ]);

                // Fire SMS notification event
                event(new \App\Events\PaymentCompleted($payment));

                // Update appointment status if applicable
                if ($payment->appointment) {
                    $payment->appointment->update(['status' => 'confirmed']);
                }

                // Update prescription status if applicable
                if ($payment->prescription) {
                    $payment->prescription->update(['status' => 'fully_dispensed']);
                }

                return redirect()->route('patient.payments.show', $payment)
                    ->with('success', 'Payment completed successfully!');
            } else {
                $payment->markAsFailed();

                return redirect()->route('patient.payments.show', $payment)
                    ->with('error', 'Payment could not be completed. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Payment success handling error: '.$e->getMessage());

            return redirect()->route('patient.dashboard')
                ->with('error', 'Error processing payment. Please contact support.');
        }
    }

    /**
     * Handle cancelled PayPal payment
     */
    public function cancel(Request $request)
    {
        $orderId = $request->get('token');

        if ($orderId) {
            $payment = Payment::where('paypal_order_id', $orderId)->first();
            if ($payment) {
                $payment->update(['status' => 'cancelled']);

                // Reset prescription status if applicable
                if ($payment->prescription) {
                    $payment->prescription->update(['status' => 'fully_dispensed']);
                }

                return redirect()->route('patient.payments.show', $payment)
                    ->with('warning', 'Payment was cancelled.');
            }
        }

        return redirect()->route('patient.dashboard')
            ->with('warning', 'Payment was cancelled.');
    }

    /**
     * Show payment details
     */
    public function show($paymentId)
    {
        // dd($paymentId);
        // $this->authorize('view', $payment);

        // $payment->load(['patient', 'appointment.service', 'items.service']);
        $payment = Payment::with(['patient', 'appointment.service', 'prescription.inventory', 'items.service', 'approver'])
            ->findOrFail($paymentId);

        // dd($payment);

        return view('patient.payments.show', compact('payment'));
    }

    /**
     * List user's payments
     */
    public function index()
    {
        $payments = Payment::with(['appointment.service', 'prescription.inventory', 'items.service'])
            ->where('patient_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('patient.payments.index', compact('payments'));
    }

    /**
     * Upload proof of payment
     */
    public function uploadProofOfPayment(Request $request, $payments)
    {

        $payment = Payment::where('id', $payments)->first();
        // dd($payments, $payment);
        $request->validate([
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string|max:500',
            'paid_amount' => 'required|numeric|min:0|max:'.$payment->remaining_balance,
        ]);

        try {
            // Store the uploaded file
            $file = $request->file('proof_of_payment');
            $filename = 'proof_'.$payment->payment_reference.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('proofs_of_payment', $filename, 'public');

            // Update payment with proof and partial payment info
            $payment->uploadProofOfPayment($path, $request->notes);
            $payment->processPartialPayment($request->paid_amount);

            return redirect()->route('patient.payments.show', $payment)
                ->with('success', 'Proof of payment uploaded successfully. Your payment is now awaiting admin approval.');

        } catch (\Exception $e) {
            Log::error('Proof of payment upload error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to upload proof of payment. Please try again.');
        }
    }

    /**
     * Admin approve payment
     */
    public function approvePayment(Request $request, Payment $payment)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $payment->approve(Auth::id(), $request->notes);

            // Fire SMS notification event
            event(new \App\Events\PaymentCompleted($payment));

            return redirect()->back()
                ->with('success', 'Payment approved successfully.');

        } catch (\Exception $e) {
            Log::error('Payment approval error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to approve payment. Please try again.');
        }
    }

    /**
     * Admin reject payment
     */
    public function rejectPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $payment->reject($request->notes);

            return redirect()->back()
                ->with('success', 'Payment rejected.');

        } catch (\Exception $e) {
            Log::error('Payment rejection error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to reject payment. Please try again.');
        }
    }
}
