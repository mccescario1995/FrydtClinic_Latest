@extends('employee.layouts.app')

@section('title', 'Patient Management')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Patient Management</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Manage patient records and information</p>
        </div>
        <a href="{{ route('employee.patients.create') }}" style="background: #007bff; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; display: inline-block;">
            + Add New Patient
        </a>
    </div>

    <!-- Search Form -->
    <div style="background: white; border-radius: 8px; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('employee.patients') }}" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
            <button type="submit" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px;">Search</button>
            @if(request('search'))
                <a href="{{ route('employee.patients') }}" style="color: #6c757d; text-decoration: none; padding: 10px; font-size: 14px;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Patient List -->
    @if($patients->count() > 0)
        <div style="background: white; border-radius: 8px; border: 1px solid #ddd; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Name</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Email</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Phone</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Gender</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333; border-right: 1px solid #dee2e6;">Status</th>
                            <th style="padding: 15px; text-align: left; font-weight: bold; color: #333;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr style="border-bottom: 1px solid #dee2e6; hover: background: #f8f9fa;">
                                <td style="padding: 15px; border-right: 1px solid #dee2e6;">
                                    <div>
                                        <strong style="color: #333; font-size: 16px;">{{ $patient->name }}</strong>
                                        @if($patient->patientProfile && $patient->patientProfile->birth_date)
                                            <br><small style="color: #666;">
                                                Age: {{ \Carbon\Carbon::parse($patient->patientProfile->birth_date)->age }} years
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">{{ $patient->email }}</td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">{{ $patient->patientProfile->phone ?? 'N/A' }}</td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6; color: #666;">{{ $patient->patientProfile->gender ? ucfirst($patient->patientProfile->gender) : 'N/A' }}</td>
                                <td style="padding: 15px; border-right: 1px solid #dee2e6;">
                                    <span style="background: {{ $patient->status === 'active' ? '#d4edda' : '#e2e3e5' }}; color: {{ $patient->status === 'active' ? '#155724' : '#383d41' }}; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                                        {{ ucfirst($patient->status) }}
                                    </span>
                                </td>
                                <td style="padding: 15px;">
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('employee.patients.show', $patient->id) }}" style="background: #17a2b8; color: white; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px;">View</a>
                                        <a href="{{ route('employee.patients.edit', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 6px 12px; border-radius: 3px; font-size: 12px;">Edit</a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-patient-id="{{ $patient->id }}" data-patient-name="{{ $patient->name }}" style="font-size: 12px;">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="padding: 15px; background: #f8f9fa; border-top: 1px solid #dee2e6; text-align: center;">
                {{ $patients->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div style="background: white; border: 2px dashed #dee2e6; border-radius: 8px; padding: 60px 20px; text-align: center;">
            <div style="font-size: 48px; color: #dee2e6; margin-bottom: 20px;">👥</div>
            <h3 style="color: #666; margin: 0 0 10px 0;">No Patients Found</h3>
            <p style="color: #999; margin: 0 0 20px 0;">There are no patients registered in the system yet.</p>
            <a href="{{ route('employee.patients.create') }}" style="background: #007bff; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; display: inline-block;">
                Add First Patient
            </a>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete patient <strong id="patientName"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone and will permanently remove all patient data including medical records, appointments, and billing information.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Patient</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle delete modal
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const patientId = button.getAttribute('data-patient-id');
            const patientName = button.getAttribute('data-patient-name');

            document.getElementById('patientName').textContent = patientName;
            document.getElementById('deleteForm').action = `/employee/patients/${patientId}`;
        });
    </script>
</div>
@endsection
