@extends('employee.layouts.app')

@section('title', 'Delivery Records - ' . $patient->name)

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Delivery Records</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('employee.patients.create-delivery-record', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">+ Add Delivery Record</a>
            <a href="{{ route('employee.patients.medical-records', $patient->id) }}" onclick="sessionStorage.setItem('lastMedicalRecordsTab', 'delivery');" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back</a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Delivery Records List -->
    @if($deliveryRecords->count() > 0)
        <div style="background: white; border-radius: 8px; border: 1px solid #ddd; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Delivery Date</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Delivery Type</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Provider</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Newborn Gender</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Newborn Weight</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryRecords as $record)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ \Carbon\Carbon::parse($record->delivery_date_time)->format('M d, Y H:i') }}
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ $record->delivery_type ?? 'N/A' }}
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    @if($record->attendingProvider)
                                        {{ $record->attendingProvider->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ $record->newborn_gender ?? 'N/A' }}
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    @if($record->newborn_weight)
                                        {{ $record->newborn_weight }} kg
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td style="padding: 15px;">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('employee.patients.show-delivery-record', [$patient->id, $record->id]) }}" style="background: #17a2b8; color: white; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; display: inline-block;">View</a>
                                        <a href="{{ route('employee.patients.edit-delivery-record', [$patient->id, $record->id]) }}" style="background: #ffc107; color: black; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; display: inline-block;">Edit</a>
                                        <form method="POST" action="{{ route('employee.patients.delete-delivery-record', [$patient->id, $record->id]) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this delivery record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; cursor: pointer;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="padding: 15px; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: center;">
                {{ $deliveryRecords->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div style="background: white; border: 2px dashed #dee2e6; border-radius: 8px; padding: 60px 20px; text-align: center;">
            <div style="font-size: 48px; color: #dee2e6; margin-bottom: 20px;">🏥</div>
            <h3 style="color: #666; margin: 0 0 10px 0;">No Delivery Records Found</h3>
            <p style="color: #999; margin: 0 0 20px 0;">There are no delivery records for this patient yet.</p>
            <a href="{{ route('employee.patients.create-delivery-record', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; display: inline-block;">
                Add First Delivery Record
            </a>
        </div>
    @endif
</div>
@endsection
