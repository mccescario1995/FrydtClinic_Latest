@extends('admin-portal.layouts.app')

@section('title', 'Create Lab Result')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-flask me-2"></i>Create Lab Result</h1>
                <p class="text-muted mb-0">Add a new laboratory result for {{ $patient->name }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.patients.lab-results', $patient->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Lab Results
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Patient Information Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Patient Name</small>
                            <strong>{{ $patient->name }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $patient->email }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Phone</small>
                            <strong>{{ $patient->patientProfile->phone ?? 'Not provided' }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Birth Date</small>
                            <strong>{{ $patient->patientProfile->birth_date ? $patient->patientProfile->birth_date->format('M d, Y') : 'Not provided' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lab Result Form -->
<div class="row">
    <div class="col-md-12">
        <form method="POST" action="{{ route('admin-portal.patients.store-lab-result', $patient->id) }}">
            @csrf

            <!-- Test Information -->
            <div class="admin-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-vial me-2"></i>Test Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="test_name" class="form-label">Test Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="test_name" name="test_name" required>
                        </div>
                        <div class="col-md-3">
                            <label for="test_category" class="form-label">Test Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="test_category" name="test_category" required>
                                <option value="">Select Category</option>
                                <option value="Hematology">Hematology</option>
                                <option value="Chemistry">Chemistry</option>
                                <option value="Microbiology">Microbiology</option>
                                <option value="Immunology">Immunology</option>
                                <option value="Endocrinology">Endocrinology</option>
                                <option value="Toxicology">Toxicology</option>
                                <option value="Urinalysis">Urinalysis</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="test_code" class="form-label">Test Code</label>
                            <input type="text" class="form-control" id="test_code" name="test_code" maxlength="50">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sample_type" class="form-label">Sample Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="sample_type" name="sample_type" required>
                                <option value="">Select Sample Type</option>
                                <option value="Blood">Blood</option>
                                <option value="Serum">Serum</option>
                                <option value="Plasma">Plasma</option>
                                <option value="Urine">Urine</option>
                                <option value="Stool">Stool</option>
                                <option value="Sputum">Sputum</option>
                                <option value="CSF">CSF</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sample_type_other" class="form-label">Other Sample Type</label>
                            <input type="text" class="form-control" id="sample_type_other" name="sample_type_other" maxlength="100">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sample_collection_date_time" class="form-label">Sample Collection Date/Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="sample_collection_date_time" name="sample_collection_date_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sample_id" class="form-label">Sample ID</label>
                            <input type="text" class="form-control" id="sample_id" name="sample_id" maxlength="100">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="test_description" class="form-label">Test Description</label>
                        <textarea class="form-control" id="test_description" name="test_description" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Providers -->
            <div class="admin-card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Healthcare Providers</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="ordered_by" class="form-label">Ordered By <span class="text-danger">*</span></label>
                            <select class="form-select" id="ordered_by" name="ordered_by" required>
                                <option value="">Select Provider</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="performed_by" class="form-label">Performed By</label>
                            <select class="form-select" id="performed_by" name="performed_by">
                                <option value="">Select Technician</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="reviewed_by" class="form-label">Reviewed By</label>
                            <select class="form-select" id="reviewed_by" name="reviewed_by">
                                <option value="">Select Reviewer</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div class="admin-card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Test Results</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="result_value" class="form-label">Result Value</label>
                            <input type="text" class="form-control" id="result_value" name="result_value" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label for="result_unit" class="form-label">Result Unit</label>
                            <input type="text" class="form-control" id="result_unit" name="result_unit" maxlength="50">
                        </div>
                        <div class="col-md-4">
                            <label for="reference_range" class="form-label">Reference Range</label>
                            <input type="text" class="form-control" id="reference_range" name="reference_range" maxlength="100">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="result_status" class="form-label">Result Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="result_status" name="result_status" required>
                                <option value="">Select Status</option>
                                <option value="normal">Normal</option>
                                <option value="abnormal">Abnormal</option>
                                <option value="critical">Critical</option>
                                <option value="ordered">Ordered</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="test_status" class="form-label">Test Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="test_status" name="test_status" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="test_ordered_date_time" class="form-label">Test Ordered Date/Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="test_ordered_date_time" name="test_ordered_date_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="test_performed_date_time" class="form-label">Test Performed Date/Time</label>
                            <input type="datetime-local" class="form-control" id="test_performed_date_time" name="test_performed_date_time">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="result_available_date_time" class="form-label">Result Available Date/Time</label>
                            <input type="datetime-local" class="form-control" id="result_available_date_time" name="result_available_date_time">
                        </div>
                        <div class="col-md-6">
                            <label for="result_reviewed_date_time" class="form-label">Result Reviewed Date/Time</label>
                            <input type="datetime-local" class="form-control" id="result_reviewed_date_time" name="result_reviewed_date_time">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="test_result" class="form-label">Detailed Test Result</label>
                        <textarea class="form-control" id="test_result" name="test_result" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="admin-card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="clinical_indication" class="form-label">Clinical Indication</label>
                            <textarea class="form-control" id="clinical_indication" name="clinical_indication" rows="2" maxlength="500"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="interpretation" class="form-label">Interpretation</label>
                            <textarea class="form-control" id="interpretation" name="interpretation" rows="2" maxlength="1000"></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="2" maxlength="1000"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rejection_reason" class="form-label">Rejection Reason</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="2" maxlength="500"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="rejected_date_time" class="form-label">Rejected Date/Time</label>
                            <input type="datetime-local" class="form-control" id="rejected_date_time" name="rejected_date_time">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quality Control & Billing -->
            <div class="admin-card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Quality Control & Billing</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="qc_passed" name="qc_passed" value="1">
                                <label class="form-check-label" for="qc_passed">
                                    QC Passed
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="urgent" name="urgent" value="1">
                                <label class="form-check-label" for="urgent">
                                    Urgent
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="stat" name="stat" value="1">
                                <label class="form-check-label" for="stat">
                                    STAT
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="qc_notes" class="form-label">QC Notes</label>
                        <textarea class="form-control" id="qc_notes" name="qc_notes" rows="2" maxlength="500"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="test_cost" class="form-label">Test Cost</label>
                            <input type="number" step="0.01" class="form-control" id="test_cost" name="test_cost" min="0">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="covered_by_philhealth" name="covered_by_philhealth" value="1">
                                <label class="form-check-label" for="covered_by_philhealth">
                                    Covered by PhilHealth
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="philhealth_coverage_amount" class="form-label">PhilHealth Coverage Amount</label>
                            <input type="number" step="0.01" class="form-control" id="philhealth_coverage_amount" name="philhealth_coverage_amount" min="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requires_follow_up" name="requires_follow_up" value="1">
                                <label class="form-check-label" for="requires_follow_up">
                                    Requires Follow-up
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="follow_up_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="follow_up_date" name="follow_up_date">
                        </div>
                        <div class="col-md-3">
                            <label for="follow_up_instructions" class="form-label">Follow-up Instructions</label>
                            <input type="text" class="form-control" id="follow_up_instructions" name="follow_up_instructions" maxlength="500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="admin-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Lab Result
                            </button>
                            <a href="{{ route('admin-portal.patients.lab-results', $patient->id) }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
