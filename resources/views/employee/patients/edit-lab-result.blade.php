@extends('employee.layouts.app')

@section('title', 'Edit Lab Result - ' . $patient->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Lab Result for {{ $patient->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.patients.show-lab-result', [$patient->id, $result->id]) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View Result
                        </a>
                        <a href="{{ route('employee.patients.lab-results', $patient->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Results
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.patients.update-lab-result', [$patient->id, $result->id]) }}">
                @csrf
                @method('PUT')

                <!-- Test Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-flask text-primary me-2"></i>Test Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="test_name" class="form-label">Test Name <span class="text-danger">*</span></label>
                                    <input type="text" id="test_name" name="test_name" value="{{ old('test_name', $result->test_name) }}" class="form-control" required maxlength="255">
                                    @if($errors->has('test_name'))
                                        <div class="text-danger small mt-1">{{ $errors->first('test_name') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="test_category" class="form-label">Test Category <span class="text-danger">*</span></label>
                                    <select id="test_category" name="test_category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <option value="hematology" {{ old('test_category', $result->test_category) === 'hematology' ? 'selected' : '' }}>Hematology</option>
                                        <option value="chemistry" {{ old('test_category', $result->test_category) === 'chemistry' ? 'selected' : '' }}>Chemistry</option>
                                        <option value="microbiology" {{ old('test_category', $result->test_category) === 'microbiology' ? 'selected' : '' }}>Microbiology</option>
                                        <option value="immunology" {{ old('test_category', $result->test_category) === 'immunology' ? 'selected' : '' }}>Immunology</option>
                                        <option value="endocrinology" {{ old('test_category', $result->test_category) === 'endocrinology' ? 'selected' : '' }}>Endocrinology</option>
                                        <option value="toxicology" {{ old('test_category', $result->test_category) === 'toxicology' ? 'selected' : '' }}>Toxicology</option>
                                        <option value="urinalysis" {{ old('test_category', $result->test_category) === 'urinalysis' ? 'selected' : '' }}>Urinalysis</option>
                                        <option value="other" {{ old('test_category', $result->test_category) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @if($errors->has('test_category'))
                                        <div class="text-danger small mt-1">{{ $errors->first('test_category') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="test_code" class="form-label">Test Code</label>
                                    <input type="text" id="test_code" name="test_code" value="{{ old('test_code', $result->test_code) }}" class="form-control" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="test_status" class="form-label">Test Status <span class="text-danger">*</span></label>
                                    <select id="test_status" name="test_status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="ordered" {{ old('test_status', $result->test_status) === 'ordered' ? 'selected' : '' }}>Ordered</option>
                                        <option value="in_progress" {{ old('test_status', $result->test_status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('test_status', $result->test_status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('test_status', $result->test_status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="rejected" {{ old('test_status', $result->test_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    @if($errors->has('test_status'))
                                        <div class="text-danger small mt-1">{{ $errors->first('test_status') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="test_description" class="form-label">Test Description</label>
                                    <textarea id="test_description" name="test_description" class="form-control" rows="3" maxlength="500">{{ old('test_description', $result->test_description) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-vial text-success me-2"></i>Sample Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sample_type" class="form-label">Sample Type <span class="text-danger">*</span></label>
                                    <select id="sample_type" name="sample_type" class="form-select" required>
                                        <option value="">Select Sample Type</option>
                                        <option value="blood" {{ old('sample_type', $result->sample_type) === 'blood' ? 'selected' : '' }}>Blood</option>
                                        <option value="serum" {{ old('sample_type', $result->sample_type) === 'serum' ? 'selected' : '' }}>Serum</option>
                                        <option value="plasma" {{ old('sample_type', $result->sample_type) === 'plasma' ? 'selected' : '' }}>Plasma</option>
                                        <option value="urine" {{ old('sample_type', $result->sample_type) === 'urine' ? 'selected' : '' }}>Urine</option>
                                        <option value="stool" {{ old('sample_type', $result->sample_type) === 'stool' ? 'selected' : '' }}>Stool</option>
                                        <option value="sputum" {{ old('sample_type', $result->sample_type) === 'sputum' ? 'selected' : '' }}>Sputum</option>
                                        <option value="csf" {{ old('sample_type', $result->sample_type) === 'csf' ? 'selected' : '' }}>CSF</option>
                                        <option value="other" {{ old('sample_type', $result->sample_type) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @if($errors->has('sample_type'))
                                        <div class="text-danger small mt-1">{{ $errors->first('sample_type') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6" id="other_sample_type_container" style="display: {{ old('sample_type', $result->sample_type) === 'other' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="sample_type_other" class="form-label">Other Sample Type</label>
                                    <input type="text" id="sample_type_other" name="sample_type_other" value="{{ old('sample_type_other', $result->sample_type_other) }}" class="form-control" maxlength="100">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sample_collection_date_time" class="form-label">Sample Collection Date/Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="sample_collection_date_time" name="sample_collection_date_time" value="{{ old('sample_collection_date_time', $result->sample_collection_date_time ? \Carbon\Carbon::parse($result->sample_collection_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control" required>
                                    @if($errors->has('sample_collection_date_time'))
                                        <div class="text-danger small mt-1">{{ $errors->first('sample_collection_date_time') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sample_id" class="form-label">Sample ID</label>
                                    <input type="text" id="sample_id" name="sample_id" value="{{ old('sample_id', $result->sample_id) }}" class="form-control" maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Healthcare Providers -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-md text-danger me-2"></i>Healthcare Providers</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ordered_by" class="form-label">Ordered By <span class="text-danger">*</span></label>
                                    <select id="ordered_by" name="ordered_by" class="form-select" required>
                                        <option value="">Select Provider</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('ordered_by', $result->ordered_by) == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('ordered_by'))
                                        <div class="text-danger small mt-1">{{ $errors->first('ordered_by') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="performed_by" class="form-label">Performed By</label>
                                    <select id="performed_by" name="performed_by" class="form-select">
                                        <option value="">Select Technician</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('performed_by', $result->performed_by) == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reviewed_by" class="form-label">Reviewed By</label>
                                    <select id="reviewed_by" name="reviewed_by" class="form-select">
                                        <option value="">Select Reviewer</option>
                                        @foreach($providers as $provider)
                                            <option value="{{ $provider->id }}" {{ old('reviewed_by', $result->reviewed_by) == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Results -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line text-info me-2"></i>Test Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="result_value" class="form-label">Result Value</label>
                                    <input type="text" id="result_value" name="result_value" value="{{ old('result_value', $result->result_value) }}" class="form-control" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="result_unit" class="form-label">Result Unit</label>
                                    <input type="text" id="result_unit" name="result_unit" value="{{ old('result_unit', $result->result_unit) }}" class="form-control" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reference_range" class="form-label">Reference Range</label>
                                    <input type="text" id="reference_range" name="reference_range" value="{{ old('reference_range', $result->reference_range) }}" class="form-control" maxlength="100">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="result_status" class="form-label">Result Status <span class="text-danger">*</span></label>
                                    <select id="result_status" name="result_status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="normal" {{ old('result_status', $result->result_status) === 'normal' ? 'selected' : '' }}>Normal</option>
                                        <option value="abnormal" {{ old('result_status', $result->result_status) === 'abnormal' ? 'selected' : '' }}>Abnormal</option>
                                        <option value="critical" {{ old('result_status', $result->result_status) === 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @if($errors->has('result_status'))
                                        <div class="text-danger small mt-1">{{ $errors->first('result_status') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="test_result" class="form-label">Detailed Test Result</label>
                                    <textarea id="test_result" name="test_result" class="form-control" rows="3" maxlength="500">{{ old('test_result', $result->test_result) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Dates -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i>Important Dates</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="test_ordered_date_time" class="form-label">Test Ordered Date/Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="test_ordered_date_time" name="test_ordered_date_time" value="{{ old('test_ordered_date_time', $result->test_ordered_date_time ? \Carbon\Carbon::parse($result->test_ordered_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control" required>
                                    @if($errors->has('test_ordered_date_time'))
                                        <div class="text-danger small mt-1">{{ $errors->first('test_ordered_date_time') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="test_performed_date_time" class="form-label">Test Performed Date/Time</label>
                                    <input type="datetime-local" id="test_performed_date_time" name="test_performed_date_time" value="{{ old('test_performed_date_time', $result->test_performed_date_time ? \Carbon\Carbon::parse($result->test_performed_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="result_available_date_time" class="form-label">Result Available Date/Time</label>
                                    <input type="datetime-local" id="result_available_date_time" name="result_available_date_time" value="{{ old('result_available_date_time', $result->result_available_date_time ? \Carbon\Carbon::parse($result->result_available_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="result_reviewed_date_time" class="form-label">Result Reviewed Date/Time</label>
                                    <input type="datetime-local" id="result_reviewed_date_time" name="result_reviewed_date_time" value="{{ old('result_reviewed_date_time', $result->result_reviewed_date_time ? \Carbon\Carbon::parse($result->result_reviewed_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-alt text-secondary me-2"></i>Additional Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="clinical_indication" class="form-label">Clinical Indication</label>
                                    <textarea id="clinical_indication" name="clinical_indication" class="form-control" rows="2" maxlength="500">{{ old('clinical_indication', $result->clinical_indication) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="interpretation" class="form-label">Interpretation</label>
                                    <textarea id="interpretation" name="interpretation" class="form-control" rows="2" maxlength="1000">{{ old('interpretation', $result->interpretation) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Comments</label>
                                    <textarea id="comments" name="comments" class="form-control" rows="2" maxlength="1000">{{ old('comments', $result->comments) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quality Control & Billing -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-check-circle text-success me-2"></i>Quality Control & Billing</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="qc_passed" name="qc_passed" value="1" {{ old('qc_passed', $result->qc_passed) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="qc_passed">QC Passed</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="urgent" name="urgent" value="1" {{ old('urgent', $result->urgent) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="urgent">Urgent</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="stat" name="stat" value="1" {{ old('stat', $result->stat) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="stat">STAT</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="qc_notes" class="form-label">QC Notes</label>
                                    <textarea id="qc_notes" name="qc_notes" class="form-control" rows="2" maxlength="500">{{ old('qc_notes', $result->qc_notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="test_cost" class="form-label">Test Cost</label>
                                    <input type="number" step="0.01" id="test_cost" name="test_cost" value="{{ old('test_cost', $result->test_cost) }}" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="covered_by_philhealth" name="covered_by_philhealth" value="1" {{ old('covered_by_philhealth', $result->covered_by_philhealth) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="covered_by_philhealth">Covered by PhilHealth</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="philhealth_coverage_amount" class="form-label">PhilHealth Coverage Amount</label>
                                    <input type="number" step="0.01" id="philhealth_coverage_amount" name="philhealth_coverage_amount" value="{{ old('philhealth_coverage_amount', $result->philhealth_coverage_amount) }}" class="form-control" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="requires_follow_up" name="requires_follow_up" value="1" {{ old('requires_follow_up', $result->requires_follow_up) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_follow_up">Requires Follow-up</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Follow-up Date</label>
                                    <input type="date" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date', $result->follow_up_date ? \Carbon\Carbon::parse($result->follow_up_date)->format('Y-m-d') : '') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="follow_up_instructions" class="form-label">Follow-up Instructions</label>
                                    <input type="text" id="follow_up_instructions" name="follow_up_instructions" value="{{ old('follow_up_instructions', $result->follow_up_instructions) }}" class="form-control" maxlength="500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rejection Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-times-circle text-danger me-2"></i>Rejection Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                    <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="2" maxlength="500">{{ old('rejection_reason', $result->rejection_reason) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rejected_date_time" class="form-label">Rejected Date/Time</label>
                                    <input type="datetime-local" id="rejected_date_time" name="rejected_date_time" value="{{ old('rejected_date_time', $result->rejected_date_time ? \Carbon\Carbon::parse($result->rejected_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('employee.patients.show-lab-result', [$patient->id, $result->id]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Lab Result</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Show/hide other sample type field
    document.getElementById('sample_type').addEventListener('change', function() {
        const otherField = document.getElementById('other_sample_type_container');
        if (this.value === 'other') {
            otherField.style.display = 'block';
        } else {
            otherField.style.display = 'none';
        }
    });
</script>
@endsection
