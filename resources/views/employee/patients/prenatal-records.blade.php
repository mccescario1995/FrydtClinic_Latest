@extends('employee.layouts.app')

@section('title', 'Prenatal Records - ' . $patient->name)

@section('content')
<script>
// Store the current page as the last visited medical records page
sessionStorage.setItem('lastMedicalRecordsPage', window.location.href);
</script>
<div style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Prenatal Records</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('employee.patients.create-prenatal-record', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">+ Add Prenatal Record</a>
            <a href="#" onclick="goBackToMedicalRecords()" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back to Medical Records</a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Prenatal Records List -->
    @if($prenatalRecords->count() > 0)
        <div style="background: white; border-radius: 8px; border: 1px solid #ddd; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Visit Date</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Visit #</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Gestational Age</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Blood Pressure</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Provider</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prenatalRecords as $record)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 15px; border-right: 1px solid #dee2e6;">
                                    <strong style="color: #333;">{{ \Carbon\Carbon::parse($record->visit_date)->format('M d, Y') }}</strong>
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ $record->visit_number }}
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ $record->getGestationalAgeAttribute() }}
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ $record->getBloodPressureAttribute() }}
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">
                                    {{ $record->attendingPhysician->name ?? 'Unknown' }}
                                </td>
                                <td style="padding: 15px;">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('employee.patients.show-prenatal-record', [$patient->id, $record->id]) }}" style="background: #17a2b8; color: white; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; display: inline-block;">View</a>
                                        <a href="{{ route('employee.patients.edit-prenatal-record', [$patient->id, $record->id]) }}" style="background: #ffc107; color: black; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px; display: inline-block;">Edit</a>
                                        <form method="POST" action="{{ route('employee.patients.delete-prenatal-record', [$patient->id, $record->id]) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this prenatal record?')">
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
                {{ $prenatalRecords->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div style="background: white; border: 2px dashed #dee2e6; border-radius: 8px; padding: 60px 20px; text-align: center;">
            <div style="font-size: 48px; color: #dee2e6; margin-bottom: 20px;">📋</div>
            <h3 style="color: #666; margin: 0 0 10px 0;">No Prenatal Records Found</h3>
            <p style="color: #999; margin: 0 0 20px 0;">There are no prenatal records for this patient yet.</p>
            <a href="{{ route('employee.patients.create-prenatal-record', $patient->id) }}" style="background: #28a745; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; display: inline-block;">
                Add First Prenatal Record
            </a>
        </div>
    @endif

</div>

@endsection

@section('scripts')
<script>
function goBackToMedicalRecords() {
    const lastPage = sessionStorage.getItem('lastMedicalRecordsPage');
    if (lastPage && lastPage !== window.location.href) {
        window.location.href = lastPage;
    } else {
        // Fallback to medical records page
        window.location.href = "{{ route('employee.patients.medical-records', $patient->id) }}";
    }
}
</script>
@endsection
