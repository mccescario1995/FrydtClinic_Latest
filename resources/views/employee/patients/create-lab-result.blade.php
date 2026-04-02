@extends('employee.layouts.app')

@section('title', 'Add Lab Result - ' . $patient->name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Add Lab Result</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <a href="{{ route('employee.patients.lab-results', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back to Results</a>
    </div>

    <!-- Form -->
    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd;">
        <form method="POST" action="{{ route('employee.patients.store-lab-result', $patient->id) }}">
            @csrf

            <!-- Basic Test Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Test Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="test_name" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Test Name <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="test_name" name="test_name" value="{{ old('test_name') }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('test_name') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('test_name'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('test_name') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="test_category" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Test Category <span style="color: #dc3545;">*</span></label>
                        <select id="test_category" name="test_category" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('test_category') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Category</option>
                            <option value="hematology" {{ old('test_category') === 'hematology' ? 'selected' : '' }}>Hematology</option>
                            <option value="chemistry" {{ old('test_category') === 'chemistry' ? 'selected' : '' }}>Chemistry</option>
                            <option value="microbiology" {{ old('test_category') === 'microbiology' ? 'selected' : '' }}>Microbiology</option>
                            <option value="immunology" {{ old('test_category') === 'immunology' ? 'selected' : '' }}>Immunology</option>
                            <option value="endocrinology" {{ old('test_category') === 'endocrinology' ? 'selected' : '' }}>Endocrinology</option>
                            <option value="other" {{ old('test_category') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @if($errors->has('test_category'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('test_category') }}</div>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="ordered_by" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Ordered By <span style="color: #dc3545;">*</span></label>
                        <select id="ordered_by" name="ordered_by" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('ordered_by') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Provider</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('ordered_by') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('ordered_by'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('ordered_by') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="performed_by" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Performed By</label>
                        <select id="performed_by" name="performed_by"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Technician</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('performed_by') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="reviewed_by" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Reviewed By</label>
                        <select id="reviewed_by" name="reviewed_by"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Provider</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('reviewed_by') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sample Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Sample Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="sample_type" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Sample Type <span style="color: #dc3545;">*</span></label>
                        <select id="sample_type" name="sample_type" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('sample_type') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Sample Type</option>
                            <option value="blood" {{ old('sample_type') === 'blood' ? 'selected' : '' }}>Blood</option>
                            <option value="urine" {{ old('sample_type') === 'urine' ? 'selected' : '' }}>Urine</option>
                            <option value="stool" {{ old('sample_type') === 'stool' ? 'selected' : '' }}>Stool</option>
                            <option value="sputum" {{ old('sample_type') === 'sputum' ? 'selected' : '' }}>Sputum</option>
                            <option value="swab" {{ old('sample_type') === 'swab' ? 'selected' : '' }}>Swab</option>
                            <option value="other" {{ old('sample_type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @if($errors->has('sample_type'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('sample_type') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="sample_collection_date_time" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Collection Date/Time <span style="color: #dc3545;">*</span></label>
                        <input type="datetime-local" id="sample_collection_date_time" name="sample_collection_date_time" value="{{ old('sample_collection_date_time') }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('sample_collection_date_time') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('sample_collection_date_time'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('sample_collection_date_time') }}</div>
                        @endif
                    </div>
                </div>

                <div id="other_sample_type" style="display: {{ old('sample_type') === 'other' ? 'block' : 'none' }};">
                    <label for="sample_type_other" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Specify Other Sample Type</label>
                    <input type="text" id="sample_type_other" name="sample_type_other" value="{{ old('sample_type_other') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
            </div>

            <!-- Test Status -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #ffc107; padding-bottom: 10px;">Test Status</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="test_ordered_date_time" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Ordered Date/Time <span style="color: #dc3545;">*</span></label>
                        <input type="datetime-local" id="test_ordered_date_time" name="test_ordered_date_time" value="{{ old('test_ordered_date_time') }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('test_ordered_date_time') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('test_ordered_date_time'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('test_ordered_date_time') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="test_status" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Test Status <span style="color: #dc3545;">*</span></label>
                        <select id="test_status" name="test_status" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('test_status') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Status</option>
                            <option value="ordered" {{ old('test_status') === 'ordered' ? 'selected' : '' }}>Ordered</option>
                            <option value="in_progress" {{ old('test_status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('test_status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('test_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="rejected" {{ old('test_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @if($errors->has('test_status'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('test_status') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="result_status" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Result Status <span style="color: #dc3545;">*</span></label>
                        <select id="result_status" name="result_status" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('result_status') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Result Status</option>
                            <option value="normal" {{ old('result_status') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="abnormal_high" {{ old('result_status') === 'abnormal_high' ? 'selected' : '' }}>Abnormal High</option>
                            <option value="abnormal_low" {{ old('result_status') === 'abnormal_low' ? 'selected' : '' }}>Abnormal Low</option>
                            <option value="critical_high" {{ old('result_status') === 'critical_high' ? 'selected' : '' }}>Critical High</option>
                            <option value="critical_low" {{ old('result_status') === 'critical_low' ? 'selected' : '' }}>Critical Low</option>
                            <option value="inconclusive" {{ old('result_status') === 'inconclusive' ? 'selected' : '' }}>Inconclusive</option>
                            <option value="pending" {{ old('result_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        @if($errors->has('result_status'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('result_status') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('employee.patients.lab-results', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px;">Cancel</a>
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; cursor: pointer;">Save Lab Result</button>
            </div>
        </form>
    </div>

    <script>
        // Show/hide other sample type field
        document.getElementById('sample_type').addEventListener('change', function() {
            const otherField = document.getElementById('other_sample_type');
            if (this.value === 'other') {
                otherField.style.display = 'block';
            } else {
                otherField.style.display = 'none';
            }
        });
    </script>
</div>
@endsection
