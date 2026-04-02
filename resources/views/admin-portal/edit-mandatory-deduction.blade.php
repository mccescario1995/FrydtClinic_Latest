@extends('admin-portal.layouts.app')

@section('title', 'Edit Mandatory Deduction')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Mandatory Deduction</h1>
            <p class="text-muted">Update deduction details for "{{ $deduction->name }}"</p>
        </div>
        <div>
            <a href="{{ route('admin-portal.mandatory-deductions.show', $deduction->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('admin-portal.mandatory-deductions') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deduction Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin-portal.mandatory-deductions.update', $deduction->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="deduction_type" class="form-label">Deduction Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('deduction_type') is-invalid @enderror"
                                        id="deduction_type" name="deduction_type" required>
                                    <option value="">Select Type</option>
                                    @foreach($deductionTypes as $type => $label)
                                        <option value="{{ $type }}" {{ old('deduction_type', $deduction->deduction_type) == $type ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('deduction_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $deduction->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $deduction->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="percentage_rate" class="form-label">Percentage Rate (%)</label>
                                <input type="number" class="form-control @error('percentage_rate') is-invalid @enderror"
                                       id="percentage_rate" name="percentage_rate"
                                       value="{{ old('percentage_rate', $deduction->percentage_rate) }}"
                                       step="0.01" min="0" max="100">
                                <small class="form-text text-muted">Enter percentage (0-100) or leave 0 if not applicable</small>
                                @error('percentage_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fixed_amount" class="form-label">Fixed Amount (₱)</label>
                                <input type="number" class="form-control @error('fixed_amount') is-invalid @enderror"
                                       id="fixed_amount" name="fixed_amount"
                                       value="{{ old('fixed_amount', $deduction->fixed_amount) }}"
                                       step="0.01" min="0">
                                <small class="form-text text-muted">Enter fixed amount or leave 0 if not applicable</small>
                                @error('fixed_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="minimum_base_salary" class="form-label">Minimum Base Salary (₱)</label>
                                <input type="number" class="form-control @error('minimum_base_salary') is-invalid @enderror"
                                       id="minimum_base_salary" name="minimum_base_salary"
                                       value="{{ old('minimum_base_salary', $deduction->minimum_base_salary) }}"
                                       step="0.01" min="0">
                                <small class="form-text text-muted">Minimum salary required for this deduction to apply</small>
                                @error('minimum_base_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="maximum_deduction" class="form-label">Maximum Deduction (₱)</label>
                                <input type="number" class="form-control @error('maximum_deduction') is-invalid @enderror"
                                       id="maximum_deduction" name="maximum_deduction"
                                       value="{{ old('maximum_deduction', $deduction->maximum_deduction) }}"
                                       step="0.01" min="0">
                                <small class="form-text text-muted">Maximum allowed deduction amount (leave empty for no limit)</small>
                                @error('maximum_deduction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="effective_date" class="form-label">Effective Date</label>
                                <input type="date" class="form-control @error('effective_date') is-invalid @enderror"
                                       id="effective_date" name="effective_date"
                                       value="{{ old('effective_date', $deduction->effective_date ? $deduction->effective_date->format('Y-m-d') : '') }}">
                                @error('effective_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active"
                                           name="is_active" value="1" {{ old('is_active', $deduction->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                                <small class="form-text text-muted">Uncheck to deactivate this deduction</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3">{{ old('notes', $deduction->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin-portal.mandatory-deductions.show', $deduction->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="{{ route('admin-portal.mandatory-deductions') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Deduction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar with Deduction Info -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deduction Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        <span class="text-muted">{{ $deduction->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        <span class="text-muted">{{ $deduction->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Employee Associations:</strong><br>
                        <span class="badge bg-info">{{ $deduction->employeeDeductions()->count() }} employees</span>
                    </div>

                    @if($deduction->employeeDeductions()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This deduction is assigned to {{ $deduction->employeeDeductions()->count() }} employees.
                            Changes will affect their payroll calculations.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Calculation Preview</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="preview_salary" class="form-label">Test Salary Amount (₱)</label>
                        <input type="number" class="form-control" id="preview_salary"
                               placeholder="Enter test salary" min="0" step="0.01">
                    </div>
                    <div class="preview-result">
                        <div class="alert alert-info">
                            <strong>Calculated Deduction:</strong><br>
                            <span id="calculated_amount">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const percentageInput = document.getElementById('percentage_rate');
    const fixedAmountInput = document.getElementById('fixed_amount');
    const minimumSalaryInput = document.getElementById('minimum_base_salary');
    const maximumDeductionInput = document.getElementById('maximum_deduction');
    const previewSalaryInput = document.getElementById('preview_salary');
    const calculatedAmountSpan = document.getElementById('calculated_amount');

    function calculateDeduction() {
        const salary = parseFloat(previewSalaryInput.value) || 0;
        const percentage = parseFloat(percentageInput.value) || 0;
        const fixedAmount = parseFloat(fixedAmountInput.value) || 0;
        const minSalary = parseFloat(minimumSalaryInput.value) || 0;
        const maxDeduction = parseFloat(maximumDeductionInput.value) || 0;

        let calculated = 0;

        if (salary >= minSalary) {
            // Calculate percentage-based deduction
            if (percentage > 0) {
                calculated += (salary * percentage) / 100;
            }

            // Add fixed amount
            if (fixedAmount > 0) {
                calculated += fixedAmount;
            }

            // Apply maximum limit if set
            if (maxDeduction > 0 && calculated > maxDeduction) {
                calculated = maxDeduction;
            }
        }

        calculatedAmountSpan.textContent = '₱' + calculated.toFixed(2);
    }

    function validateAmounts() {
        const percentage = parseFloat(percentageInput.value) || 0;
        const fixed = parseFloat(fixedAmountInput.value) || 0;

        if (percentage === 0 && fixed === 0) {
            percentageInput.setCustomValidity('Please provide either a percentage rate or fixed amount');
            fixedAmountInput.setCustomValidity('Please provide either a percentage rate or fixed amount');
        } else {
            percentageInput.setCustomValidity('');
            fixedAmountInput.setCustomValidity('');
        }
    }

    // Event listeners
    [percentageInput, fixedAmountInput, minimumSalaryInput, maximumDeductionInput, previewSalaryInput]
        .forEach(input => {
            input.addEventListener('input', function() {
                if (input === previewSalaryInput || input === percentageInput ||
                    input === fixedAmountInput || input === minimumSalaryInput ||
                    input === maximumDeductionInput) {
                    calculateDeduction();
                }
                if (input === percentageInput || input === fixedAmountInput) {
                    validateAmounts();
                }
            });
        });

    // Auto-fill name based on deduction type
    const deductionTypeSelect = document.getElementById('deduction_type');
    const nameInput = document.getElementById('name');

    deductionTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        const deductionTypes = @json($deductionTypes);

        if (selectedType && !nameInput.value) {
            nameInput.value = deductionTypes[selectedType] || '';
        }
    });

    // Initial calculation
    calculateDeduction();
});
</script>
@endpush
