@extends('admin-portal.layouts.app')

@section('title', 'Manage Employee Deductions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2"></i>Manage Deductions</h1>
                <p class="text-muted mb-0">Managing mandatory deductions for {{ $employee->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.employee-deductions') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back to Employees
                </a>
                <button type="button" class="btn btn-admin-primary" id="editDeductionsBtn">
                    <i class="fas fa-edit me-1"></i>Edit Deductions
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Employee Info Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if ($employee->employeeProfile && $employee->employeeProfile->image_path)
                            <img src="{{ asset('storage/app/public/' . $employee->employeeProfile->image_path) }}"
                                alt="Profile Image" class="rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                style="width: 80px; height: 80px; font-weight: bold; font-size: 32px;">
                                {{ substr($employee->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-1">{{ $employee->name }}</h5>
                        <p class="text-muted mb-1">{{ $employee->email }}</p>
                        @if ($employee->employeeProfile && $employee->employeeProfile->position)
                            <small class="text-primary">{{ $employee->employeeProfile->position }}</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h4 mb-0 text-primary">{{ $employeeDeductions->where('is_enabled', true)->count() }}</div>
                                <small class="text-muted">Active Deductions</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-0 text-success">
                                    @php
                                        $totalDeduction = 0;
                                        $baseSalary = $employee->employeeProfile ? $employee->employeeProfile->hourly_rate * 160 : 0; // Assuming 160 hours/month
                                        foreach ($employeeDeductions->where('is_enabled', true) as $empDeduction) {
                                            $totalDeduction += $empDeduction->calculateDeduction($baseSalary);
                                        }
                                        echo '₱' . number_format($totalDeduction, 2);
                                    @endphp
                                </div>
                                <small class="text-muted">Monthly Deduction</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-0 text-info">{{ $mandatoryDeductions->count() }}</div>
                                <small class="text-muted">Available Deductions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Mode -->
<div id="viewMode" class="row">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Mandatory Deductions</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportDeductions()">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="viewDeductionHistory()">
                        <i class="fas fa-history me-1"></i>History
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($mandatoryDeductions as $deduction)
                        @php
                            $employeeDeduction = $employeeDeductions->where('deduction_id', $deduction->id)->first();
                            $isEnabled = $employeeDeduction && $employeeDeduction->is_enabled;
                            $effectiveRate = $employeeDeduction ? $employeeDeduction->getEffectivePercentageRate() : $deduction->percentage_rate;
                            $effectiveAmount = $employeeDeduction ? $employeeDeduction->getEffectiveFixedAmount() : $deduction->fixed_amount;
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 {{ $isEnabled ? 'border-success shadow-sm' : 'border-secondary' }} transition-all hover-lift">
                                <div class="card-header d-flex justify-content-between align-items-center {{ $isEnabled ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                                    <h6 class="mb-0">{{ $deduction->name }}</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $isEnabled ? 'bg-light text-success' : 'bg-light text-secondary' }}">
                                            {{ $isEnabled ? 'Active' : 'Inactive' }}
                                        </span>
                                        @if($employeeDeduction && ($employeeDeduction->custom_percentage_rate !== null || $employeeDeduction->custom_fixed_amount !== null))
                                            <span class="badge bg-warning text-dark" title="Custom settings applied">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">{{ $deduction->description }}</p>
                                    @if ($isEnabled)
                                        <div class="mb-3">
                                            @if ($effectiveRate > 0)
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="small text-muted">Rate:</span>
                                                    <span class="small fw-bold">{{ number_format($effectiveRate, 2) }}%</span>
                                                </div>
                                            @endif
                                            @if ($effectiveAmount > 0)
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="small text-muted">Amount:</span>
                                                    <span class="small fw-bold">₱{{ number_format($effectiveAmount, 2) }}</span>
                                                </div>
                                            @endif
                                            @if ($employeeDeduction && ($employeeDeduction->custom_percentage_rate !== null || $employeeDeduction->custom_fixed_amount !== null))
                                                <div class="alert alert-warning alert-sm py-2 px-3 mb-0">
                                                    <small><i class="fas fa-exclamation-triangle me-1"></i>Custom settings applied</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-center">
                                            @php
                                                $sampleDeduction = $employeeDeduction ? $employeeDeduction->calculateDeduction($baseSalary) : 0;
                                            @endphp
                                            <div class="h6 mb-0 text-success">₱{{ number_format($sampleDeduction, 2) }}</div>
                                            <small class="text-muted">Monthly Impact</small>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-ban fa-2x mb-2"></i>
                                            <div class="small">Deduction disabled</div>
                                        </div>
                                    @endif
                                </div>
                                @if($isEnabled && $employeeDeduction && $employeeDeduction->notes)
                                    <div class="card-footer bg-light">
                                        <small class="text-muted">
                                            <i class="fas fa-sticky-note me-1"></i>{{ Str::limit($employeeDeduction->notes, 50) }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="empty-title">No Mandatory Deductions</div>
                                <div class="empty-text">
                                    There are no mandatory deductions configured in the system.
                                </div>
                                <a href="{{ route('admin-portal.mandatory-deductions.create') }}" class="btn btn-admin-primary">
                                    <i class="fas fa-plus me-1"></i>Create First Deduction
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Mode -->
<div id="editMode" class="row" style="display: none;">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Employee Deductions</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-info" id="previewChangesBtn">
                        <i class="fas fa-eye me-1"></i>Preview Changes
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="cancelEditBtn">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin-portal.employee-deductions.update-employee', $employee->id) }}" id="editDeductionsForm">
                    @csrf
                    @method('PUT')

                    <!-- Quick Setup Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-magic me-2"></i>Quick Setup</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3">Apply bulk settings to all deductions:</p>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-outline-success" id="enableAllBtn">
                                                    <i class="fas fa-check me-1"></i>Enable All (Default Rates)
                                                </button>
                                                <button type="button" class="btn btn-outline-warning" id="enableAllCustomBtn">
                                                    <i class="fas fa-star me-1"></i>Enable All (Custom)
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" id="disableAllBtn">
                                                    <i class="fas fa-ban me-1"></i>Disable All
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <small class="text-muted">Use individual controls below for custom settings</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">Configure Deductions for {{ $employee->name }}</h6>
                            <div class="row">
                                @foreach($mandatoryDeductions as $deduction)
                                    @php
                                        $employeeDeduction = $employeeDeductions->where('deduction_id', $deduction->id)->first();
                                        $isEnabled = $employeeDeduction && $employeeDeduction->is_enabled;
                                        $customRate = $employeeDeduction ? $employeeDeduction->custom_percentage_rate : null;
                                        $customAmount = $employeeDeduction ? $employeeDeduction->custom_fixed_amount : null;
                                        $notes = $employeeDeduction ? $employeeDeduction->notes : null;
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-2 {{ $isEnabled ? 'border-success' : 'border-secondary' }} transition-all">
                                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                                <h6 class="mb-0 text-dark">{{ $deduction->name }}</h6>
                                                <div class="form-check form-switch">
                                                    <input class=" form-check-input deduction-checkbox" type="checkbox"
                                                        id="edit_deduction_{{ $deduction->id }}"
                                                        name="deductions[{{ $deduction->id }}][enabled]"
                                                        value="1" {{ $isEnabled ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="edit_deduction_{{ $deduction->id }}">
                                                        {{ $isEnabled ? 'Enabled' : 'Disabled' }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body deduction-settings" id="edit_deduction_settings_{{ $deduction->id }}" style="{{ $isEnabled ? '' : 'display: none;' }}">
                                                <p class="text-muted small mb-3">{{ $deduction->description }}</p>
                                                <input type="hidden" name="deductions[{{ $deduction->id }}][deduction_id]" value="{{ $deduction->id }}">

                                                <div class="row">
                                                    @if ($deduction->percentage_rate > 0)
                                                        <div class="col-12 mb-3">
                                                            <label for="edit_rate_{{ $deduction->id }}" class="form-label">
                                                                Percentage Rate (%)
                                                                <small class="text-muted">(Default: {{ number_format($deduction->percentage_rate, 2) }}%)</small>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="number" class="form-control" id="edit_rate_{{ $deduction->id }}"
                                                                    name="deductions[{{ $deduction->id }}][custom_percentage_rate]"
                                                                    value="{{ $customRate ?? '' }}" step="0.01" min="0" max="100"
                                                                    placeholder="{{ number_format($deduction->percentage_rate, 2) }}">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($deduction->fixed_amount > 0)
                                                        <div class="col-12 mb-3">
                                                            <label for="edit_amount_{{ $deduction->id }}" class="form-label">
                                                                Fixed Amount (₱)
                                                                <small class="text-muted">(Default: ₱{{ number_format($deduction->fixed_amount, 2) }})</small>
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">₱</span>
                                                                <input type="number" class="form-control" id="edit_amount_{{ $deduction->id }}"
                                                                    name="deductions[{{ $deduction->id }}][custom_fixed_amount]"
                                                                    value="{{ $customAmount ?? '' }}" step="0.01" min="0"
                                                                    placeholder="{{ number_format($deduction->fixed_amount, 2) }}">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="col-12 mb-3">
                                                        <label for="edit_notes_{{ $deduction->id }}" class="form-label">Notes</label>
                                                        <textarea class="form-control" id="edit_notes_{{ $deduction->id }}"
                                                            name="deductions[{{ $deduction->id }}][notes]" rows="2"
                                                            placeholder="Optional notes for this employee's deduction">{{ $notes }}</textarea>
                                                    </div>
                                                </div>

                                                <!-- Preview Impact -->
                                                <div class="alert alert-info py-2 mb-0">
                                                    <small>
                                                        <i class="fas fa-calculator me-1"></i>
                                                        <span id="impact_{{ $deduction->id }}">Impact will be calculated based on settings</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Only enabled deductions will be applied to this employee's payroll.
                                Leave custom rate/amount fields empty to use the default values.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-outline-secondary" id="formCancelBtn">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" id="saveDraftBtn">
                                <i class="fas fa-save me-1"></i>Save Draft
                            </button>
                            <button type="submit" class="btn btn-admin-primary">
                                <i class="fas fa-check me-1"></i>Update Employee Deductions
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Changes Preview Modal -->
<div class="modal fade" id="changesPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Preview Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="changesPreviewContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.transition-all {
    transition: all 0.3s ease;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.btn-group .btn {
    border-radius: 0.375rem;
}

.deduction-checkbox:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}
</style>
@endsection

@section('scripts')
<script>
let originalDeductions = {};
let currentDeductions = {};

// Store original state
document.addEventListener('DOMContentLoaded', function() {
    // Store original values
    document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
        const deductionId = checkbox.id.split('_')[2];
        originalDeductions[deductionId] = {
            enabled: checkbox.checked,
            rate: document.getElementById('edit_rate_' + deductionId)?.value || '',
            amount: document.getElementById('edit_amount_' + deductionId)?.value || '',
            notes: document.getElementById('edit_notes_' + deductionId)?.value || ''
        };
    });
});

function toggleEditMode() {
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');
    const editBtn = document.getElementById('editDeductionsBtn');

    if (viewMode.style.display === 'none') {
        // Switching back to view mode
        if (hasUnsavedChanges()) {
            if (!confirm('You have unsaved changes. Are you sure you want to cancel?')) {
                return;
            }
        }
        viewMode.style.display = 'block';
        editMode.style.display = 'none';
        editBtn.innerHTML = '<i class="fas fa-edit me-1"></i>Edit Deductions';
        resetToOriginal();
    } else {
        // Switching to edit mode
        viewMode.style.display = 'none';
        editMode.style.display = 'block';
        editBtn.innerHTML = '<i class="fas fa-eye me-1"></i>View Deductions';
        calculateAllImpacts();
    }
}

function hasUnsavedChanges() {
    return JSON.stringify(originalDeductions) !== JSON.stringify(getCurrentDeductions());
}

function getCurrentDeductions() {
    const current = {};
    document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
        const deductionId = checkbox.id.split('_')[2];
        current[deductionId] = {
            enabled: checkbox.checked,
            rate: document.getElementById('edit_rate_' + deductionId)?.value || '',
            amount: document.getElementById('edit_amount_' + deductionId)?.value || '',
            notes: document.getElementById('edit_notes_' + deductionId)?.value || ''
        };
    });
    return current;
}

function resetToOriginal() {
    Object.keys(originalDeductions).forEach(function(deductionId) {
        const original = originalDeductions[deductionId];
        const checkbox = document.getElementById('edit_deduction_' + deductionId);
        const rateInput = document.getElementById('edit_rate_' + deductionId);
        const amountInput = document.getElementById('edit_amount_' + deductionId);
        const notesInput = document.getElementById('edit_notes_' + deductionId);
        const settingsDiv = document.getElementById('edit_deduction_settings_' + deductionId);

        if (checkbox) checkbox.checked = original.enabled;
        if (rateInput) rateInput.value = original.rate;
        if (amountInput) amountInput.value = original.amount;
        if (notesInput) notesInput.value = original.notes;
        if (settingsDiv) settingsDiv.style.display = original.enabled ? 'block' : 'none';
    });
}

function calculateImpact(deductionId) {
    const checkbox = document.getElementById('edit_deduction_' + deductionId);
    const rateInput = document.getElementById('edit_rate_' + deductionId);
    const amountInput = document.getElementById('edit_amount_' + deductionId);
    const impactSpan = document.getElementById('impact_' + deductionId);

    if (!checkbox || !impactSpan) return;

    if (!checkbox.checked) {
        impactSpan.textContent = 'Disabled - No impact';
        return;
    }

    // Sample calculation (you may want to make this more sophisticated)
    const rate = parseFloat(rateInput?.value) || 0;
    const amount = parseFloat(amountInput?.value) || 0;
    const baseSalary = {{ $employee->employeeProfile ? $employee->employeeProfile->hourly_rate * 160 : 0 }};

    let impact = 0;
    if (rate > 0) {
        impact += (baseSalary * rate) / 100;
    }
    if (amount > 0) {
        impact += amount;
    }

    impactSpan.textContent = `Monthly impact: ₱${impact.toFixed(2)}`;
}

function calculateAllImpacts() {
    document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
        const deductionId = checkbox.id.split('_')[2];
        calculateImpact(deductionId);
    });
}

function showChangesPreview() {
    const current = getCurrentDeductions();
    const changes = [];

    Object.keys(current).forEach(function(deductionId) {
        const currentData = current[deductionId];
        const originalData = originalDeductions[deductionId];

        if (JSON.stringify(currentData) !== JSON.stringify(originalData)) {
            const deductionName = document.querySelector(`#edit_deduction_${deductionId}`).closest('.card').querySelector('.card-header h6').textContent.trim();
            changes.push({
                name: deductionName,
                original: originalData,
                current: currentData
            });
        }
    });

    const modalContent = document.getElementById('changesPreviewContent');
    if (changes.length === 0) {
        modalContent.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-check-circle fa-3x mb-3"></div><p>No changes detected.</p></div>';
    } else {
        let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Deduction</th><th>Status</th><th>Rate</th><th>Amount</th><th>Notes</th></tr></thead><tbody>';

        changes.forEach(function(change) {
            html += `<tr class="${JSON.stringify(change.current) !== JSON.stringify(change.original) ? 'table-warning' : ''}">
                <td><strong>${change.name}</strong></td>
                <td>${change.current.enabled ? 'Enabled' : 'Disabled'}</td>
                <td>${change.current.rate || 'Default'}</td>
                <td>${change.current.amount ? '₱' + parseFloat(change.current.amount).toFixed(2) : 'Default'}</td>
                <td>${change.current.notes || 'None'}</td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        modalContent.innerHTML = html;
    }

    new bootstrap.Modal(document.getElementById('changesPreviewModal')).show();
}

function exportDeductions() {
        // Collect deduction data from the DOM
    const deductions = [];
    document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
        const deductionId = checkbox.id.split('_')[2];
        const card = checkbox.closest('.card');
        const name = card.querySelector('h6').textContent.trim();
        const description = card.querySelector('.card-body p').textContent.trim();
        const enabled = checkbox.checked;
        
        // Get rate and amount from the edit mode inputs
        let percentageRate = 0;
        let fixedAmount = 0;
        
        const rateInput = document.getElementById('edit_rate_' + deductionId);
        const amountInput = document.getElementById('edit_amount_' + deductionId);
        
        if (enabled) {
            if (rateInput && rateInput.value) {
                percentageRate = parseFloat(rateInput.value);
            }
            if (amountInput && amountInput.value) {
                fixedAmount = parseFloat(amountInput.value);
            }
        }
        
        deductions.push({
            name: name,
            description: description,
            enabled: enabled,
            percentage_rate: percentageRate,
            fixed_amount: fixedAmount,
            notes: ''
        });
    });

    const csvContent = "data:text/csv;charset=utf-8,"
        + "Deduction Name,Description,Enabled,Percentage Rate,Fixed Amount,Notes\n"
        + deductions.map(d => `"${d.name}","${d.description}",${d.enabled},${d.percentage_rate},${d.fixed_amount},"${d.notes}"`).join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "{{ $employee->name }}_deductions_{{ date('Y-m-d') }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function viewDeductionHistory() {
    alert('Deduction history feature coming soon!');
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Edit mode toggle
    const editBtn = document.getElementById('editDeductionsBtn');
    if (editBtn) {
        editBtn.addEventListener('click', toggleEditMode);
    }

    // Cancel buttons
    const cancelBtn = document.getElementById('cancelEditBtn');
    const formCancelBtn = document.getElementById('formCancelBtn');
    if (cancelBtn) cancelBtn.addEventListener('click', toggleEditMode);
    if (formCancelBtn) formCancelBtn.addEventListener('click', toggleEditMode);

    // Preview changes
    const previewBtn = document.getElementById('previewChangesBtn');
    if (previewBtn) previewBtn.addEventListener('click', showChangesPreview);

    // Deduction checkbox changes
    document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const deductionId = this.id.split('_')[2];
            const settingsDiv = document.getElementById('edit_deduction_settings_' + deductionId);
            const label = this.nextElementSibling;

            if (this.checked) {
                settingsDiv.style.display = 'block';
                label.textContent = 'Enabled';
                this.closest('.card').classList.remove('border-secondary');
                this.closest('.card').classList.add('border-success');
            } else {
                settingsDiv.style.display = 'none';
                label.textContent = 'Disabled';
                this.closest('.card').classList.remove('border-success');
                this.closest('.card').classList.add('border-secondary');
                // Clear inputs when disabled
                const inputs = settingsDiv.querySelectorAll('input, textarea');
                inputs.forEach(function(input) {
                    if (input.type !== 'hidden') input.value = '';
                });
            }

            calculateImpact(deductionId);
        });

        // Input changes for real-time calculation
        const rateInput = document.getElementById('edit_rate_' + checkbox.id.split('_')[2]);
        const amountInput = document.getElementById('edit_amount_' + checkbox.id.split('_')[2]);

        if (rateInput) rateInput.addEventListener('input', () => calculateImpact(checkbox.id.split('_')[2]));
        if (amountInput) amountInput.addEventListener('input', () => calculateImpact(checkbox.id.split('_')[2]));
    });

    // Quick setup buttons
    const enableAllBtn = document.getElementById('enableAllBtn');
    const enableAllCustomBtn = document.getElementById('enableAllCustomBtn');
    const disableAllBtn = document.getElementById('disableAllBtn');

    if (enableAllBtn) {
        enableAllBtn.addEventListener('click', function() {
            document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
            showNotification('All deductions enabled with default settings.', 'success');
        });
    }

    if (enableAllCustomBtn) {
        enableAllCustomBtn.addEventListener('click', function() {
            document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));

                const deductionId = checkbox.id.split('_')[2];
                const rateInput = document.getElementById('edit_rate_' + deductionId);
                const amountInput = document.getElementById('edit_amount_' + deductionId);

                // Example: Set custom rates (you may want to prompt for these values)
                if (rateInput) rateInput.value = '5.0';
                if (amountInput) amountInput.value = '500.00';

                calculateImpact(deductionId);
            });
            showNotification('All deductions enabled with custom settings.', 'success');
        });
    }

    if (disableAllBtn) {
        disableAllBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to disable all deductions for this employee?')) {
                document.querySelectorAll('.deduction-checkbox').forEach(function(checkbox) {
                    checkbox.checked = false;
                    checkbox.dispatchEvent(new Event('change'));
                });
                showNotification('All deductions disabled.', 'info');
            }
        });
    }

    // Save draft functionality
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            const formData = new FormData(document.getElementById('editDeductionsForm'));
            formData.append('draft', '1');

            // Store draft in localStorage
            localStorage.setItem('deductions_draft_{{ $employee->id }}', JSON.stringify(Object.fromEntries(formData)));
            showNotification('Draft saved locally.', 'info');
        });
    }

    // Load draft if exists
    const draft = localStorage.getItem('deductions_draft_{{ $employee->id }}');
    if (draft) {
        const draftData = JSON.parse(draft);
        Object.keys(draftData).forEach(function(key) {
            if (key.includes('deductions')) {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) input.value = draftData[key];
            }
        });
        showNotification('Draft loaded. Review and save when ready.', 'warning');
    }

    // Form validation and submission
    const form = document.getElementById('editDeductionsForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            let errorMessages = [];

            document.querySelectorAll('.deduction-checkbox:checked').forEach(function(checkbox) {
                const deductionId = checkbox.id.split('_')[2];
                const rateInput = document.getElementById('edit_rate_' + deductionId);
                const amountInput = document.getElementById('edit_amount_' + deductionId);

                if (rateInput && amountInput) {
                    const rateValue = parseFloat(rateInput.value);
                    const amountValue = parseFloat(amountInput.value);

                    if ((isNaN(rateValue) || rateValue <= 0) && (isNaN(amountValue) || amountValue <= 0)) {
                        const deductionName = checkbox.closest('.card').querySelector('.card-header h6').textContent.trim();
                        hasErrors = true;
                        errorMessages.push(`${deductionName}: At least one of percentage rate or fixed amount must be set.`);
                    }
                }
            });

            if (hasErrors) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
                return false;
            }

            // Clear draft on successful submission
            localStorage.removeItem('deductions_draft_{{ $employee->id }}');
            showNotification('Deductions updated successfully!', 'success');
        });
    }
});

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection
