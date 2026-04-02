@extends('admin-portal.layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-cogs me-2"></i>System Settings</h1>
                <p class="text-muted mb-0">Configure system preferences and settings</p>
            </div>
        </div>
    </div>
</div>

<!-- Settings Sections -->
<div class="row">
    <!-- General Settings -->
    <div class="col-lg-8 mb-4">
        <div class="admin-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>General Settings</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" value="Clinic Management System">
                        </div>
                        <div class="col-md-6">
                            <label for="site_email" class="form-label">System Email</label>
                            <input type="email" class="form-control" id="site_email" value="admin@clinic.com">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone">
                                <option value="Asia/Manila" selected>Asia/Manila (UTC+8)</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="language" class="form-label">Default Language</label>
                            <select class="form-select" id="language">
                                <option value="en" selected>English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="site_description" class="form-label">Site Description</label>
                        <textarea class="form-control" id="site_description" rows="3">Comprehensive clinic management system for healthcare providers</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save General Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Appointment Settings -->
        <div class="admin-card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Appointment Settings</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="default_duration" class="form-label">Default Appointment Duration (minutes)</label>
                            <input type="number" class="form-control" id="default_duration" value="30" min="15" max="480">
                        </div>
                        <div class="col-md-6">
                            <label for="advance_booking" class="form-label">Maximum Advance Booking (days)</label>
                            <input type="number" class="form-control" id="advance_booking" value="90" min="1" max="365">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="business_hours_start" class="form-label">Business Hours Start</label>
                            <input type="time" class="form-control" id="business_hours_start" value="08:00">
                        </div>
                        <div class="col-md-6">
                            <label for="business_hours_end" class="form-label">Business Hours End</label>
                            <input type="time" class="form-control" id="business_hours_end" value="17:00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allow_weekends" checked>
                            <label class="form-check-label" for="allow_weekends">
                                Allow appointments on weekends
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="require_confirmation" checked>
                            <label class="form-check-label" for="require_confirmation">
                                Require appointment confirmation
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Save Appointment Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- SMS Settings -->
        <div class="admin-card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-sms me-2"></i>SMS Settings (iProgSMS)</h5>
            </div>
            <div class="card-body">
                <!-- SMS Credits Display -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info" id="sms-credits-alert">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>SMS Credits:</strong>
                                    <span id="sms-credits-display">
                                        @if(\App\Models\Setting::get('iprogsms_token'))
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Loading credits...
                                        @else
                                            <span class="text-muted">Configure API token to check credits</span>
                                        @endif
                                    </span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkSmsCredits()" id="check-credits-btn">
                                    <i class="fas fa-sync-alt me-1"></i>Check Credits
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin-portal.update-sms-settings') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="iprogsms_token" class="form-label">iProgSMS API Token</label>
                            <input type="password" class="form-control" id="iprogsms_token" name="iprogsms_token"
                                   value="{{ \App\Models\Setting::get('iprogsms_token') }}" placeholder="Enter your iProgSMS API token">
                            <div class="form-text">Your iProgSMS API token</div>
                        </div>
                        <div class="col-md-6">
                            <label for="iprogsms_url" class="form-label">API URL</label>
                            <input type="url" class="form-control" id="iprogsms_url" name="iprogsms_url"
                                   value="{{ \App\Models\Setting::get('iprogsms_url', 'https://www.iprogsms.com/api/v1/sms_messages') }}" placeholder="https://www.iprogsms.com/api/v1/sms_messages">
                            <div class="form-text">iProgSMS API endpoint URL</div>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="admin_sms_number" class="form-label">Admin SMS Number</label>
                            <input type="text" class="form-control" id="admin_sms_number" name="admin_sms_number"
                                   value="{{ \App\Models\Setting::get('admin_sms_number') }}" placeholder="9123456789">
                            <div class="form-text">Phone number to receive employee attendance notifications</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sms_enabled" name="sms_enabled" value="1"
                                       {{ \App\Models\Setting::get('sms_enabled', false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sms_enabled">
                                    Enable SMS notifications
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>SMS Notification Types:</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sms_appointments" name="sms_appointments" value="1" checked>
                            <label class="form-check-label" for="sms_appointments">
                                Appointment confirmations and reminders
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sms_payments" name="sms_payments" value="1" checked>
                            <label class="form-check-label" for="sms_payments">
                                Payment confirmations
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sms_lab_results" name="sms_lab_results" value="1" checked>
                            <label class="form-check-label" for="sms_lab_results">
                                Lab results notifications
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-1"></i>Save SMS Settings
                    </button>
                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="testSms()">
                        <i class="fas fa-paper-plane me-1"></i>Test SMS
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- System Information -->
        <div class="admin-card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Version:</strong><br>
                    <span class="text-muted">Clinic Management System v1.0.0</span>
                </div>
                <div class="mb-3">
                    <strong>PHP Version:</strong><br>
                    <span class="text-muted">{{ PHP_VERSION }}</span>
                </div>
                <div class="mb-3">
                    <strong>Laravel Version:</strong><br>
                    <span class="text-muted">{{ app()->version() }}</span>
                </div>
                <div class="mb-3">
                    <strong>Database:</strong><br>
                    <span class="text-muted">{{ config('database.default') }}</span>
                </div>
                <div class="mb-3">
                    <strong>Environment:</strong><br>
                    <span class="badge bg-{{ app()->environment() === 'production' ? 'danger' : 'warning' }}">
                        {{ app()->environment() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        {{-- <div class="admin-card mb-4">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="clearCache()">
                        <i class="fas fa-trash me-1"></i>Clear Cache
                    </button>
                    <button class="btn btn-outline-success" onclick="backupDatabase()">
                        <i class="fas fa-download me-1"></i>Backup Database
                    </button>
                    <button class="btn btn-outline-info" onclick="viewLogs()">
                        <i class="fas fa-file-alt me-1"></i>View System Logs
                    </button>
                    <button class="btn btn-outline-danger" onclick="maintenanceMode()">
                        <i class="fas fa-tools me-1"></i>Maintenance Mode
                    </button>
                </div>
            </div>
        </div> --}}

        <!-- Recent Activity -->
        {{-- <div class="admin-card">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <small class="text-muted">2 hours ago</small>
                            <div>System backup completed</div>
                        </div>
                    </div>
                    <div class="timeline-item mb-3">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <small class="text-muted">1 day ago</small>
                            <div>New user registered</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <small class="text-muted">2 days ago</small>
                            <div>Appointment settings updated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-left: 15px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.9rem;
}

.timeline-content small {
    display: block;
    margin-bottom: 2px;
}
</style>

<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the system cache?')) {
        alert('Cache cleared successfully!');
    }
}

function backupDatabase() {
    if (confirm('Are you sure you want to create a database backup?')) {
        alert('Database backup created successfully!');
    }
}

function viewLogs() {
    window.open('/admin-portal/logs', '_blank');
}

function maintenanceMode() {
    if (confirm('Are you sure you want to toggle maintenance mode?')) {
        alert('Maintenance mode toggled!');
    }
}

function checkSmsCredits() {
    const creditsDisplay = document.getElementById('sms-credits-display');
    const checkBtn = document.getElementById('check-credits-btn');
    const alertDiv = document.getElementById('sms-credits-alert');

    // Show loading
    const originalText = checkBtn.innerHTML;
    checkBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Checking...';
    checkBtn.disabled = true;
    creditsDisplay.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking credits...';

    fetch('/admin-portal/settings/check-sms-credits', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            creditsDisplay.innerHTML = `<strong class="text-success">${data.credits}</strong> credits remaining`;
            alertDiv.className = 'alert alert-success';
        } else {
            creditsDisplay.innerHTML = '<span class="text-danger">Unable to check credits</span>';
            alertDiv.className = 'alert alert-warning';
            alert('Failed to check SMS credits: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        creditsDisplay.innerHTML = '<span class="text-danger">Error checking credits</span>';
        alertDiv.className = 'alert alert-danger';
        alert('An error occurred while checking SMS credits.');
    })
    .finally(() => {
        // Restore button
        checkBtn.innerHTML = originalText;
        checkBtn.disabled = false;
    });
}

function testSms() {
    const phone = prompt('Enter a phone number to test SMS (with country code, e.g., +639123456789):');
    if (!phone) return;

    // Show loading
    const testBtn = document.querySelector('button[onclick="testSms()"]');
    const originalText = testBtn.innerHTML;
    testBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Testing...';
    testBtn.disabled = true;

    fetch('/admin-portal/settings/test-sms', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ phone: phone })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test SMS sent successfully!');
        } else {
            alert('Failed to send test SMS: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while testing SMS.');
    })
    .finally(() => {
        // Restore button
        testBtn.innerHTML = originalText;
        testBtn.disabled = false;
    });
}

// Auto-check credits on page load if token is configured
document.addEventListener('DOMContentLoaded', function() {
    const tokenInput = document.getElementById('iprogsms_token');
    if (tokenInput && tokenInput.value.trim()) {
        checkSmsCredits();
    }
});
</script>
@endsection
