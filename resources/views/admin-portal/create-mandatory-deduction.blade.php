@extends('admin-portal.layouts.app')

@section('title', 'Create Mandatory Deduction')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Mandatory Deduction</h1>
            <p class="text-muted">Add a new mandatory deduction for employees</p>
        </div>
        <div>
            <a href="{{ route('admin-portal.mandatory-deductions') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deduction Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin-portal.mandatory-deductions.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="deduction_type" class="form-label">Deduction Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('deduction_type') is-invalid @enderror"
                                        id="deduction_type" name="deduction_type" required>
                                    <option value="">Select Type</option>
                                    @foreach($deductionTypes as $type => $label)
                                        <option value="{{ $type }}" {{ old('deduction_type') == $type ? 'selected' : '' }}>
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
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="percentage_rate" class="form-label">Percentage Rate (%)</label>
                                <input type="number" class="form-control @error('percentage_rate') is-invalid @enderror"
                                       id="percentage_rate" name="percentage_rate"
                                       value="{{ old('percentage_rate', 0) }}"
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
                                       value="{{ old('fixed_amount', 0) }}"
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
                                       value="{{ old('minimum_base_salary', 0) }}"
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
                                       value="{{ old('maximum_deduction') }}"
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
                                       value="{{ old('effective_date') }}">
                                @error('effective_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active"
                                           name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin-portal.mandatory-deductions') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Deduction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar with Help -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deduction Guidelines</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Philippine Mandatory Deductions:</h6>
                    <ul class="list-unstyled">
                        <li><strong>SSS:</strong> Social Security System contribution</li>
                        <li><strong>PhilHealth:</strong> National Health Insurance Program</li>
                        <li><strong>Pag-IBIG:</strong> Home development mutual fund</li>
                        <li><strong>Tax:</strong> Withholding tax on salary</li>
                        <li><strong>Other:</strong> Additional mandatory deductions</li>
                    </ul>

                    <hr>

                    <h6 class="text-primary">Calculation Tips:</h6>
                    <ul class="list-unstyled">
                        <li>• Use percentage rate for rate-based deductions</li>
                        <li>• Use fixed amount for flat rate deductions</li>
                        <li>• Combine both for complex calculations</li>
                        <li>• Set minimum salary to avoid low-income impact</li>
                        <li>• Set maximum to cap deduction amounts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Validate that at least one of percentage or fixed amount is provided
    const percentageInput = document.getElementById('percentage_rate');
    const fixedAmountInput = document.getElementById('fixed_amount');

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

    percentageInput.addEventListener('input', validateAmounts);
    fixedAmountInput.addEventListener('input', validateAmounts);
});
</script>
@endpush
