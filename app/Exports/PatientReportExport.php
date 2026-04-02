<?php

namespace App\Exports;

use App\Models\PatientProfile;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatientReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $patients;

    public function __construct($patients)
    {
        $this->patients = $patients;
    }

    public function collection()
    {
        return $this->patients;
    }

    public function headings(): array
    {
        return [
            'Patient ID',
            'Name',
            'Email',
            'Phone',
            'Gender',
            'Birth Date',
            'Age',
            'Civil Status',
            'Address',
            'Blood Type',
            'PhilHealth Member',
            'PhilHealth Number',
            'Emergency Contact',
            'Emergency Phone',
            'Registration Date',
        ];
    }

    public function map($patient): array
    {
        return [
            $patient->id,
            $patient->name ?? '',
            $patient->email ?? '',
            $patient->phone ?? '',
            $patient->gender ?? '',
            $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '',
            $patient->birth_date ? $patient->birth_date->age : '',
            $patient->civil_status ?? '',
            $patient->address ?? '',
            $patient->blood_type ?? '',
            $patient->philhealth_membership === 'member' ? 'Yes' : 'No',
            $patient->philhealth_number ?? '',
            $patient->emergency_contact_name ?? '',
            $patient->emergency_contact_phone ?? '',
            $patient->created_at ? $patient->created_at->format('Y-m-d') : '',
        ];
    }
}
