@extends('admin-portal.layouts.app')

@section('title', 'SMS Logs - Admin Portal')

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-sms me-2"></i>SMS Logs
    </h1>
    <p class="page-subtitle">Monitor SMS notifications and delivery status</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-primary mb-2">
                <i class="fas fa-envelope fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $totalLogs }}</h4>
            <p class="text-muted mb-0">Total SMS</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-success mb-2">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $successfulLogs }}</h4>
            <p class="text-muted mb-0">Sent</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-danger mb-2">
                <i class="fas fa-times-circle fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $failedLogs }}</h4>
            <p class="text-muted mb-0">Failed</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3 text-center">
            <div class="text-warning mb-2">
                <i class="fas fa-clock fa-2x"></i>
            </div>
            <h4 class="mb-1">{{ $pendingLogs }}</h4>
            <p class="text-muted mb-0">Pending</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <form method="GET" action="{{ route('admin-portal.sms-logs') }}" class="row g-3">
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Status</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="sms_type" class="form-label">Type</label>
            <select name="sms_type" id="sms_type" class="form-select">
                <option value="">All Types</option>
                <option value="appointment" {{ request('sms_type') === 'appointment' ? 'selected' : '' }}>Appointment</option>
                <option value="appointment_booking" {{ request('sms_type') === 'appointment_booking' ? 'selected' : '' }}>Appointment Booking</option>
                <option value="reminder" {{ request('sms_type') === 'reminder' ? 'selected' : '' }}>Reminder</option>
                <option value="payment" {{ request('sms_type') === 'payment' ? 'selected' : '' }}>Payment</option>
                <option value="lab_result" {{ request('sms_type') === 'lab_result' ? 'selected' : '' }}>Lab Result</option>
                <option value="test" {{ request('sms_type') === 'test' ? 'selected' : '' }}>Test</option>
                <option value="notification" {{ request('sms_type') === 'notification' ? 'selected' : '' }}>Notification</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" name="phone" id="phone" class="form-control"
                   value="{{ request('phone') }}" placeholder="Search by phone...">
        </div>
        <div class="col-md-2">
            <label for="date_from" class="form-label">From Date</label>
            <input type="date" name="date_from" id="date_from" class="form-control"
                   value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label">To Date</label>
            <input type="date" name="date_to" id="date_to" class="form-control"
                   value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- SMS Logs Table -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-list me-2"></i>SMS Records</h2>
        <p class="section-subtitle">Detailed SMS delivery logs and status</p>
    </div>

    <div class="admin-card">
        <div class="card-body">
            @if($smsLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table admin-table table-hover">
                        <thead>
                            <tr>
                                <th>Phone Number</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Recipient</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($smsLogs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->phone_number }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $typeLabel = ucwords(str_replace('_', ' ', $log->sms_type ?? 'general'));
                                            $badgeClass = match($log->sms_type) {
                                                'appointment', 'appointment_booking' => 'bg-primary',
                                                'reminder' => 'bg-info',
                                                'payment' => 'bg-success',
                                                'lab_result' => 'bg-warning',
                                                'test' => 'bg-secondary',
                                                'notification' => 'bg-dark',
                                                default => 'bg-light text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                                    </td>
                                    <td>
                                        @switch($log->status)
                                            @case('sent')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Sent
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Failed
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($log->sent_at)
                                            <div class="text-success fw-bold">
                                                {{ \Carbon\Carbon::parse($log->sent_at)->format('M d, Y H:i') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($log->sent_at)->diffForHumans() }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                     style="width: 30px; height: 30px; font-weight: bold; font-size: 12px;">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $log->user->name }}</div>
                                                    <small class="text-muted">{{ $log->user->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info"
                                                title="View Details"
                                                onclick="viewSmsDetails({{ $log->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger ms-1"
                                                title="Delete SMS Log"
                                                onclick="deleteSmsLog({{ $log->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $smsLogs->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-sms"></i>
                    </div>
                    <h5 class="empty-title">No SMS Logs Found</h5>
                    <p class="empty-text">No SMS records match your current filter criteria.</p>
                    <a href="{{ route('admin-portal.sms-logs') }}" class="btn btn-admin-primary">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SMS Details Modal -->
<div class="modal fade" id="smsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SMS Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="smsDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewSmsDetails(smsLogId) {
    // Fetch SMS details via AJAX and show in modal
    fetch('/admin-portal/sms-logs/' + smsLogId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSmsModal(data.smsLog);
            } else {
                alert('Error loading SMS details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading SMS details');
        });
}

function showSmsModal(smsLog) {
    const modal = document.getElementById('smsModal');
    const modalBody = modal.querySelector('.modal-body');

    let statusBadge = '';
    switch(smsLog.status) {
        case 'sent':
            statusBadge = '<span class="badge bg-success">Sent</span>';
            break;
        case 'failed':
            statusBadge = '<span class="badge bg-danger">Failed</span>';
            break;
        case 'pending':
            statusBadge = '<span class="badge bg-warning">Pending</span>';
            break;
    }

    let metadataHtml = '';
    if (smsLog.metadata) {
        const metadata = typeof smsLog.metadata === 'string' ? JSON.parse(smsLog.metadata) : smsLog.metadata;
        metadataHtml = '<h6>Metadata:</h6><pre class="bg-light p-2 rounded">' + JSON.stringify(metadata, null, 2) + '</pre>';
    }

    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>SMS Information</h6>
                <p><strong>Phone Number:</strong> ${smsLog.phone_number}</p>
                <p><strong>Type:</strong> ${smsLog.sms_type ? smsLog.sms_type.charAt(0).toUpperCase() + smsLog.sms_type.slice(1) : 'General'}</p>
                <p><strong>Status:</strong> ${statusBadge}</p>
                <p><strong>Created:</strong> ${new Date(smsLog.created_at).toLocaleString()}</p>
                ${smsLog.sent_at ? `<p><strong>Sent At:</strong> ${new Date(smsLog.sent_at).toLocaleString()}</p>` : ''}
                ${smsLog.twilio_sid ? `<p><strong>Twilio SID:</strong> <code>${smsLog.twilio_sid}</code></p>` : ''}
            </div>
            <div class="col-md-6">
                <h6>Recipient Information</h6>
                ${smsLog.user ? `
                    <p><strong>Name:</strong> ${smsLog.user.name}</p>
                    <p><strong>Email:</strong> ${smsLog.user.email}</p>
                    <p><strong>User Type:</strong> ${smsLog.user.user_type}</p>
                ` : '<p class="text-muted">No user information available</p>'}
                ${smsLog.sender ? `
                    <p><strong>Sent By:</strong> ${smsLog.sender.name}</p>
                ` : ''}
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Message Content:</h6>
                <div class="bg-light p-3 rounded border">
                    ${smsLog.message.replace(/\n/g, '<br>')}
                </div>
            </div>
        </div>
        ${smsLog.error_message ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="text-danger">Error Message:</h6>
                    <div class="bg-danger text-white p-3 rounded">
                        ${smsLog.error_message}
                    </div>
                </div>
            </div>
        ` : ''}
        ${metadataHtml ? `
            <div class="row mt-3">
                <div class="col-12">
                    ${metadataHtml}
                </div>
            </div>
        ` : ''}
    `;

    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

function deleteSmsLog(smsLogId) {
    if (confirm('Are you sure you want to delete this SMS log? This action cannot be undone.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin-portal/sms-logs/' + smsLogId;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
