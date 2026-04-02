<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinancialReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $billings;

    public function __construct($billings)
    {
        $this->billings = $billings;
    }

    public function collection()
    {
        return $this->billings;
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Patient Name',
            'Invoice Date',
            'Due Date',
            'Service Start',
            'Service End',
            'Subtotal',
            'Discount',
            'PhilHealth Coverage',
            'Tax',
            'Total Amount',
            'Amount Paid',
            'Balance Due',
            'Payment Status',
            'Payment Method',
            'Payment Reference',
        ];
    }

    public function map($billing): array
    {
        return [
            $billing->invoice_number ?? '',
            $billing->patient->name ?? '',
            $billing->invoice_date ? $billing->invoice_date->format('Y-m-d') : '',
            $billing->due_date ? $billing->due_date->format('Y-m-d') : '',
            $billing->service_start_date ? $billing->service_start_date->format('Y-m-d') : '',
            $billing->service_end_date ? $billing->service_end_date->format('Y-m-d') : '',
            $billing->subtotal_amount ?? 0,
            $billing->discount_amount ?? 0,
            $billing->philhealth_coverage ?? 0,
            $billing->tax_amount ?? 0,
            $billing->total_amount ?? 0,
            $billing->amount_paid ?? 0,
            $billing->balance_due ?? 0,
            $billing->payment_status ?? '',
            $billing->payment_method ?? '',
            $billing->payment_reference ?? '',
        ];
    }
}
