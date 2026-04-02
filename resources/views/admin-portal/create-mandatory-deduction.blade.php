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
                                    <label for="deduction_type" class="form-label">Deduction Type <span
                                            class="text-danger">*</span></label>
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
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                    name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="percentage_rate" class="form-label">Percentage Rate (%)</label>
                                    <input type="number" class="form-control @error('percentage_rate') is-invalid @enderror"
                                        id="percentage_rate" name="percentage_rate" value="{{ old('percentage_rate', 0) }}"
                                        step="0.01" min="0" max="100">
                                    <small class="form-text text-muted">Enter percentage (0-100) or leave 0 if not
                                        applicable</small>
                                    @error('percentage_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="fixed_amount" class="form-label">Fixed Amount (₱)</label>
                                    <input type="number" class="form-control @error('fixed_amount') is-invalid @enderror"
                                        id="fixed_amount" name="fixed_amount" value="{{ old('fixed_amount', 0) }}"
                                        step="0.01" min="0">
                                    <small class="form-text text-muted">Enter fixed amount or leave 0 if not
                                        applicable</small>
                                    @error('fixed_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="minimum_base_salary" class="form-label">Minimum Base Salary (₱)</label>
                                    <input type="number"
                                        class="form-control @error('minimum_base_salary') is-invalid @enderror"
                                        id="minimum_base_salary" name="minimum_base_salary"
                                        value="{{ old('minimum_base_salary', 0) }}" step="0.01" min="0">
                                    <small class="form-text text-muted">Minimum salary required for this deduction to
                                        apply</small>
                                    @error('minimum_base_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="maximum_deduction" class="form-label">Maximum Deduction (₱)</label>
                                    <input type="number"
                                        class="form-control @error('maximum_deduction') is-invalid @enderror"
                                        id="maximum_deduction" name="maximum_deduction"
                                        value="{{ old('maximum_deduction') }}" step="0.01" min="0">
                                    <small class="form-text text-muted">Maximum allowed deduction amount (leave empty for no
                                        limit)</small>
                                    @error('maximum_deduction')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="effective_date" class="form-label">Effective Date</label>
                                    <input type="date" class="form-control @error('effective_date') is-invalid @enderror"
                                        id="effective_date" name="effective_date" value="{{ old('effective_date') }}">
                                    @error('effective_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Uncheck to deactivate this deduction</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                    rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Employee Selection -->
                            <!-- Employee Selection -->
                            <div id="individual_employees_section">
                                <div class="mb-3">
                                    <label class="form-label">Employees <span class="text-danger">*</span></label>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mb-2">
                                        <button type="button" class="btn btn-primary btn-sm" id="selectAll">Select
                                            All</button>
                                        <button type="button" class="btn btn-danger btn-sm" id="clearAll">Clear All</button>
                                    </div>

                                    <!-- Position Buttons -->
                                    <div class="row mx-1 mb-3">
                                        @php
                                            $positions = [
                                                'Head Nurse',
                                                'Nurse',
                                                'Head Doctor',
                                                'Doctor',
                                                'Midwife',
                                                'Clinic Staff',
                                                'Others/No Position'
                                            ];
                                        @endphp

                                        @foreach($positions as $position)
                                            <button type="button" class="btn btn-outline-success col p-2 mx-1 mb-2 position-btn"
                                                data-position="{{ strtolower($position) }}">
                                                {{ $position }}
                                            </button>
                                        @endforeach
                                    </div>

                                    <!-- Search -->
                                    <input type="text" id="employeeSearch" class="form-control mb-3"
                                        placeholder="Search employee...">

                                    <!-- Employee List -->
                                    <div class="mx-2" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($employees as $employee)
                                            @php
                                                $position = $employee->employeeProfile->position ?? 'Others/No Position';
                                            @endphp

                                            <div class="form-check employee-item">
                                                <input class="form-check-input employee-checkbox" type="checkbox"
                                                    name="employee_ids[]" value="{{ $employee->id }}"
                                                    id="emp_{{ $employee->id }}" data-position="{{ strtolower($position) }}">
                                                <label class="form-check-label" for="emp_{{ $employee->id }}">
                                                    {{ $employee->name }} -
                                                    <small class="text-muted">{{ $position }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ─── AUTO NAME FILL ───────────────────────────────────────────
            const typeSelect = document.getElementById('deduction_type');
            const nameInput = document.getElementById('name');

            typeSelect.addEventListener('change', function () {
                const types = @json($deductionTypes);
                if (this.value && !nameInput.value.trim()) {
                    nameInput.value = types[this.value] ?? '';
                }
            });

            // ─── VALIDATE AMOUNTS ─────────────────────────────────────────
            const pctInput = document.getElementById('percentage_rate');
            const fixedInput = document.getElementById('fixed_amount');

            function validateAmounts() {
                const pct = parseFloat(pctInput.value) || 0;
                const fixed = parseFloat(fixedInput.value) || 0;
                const msg = (pct === 0 && fixed === 0)
                    ? 'Provide a percentage rate or a fixed amount.'
                    : '';
                pctInput.setCustomValidity(msg);
                fixedInput.setCustomValidity(msg);
            }

            pctInput.addEventListener('input', validateAmounts);
            fixedInput.addEventListener('input', validateAmounts);

            // ─── HELPERS ──────────────────────────────────────────────────
            function visibleCheckboxes(selector = '.employee-checkbox') {
                return Array.from(document.querySelectorAll(selector))
                    .filter(cb => cb.closest('.employee-item').style.display !== 'none');
            }

            function visibleByPosition(position) {
                return visibleCheckboxes(`.employee-checkbox[data-position="${position}"]`);
            }

            // ─── POSITION BUTTON SYNC ─────────────────────────────────────
            function syncBtn(btn) {
                const boxes = visibleByPosition(btn.dataset.position);
                const total = boxes.length;
                const checked = boxes.filter(cb => cb.checked).length;
                const allChecked = total > 0 && checked === total;
                const partial = checked > 0 && checked < total;

                btn.classList.toggle('btn-success', allChecked);
                btn.classList.toggle('btn-warning', partial);
                btn.classList.toggle('btn-outline-success', !allChecked && !partial);
                btn.style.opacity = total === 0 ? '0.4' : '1';
            }

            function syncAllBtns() {
                document.querySelectorAll('.position-btn').forEach(syncBtn);
            }

            // ─── POSITION BUTTON CLICK ────────────────────────────────────
            document.querySelectorAll('.position-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const boxes = visibleByPosition(this.dataset.position);
                    const allChecked = boxes.length > 0 && boxes.every(cb => cb.checked);
                    boxes.forEach(cb => { cb.checked = !allChecked; });
                    syncAllBtns();
                });
            });

            // ─── MANUAL CHECKBOX SYNC ─────────────────────────────────────
            document.querySelectorAll('.employee-checkbox').forEach(cb => {
                cb.addEventListener('change', syncAllBtns);
            });

            // ─── SELECT ALL / CLEAR ALL ───────────────────────────────────
            document.getElementById('selectAll').addEventListener('click', () => {
                visibleCheckboxes().forEach(cb => { cb.checked = true; });
                syncAllBtns();
            });

            document.getElementById('clearAll').addEventListener('click', () => {
                visibleCheckboxes().forEach(cb => { cb.checked = false; });
                syncAllBtns();
            });

            // ─── SEARCH FILTER ────────────────────────────────────────────
            document.getElementById('employeeSearch').addEventListener('input', function () {
                const kw = this.value.toLowerCase();
                document.querySelectorAll('.employee-item').forEach(item => {
                    item.style.display = item.innerText.toLowerCase().includes(kw) ? '' : 'none';
                });
                syncAllBtns();
            });

            // ─── INIT ─────────────────────────────────────────────────────
            syncAllBtns();
            validateAmounts();
        });
    </script>
@endsection