<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaboratoryReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $labResults;

    public function __construct($labResults)
    {
        $this->labResults = $labResults;
    }

    public function collection()
    {
        return $this->labResults;
    }

    public function headings(): array
    {
        return [
            'Patient Name',
            'Test Name',
            'Test Code',
            'Category',
            'Sample Type',
            'Urgent',
            'STAT',
            'Ordered Date',
            'Sample Collection Date',
            'Test Performed Date',
            'Result Available Date',
            'Result Reviewed Date',
            'Test Status',
            'Result Value',
            'Unit',
            'Reference Range',
            'Result Status',
            'Interpretation',
            'Comments',
            'QC Passed',
            'Test Cost',
            'PhilHealth Covered',
            'Ordering Provider',
            'Performing Technician',
            'Reviewing Provider',
        ];
    }

    public function map($result): array
    {
        return [
            $result->patient->name ?? '',
            $result->test_name ?? '',
            $result->test_code ?? '',
            $result->test_category ?? '',
            $result->sample_type ?? '',
            $result->urgent ? 'Yes' : 'No',
            $result->stat ? 'Yes' : 'No',
            $result->test_ordered_date_time ? $result->test_ordered_date_time->format('Y-m-d H:i') : '',
            $result->sample_collection_date_time ? $result->sample_collection_date_time->format('Y-m-d H:i') : '',
            $result->test_performed_date_time ? $result->test_performed_date_time->format('Y-m-d H:i') : '',
            $result->result_available_date_time ? $result->result_available_date_time->format('Y-m-d H:i') : '',
            $result->result_reviewed_date_time ? $result->result_reviewed_date_time->format('Y-m-d H:i') : '',
            $result->test_status ?? '',
            $result->result_value ?? '',
            $result->result_unit ?? '',
            $result->reference_range ?? '',
            $result->result_status ?? '',
            $result->interpretation ?? '',
            $result->comments ?? '',
            $result->qc_passed ? 'Yes' : 'No',
            $result->test_cost ?? 0,
            $result->covered_by_philhealth ? 'Yes' : 'No',
            $result->orderingProvider->name ?? '',
            $result->performingTechnician->name ?? '',
            $result->reviewingProvider->name ?? '',
        ];
    }
}
