@extends('admin-portal.layouts.app')

@section('title', 'Attendance Management')

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-clock me-2"></i>Attendance Management
        </h1>
        <p class="page-subtitle">Monitor and manage employee attendance records</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="admin-card p-3 text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-calendar fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $totalRecords }}</h4>
                <p class="text-muted mb-0">Total Records</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-card p-3 text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $presentDays }}</h4>
                <p class="text-muted mb-0">Present Days</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-card p-3 text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $completedDays }}</h4>
                <p class="text-muted mb-0">Completed Shifts</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-card p-3 text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-percentage fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $totalRecords > 0 ? round(($presentDays / $totalRecords) * 100, 1) : 0 }}%</h4>
                <p class="text-muted mb-0">Attendance Rate</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin-portal.attendance') }}" class="row g-3">
            <div class="col-md-3">
                <label for="employee_id" class="form-label">Employee</label>
                <select name="employee_id" id="employee_id" class="form-select">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>Incomplete
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control"
                    value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin-portal.attendance') }}" class="btn btn-outline-secondary"
                    title="Clear all filters">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title"><i class="fas fa-list me-2"></i>Attendance Records</h2>
            <p class="section-subtitle">Detailed attendance records with check-in/out times</p>
        </div>

        <div class="card">
            <div class="">
                @if ($attendanceRecords->count() > 0)
                    <div class="table-responsive" style="margin-bottom: 0px; box-shadow: none;">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendanceRecords as $record)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($record->employee && $record->employee->employeeProfile && $record->employee->employeeProfile->image_path)
                                                    <img src="{{ asset('storage/app/public/' . $record->employee->employeeProfile->image_path) }}"
                                                         alt="Profile Image" class="rounded-circle me-3"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                        style="width: 40px; height: 40px; font-weight: bold; font-size: 16px;">
                                                        {{ substr($record->employee->name ?? 'N/A', 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $record->employee->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $record->employee->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</div>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($record->date)->format('l') }}</small>
                                        </td>
                                        <td>
                                            @if ($record->check_in_time)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-sign-in-alt me-1"></i>
                                                    {{ \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') }}
                                                </span>
                                                @if ($record->image_proof_check_in)
                                                    <br>
                                                    <a href="{{ url('storage/app/public/' . $record->image_proof_check_in) }}"
                                                        target="_blank">
                                                        <img src="{{ url('storage/app/public/' . $record->image_proof_check_in) }}"
                                                            class="img-thumbnail mt-1"
                                                            style="max-width: 80px; max-height: 80px;" alt="Check-in Photo">
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($record->check_out_time)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-sign-out-alt me-1"></i>
                                                    {{ \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') }}
                                                </span>
                                                @if ($record->image_proof_check_out)
                                                    <br><a href="{{ url('storage/app/public/' . $record->image_proof_check_out) }}"
                                                        target="_blank"><img
                                                            src="{{ url('storage/app/public/' . $record->image_proof_check_out) }}"
                                                            class="img-thumbnail mt-1"
                                                            style="max-width: 80px; max-height: 80px;"
                                                            alt="Check-out Photo"></a>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($record->check_in_time && $record->check_out_time)
                                                @php
                                                    $start = \Carbon\Carbon::parse($record->check_in_time);
                                                    $end = \Carbon\Carbon::parse($record->check_out_time);
                                                    $totalSeconds = $start->diffInSeconds($end);

                                                    $hours = floor($totalSeconds / 3600);
                                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                                    $seconds = $totalSeconds % 60;
                                                @endphp

                                                <span class="badge bg-info">
                                                    {{ $hours > 0 ? $hours . 'hr' . ($hours > 1 ? 's ' : ' ') : '' }}
                                                    {{ str_pad($minutes, 2, '0', STR_PAD_LEFT) . 'min' . ($minutes != 1 ? 's ' : ' ') }}
                                                    {{ str_pad($seconds, 2, '0', STR_PAD_LEFT) . 'sec' . ($seconds != 1 ? 's' : '') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($record->check_in_time && $record->check_out_time)
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($record->check_in_time)
                                                <span class="badge bg-warning">Checked In</span>
                                            @else
                                                <span class="badge bg-secondary">Absent</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-info" title="View Details"
                                                    onclick="viewAttendanceDetails({{ $record->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                {{-- <button class="btn btn-sm btn-outline-warning" title="Edit Attendance"
                                                    onclick="editAttendance({{ $record->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button> --}}
                                                <button class="btn btn-sm btn-outline-danger" title="Delete Record"
                                                    onclick="deleteAttendance({{ $record->id }}, '{{ $record->employee->name }}', '{{ $record->date }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $attendanceRecords->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="empty-title">No Attendance Records Found</h5>
                        <p class="empty-text">No attendance records match your current filter criteria.</p>
                        <a href="{{ route('admin-portal.attendance') }}" class="btn btn-admin-primary">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Details Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attendance Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="attendanceDetails">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewAttendanceDetails(attendanceId) {
            // Fetch attendance details via AJAX and show in modal
            fetch('/admin-portal/attendance/' + attendanceId + '/details')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAttendanceModal(data.attendance);
                    } else {
                        alert('Error loading attendance details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading attendance details');
                });
        }

        function showAttendanceModal(attendance) {
            const modal = document.getElementById('attendanceModal');
            const modalBody = modal.querySelector('.modal-body');

            let imageHtml = '';
            if (attendance.image_proof_check_in) {
                imageHtml +=
                    `<p><strong>Check-in Photo:</strong><br><img src="/storage/app/public/${attendance.image_proof_check_in}" class="img-fluid" style="max-width: 200px;"></p>`;
            }
            if (attendance.image_proof_check_out) {
                imageHtml +=
                    `<p><strong>Check-out Photo:</strong><br><img src="/storage/app/public/${attendance.image_proof_check_out}" class="img-fluid" style="max-width: 200px;"></p>`;
            }

            modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Employee Information</h6>
                <p><strong>Name:</strong> ${attendance.employee.name}</p>
                <p><strong>Email:</strong> ${attendance.employee.email}</p>
                <p><strong>Date:</strong> ${new Date(attendance.date).toLocaleDateString()}</p>
            </div>
            <div class="col-md-6">
                <h6>Attendance Times</h6>
                <p><strong>Check In:</strong> ${attendance.check_in_time ? new Date(attendance.check_in_time).toLocaleTimeString() : 'Not checked in'}</p>
                <p><strong>Check Out:</strong> ${attendance.check_out_time ? new Date(attendance.check_out_time).toLocaleTimeString() : 'Not checked out'}</p>
                ${attendance.check_in_time && attendance.check_out_time ? `<p><strong>Duration:</strong> ${calculateDuration(attendance.check_in_time, attendance.check_out_time)}</p>` : ''}
            </div>
        </div>
        ${imageHtml}
    `;

            // Show modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }

        function calculateDuration(checkIn, checkOut) {
            const start = new Date(checkIn);
            const end = new Date(checkOut);
            const diffMs = end - start;
            const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            return `${diffHrs}h ${diffMins}m`;
        }

        function editAttendance(attendanceId) {
            window.location.href = '/admin-portal/attendance/' + attendanceId + '/edit';
        }

        function deleteAttendance(attendanceId, employeeName, date) {
            if (confirm(
                    `Are you sure you want to delete the attendance record for ${employeeName} on ${date}? This action cannot be undone.`
                )) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin-portal/attendance/' + attendanceId;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
