<?php

namespace App\Services;

use App\Models\Payment;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Exception;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->setCurrency('PHP');
        $this->provider->getAccessToken();
    }

    /**
     * Create PayPal order
     */
    public function createOrder(Payment $payment): array
    {
        try {
            $data = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $payment->payment_reference,
                        'amount' => [
                            'currency_code' => $payment->currency,
                            'value' => number_format($payment->amount, 2, '.', ''),
                        ],
                        'description' => $payment->description ?: 'FRYDT Clinic Payment',
                    ]
                ],
                'application_context' => [
                    'return_url' => route('patient.payment.success'),
                    'cancel_url' => route('patient.payment.cancel'),
                    'brand_name' => 'FRYDT Lying-In Clinic',
                ]
            ];

            $order = $this->provider->createOrder($data);

            if (isset($order['id'])) {
                // Update payment with PayPal order ID
                $payment->update([
                    'paypal_order_id' => $order['id'],
                    'paypal_response' => $order
                ]);

                return $order;
            }

            throw new Exception('Failed to create PayPal order: ' . json_encode($order));
        } catch (Exception $e) {
            Log::error('PayPal create order error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Capture PayPal order
     */
    public function captureOrder(string $orderId): array
    {
        try {
            $result = $this->provider->capturePaymentOrder($orderId);

            if (isset($result['status']) && $result['status'] === 'COMPLETED') {
                return $result;
            }

            throw new Exception('Failed to capture PayPal order: ' . json_encode($result));
        } catch (Exception $e) {
            Log::error('PayPal capture order error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails(string $orderId): array
    {
        try {
            $order = $this->provider->showOrderDetails($orderId);

            if (isset($order['id'])) {
                return $order;
            }

            throw new Exception('Failed to get PayPal order details: ' . json_encode($order));
        } catch (Exception $e) {
            Log::error('PayPal get order details error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Build order items for PayPal
     */
    private function buildOrderItems(Payment $payment): array
    {
        $items = [];

        foreach ($payment->items as $item) {
            $items[] = [
                'name' => $item->service_name,
                'unit_amount' => [
                    'currency_code' => $payment->currency,
                    'value' => number_format($item->unit_price, 2, '.', '')
                ],
                'quantity' => (string) $item->quantity,
                'category' => 'DIGITAL_GOODS'
            ];
        }

        return $items;
    }
}
