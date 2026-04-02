@extends('employee.layouts.app')

@section('title', 'Add Delivery Record - ' . $patient->name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Add Delivery Record</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <a href="{{ route('employee.patients.delivery-records', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back to Records</a>
    </div>

    <!-- Form -->
    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd;">
        <form method="POST" action="{{ route('employee.patients.store-delivery-record', $patient->id) }}">
            @csrf
            <!-- Providers & Basic Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Providers & Basic Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="attending_provider_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Attending Provider <span style="color: #dc3545;">*</span></label>
                        <select id="attending_provider_id" name="attending_provider_id" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('attending_provider_id') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Attending Provider</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('attending_provider_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('attending_provider_id'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('attending_provider_id') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="delivering_provider_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Delivering Provider</label>
                        <select id="delivering_provider_id" name="delivering_provider_id"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Delivering Provider</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('delivering_provider_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="anesthesiologist_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Anesthesiologist</label>
                        <select id="anesthesiologist_id" name="anesthesiologist_id"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Anesthesiologist</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('anesthesiologist_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="rupture_of_membranes_type" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Rupture of Membranes Type</label>
                        <select id="rupture_of_membranes_type" name="rupture_of_membranes_type"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Type</option>
                            <option value="spontaneous" {{ old('rupture_of_membranes_type') == 'spontaneous' ? 'selected' : '' }}>Spontaneous</option>
                            <option value="artificial" {{ old('rupture_of_membranes_type') == 'artificial' ? 'selected' : '' }}>Artificial</option>
                            <option value="premature" {{ old('rupture_of_membranes_type') == 'premature' ? 'selected' : '' }}>Premature</option>
                        </select>
                    </div>
                    <div>
                        <label for="delivery_type" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Delivery Type</label>
                        <select id="delivery_type" name="delivery_type"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Delivery Type</option>
                            <option value="vaginal_spontaneous" {{ old('delivery_type') == 'vaginal_spontaneous' ? 'selected' : '' }}>Vaginal - Spontaneous</option>
                            <option value="vaginal_assisted" {{ old('delivery_type') == 'vaginal_assisted' ? 'selected' : '' }}>Vaginal - Assisted</option>
                            <option value="cesarean_section" {{ old('delivery_type') == 'cesarean_section' ? 'selected' : '' }}>Cesarean Section</option>
                            <option value="breech" {{ old('delivery_type') == 'breech' ? 'selected' : '' }}>Breech</option>
                            <option value="other" {{ old('delivery_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="admission_date_time" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Admission Date & Time <span style="color: #dc3545;">*</span></label>
                        <input type="datetime-local" id="admission_date_time" name="admission_date_time" value="{{ old('admission_date_time') }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('admission_date_time') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('admission_date_time'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('admission_date_time') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="labor_onset_date_time" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Labor Onset Date & Time</label>
                        <input type="datetime-local" id="labor_onset_date_time" name="labor_onset_date_time" value="{{ old('labor_onset_date_time') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="rupture_of_membranes_date_time" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Rupture of Membranes Date & Time</label>
                        <input type="datetime-local" id="rupture_of_membranes_date_time" name="rupture_of_membranes_date_time" value="{{ old('rupture_of_membranes_date_time') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="delivery_date_time" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Delivery Date & Time <span style="color: #dc3545;">*</span></label>
                        <input type="datetime-local" id="delivery_date_time" name="delivery_date_time" value="{{ old('delivery_date_time') }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('delivery_date_time') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('delivery_date_time'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('delivery_date_time') }}</div>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="delivery_place" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Delivery Place</label>
                    <select id="delivery_place" name="delivery_place"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        <option value="">Select Place</option>
                        <option value="hospital" {{ old('delivery_place') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                        <option value="birthing_center" {{ old('delivery_place') == 'birthing_center' ? 'selected' : '' }}>Birthing Center</option>
                        <option value="home" {{ old('delivery_place') == 'home' ? 'selected' : '' }}>Home</option>
                        <option value="other" {{ old('delivery_place') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <!-- Prenatal History & Risk Factors -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Prenatal History & Risk Factors</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="gravida" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Gravida</label>
                        <input type="number" id="gravida" name="gravida" value="{{ old('gravida') }}" min="1" placeholder="Number of pregnancies"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="para" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Para</label>
                        <input type="number" id="para" name="para" value="{{ old('para') }}" min="0" placeholder="Number of births"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="living_children" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Living Children</label>
                        <input type="number" id="living_children" name="living_children" value="{{ old('living_children') }}" min="0" placeholder="Number of living children"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="prenatal_history" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Prenatal History</label>
                    <textarea id="prenatal_history" name="prenatal_history" rows="3" placeholder="Summary of prenatal care and history"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('prenatal_history') }}</textarea>
                </div>

                <div>
                    <label for="risk_factors" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Risk Factors</label>
                    <textarea id="risk_factors" name="risk_factors" rows="3" placeholder="Any risk factors identified"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('risk_factors') }}</textarea>
                </div>
            </div>

            <!-- Labor Progress -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #17a2b8; padding-bottom: 10px;">Labor Progress</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="labor_duration_hours" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Labor Duration (Hours)</label>
                        <input type="number" id="labor_duration_hours" name="labor_duration_hours" value="{{ old('labor_duration_hours') }}" min="0" placeholder="Total hours"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="labor_duration_minutes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Labor Duration (Minutes)</label>
                        <input type="number" id="labor_duration_minutes" name="labor_duration_minutes" value="{{ old('labor_duration_minutes') }}" min="0" max="59" placeholder="Additional minutes"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="presentation" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Presentation</label>
                        <select id="presentation" name="presentation"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Presentation</option>
                            <option value="cephalic" {{ old('presentation') == 'cephalic' ? 'selected' : '' }}>Cephalic</option>
                            <option value="breech" {{ old('presentation') == 'breech' ? 'selected' : '' }}>Breech</option>
                            <option value="transverse" {{ old('presentation') == 'transverse' ? 'selected' : '' }}>Transverse</option>
                            <option value="other" {{ old('presentation') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="position" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Position</label>
                        <select id="position" name="position"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Position</option>
                            <option value="occiput_anterior" {{ old('position') == 'occiput_anterior' ? 'selected' : '' }}>Occiput Anterior</option>
                            <option value="occiput_posterior" {{ old('position') == 'occiput_posterior' ? 'selected' : '' }}>Occiput Posterior</option>
                            <option value="occiput_transverse" {{ old('position') == 'occiput_transverse' ? 'selected' : '' }}>Occiput Transverse</option>
                            <option value="other" {{ old('position') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="labor_progress" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Labor Progress</label>
                    <textarea id="labor_progress" name="labor_progress" rows="3" placeholder="Description of labor progress"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('labor_progress') }}</textarea>
                </div>

                <div>
                    <label for="labor_complications" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Labor Complications</label>
                    <textarea id="labor_complications" name="labor_complications" rows="3" placeholder="Any complications during labor"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('labor_complications') }}</textarea>
                </div>

                <!-- Delivery Details -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #dc3545; padding-bottom: 10px;">Delivery Details</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="episiotomy_performed" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Episiotomy Performed</label>
                        <select id="episiotomy_performed" name="episiotomy_performed"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="1" {{ old('episiotomy_performed') == '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('episiotomy_performed') === '0' || !old('episiotomy_performed') ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label for="episiotomy_degree" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Episiotomy Degree</label>
                        <select id="episiotomy_degree" name="episiotomy_degree"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Degree</option>
                            <option value="first_degree" {{ old('episiotomy_degree') == 'first_degree' ? 'selected' : '' }}>First Degree</option>
                            <option value="second_degree" {{ old('episiotomy_degree') == 'second_degree' ? 'selected' : '' }}>Second Degree</option>
                            <option value="third_degree" {{ old('episiotomy_degree') == 'third_degree' ? 'selected' : '' }}>Third Degree</option>
                            <option value="fourth_degree" {{ old('episiotomy_degree') == 'fourth_degree' ? 'selected' : '' }}>Fourth Degree</option>
                        </select>
                    </div>
                    <div>
                        <label for="perineal_tear" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Perineal Tear</label>
                        <select id="perineal_tear" name="perineal_tear"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="none" {{ old('perineal_tear') == 'none' ? 'selected' : '' }}>None</option>
                            <option value="first_degree" {{ old('perineal_tear') == 'first_degree' ? 'selected' : '' }}>First Degree</option>
                            <option value="second_degree" {{ old('perineal_tear') == 'second_degree' ? 'selected' : '' }}>Second Degree</option>
                            <option value="third_degree" {{ old('perineal_tear') == 'third_degree' ? 'selected' : '' }}>Third Degree</option>
                            <option value="fourth_degree" {{ old('perineal_tear') == 'fourth_degree' ? 'selected' : '' }}>Fourth Degree</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="anesthesia_type" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Anesthesia Type</label>
                        <select id="anesthesia_type" name="anesthesia_type"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Type</option>
                            <option value="none" {{ old('anesthesia_type') == 'none' ? 'selected' : '' }}>None</option>
                            <option value="epidural" {{ old('anesthesia_type') == 'epidural' ? 'selected' : '' }}>Epidural</option>
                            <option value="spinal" {{ old('anesthesia_type') == 'spinal' ? 'selected' : '' }}>Spinal</option>
                            <option value="general" {{ old('anesthesia_type') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="local" {{ old('anesthesia_type') == 'local' ? 'selected' : '' }}>Local</option>
                            <option value="other" {{ old('anesthesia_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div></div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="anesthesia_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Anesthesia Notes</label>
                    <textarea id="anesthesia_notes" name="anesthesia_notes" rows="2" placeholder="Notes about anesthesia administration"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('anesthesia_notes') }}</textarea>
                </div>

                <div>
                    <label for="delivery_complications" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Delivery Complications</label>
                    <textarea id="delivery_complications" name="delivery_complications" rows="3" placeholder="Any complications during delivery"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('delivery_complications') }}</textarea>
                </div>
            </div>

            <!-- Newborn Information -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #6f42c1; padding-bottom: 10px;">Newborn Information</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="newborn_gender" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Gender</label>
                        <select id="newborn_gender" name="newborn_gender"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('newborn_gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('newborn_gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="intersex" {{ old('newborn_gender') == 'intersex' ? 'selected' : '' }}>Intersex</option>
                        </select>
                    </div>
                    <div>
                        <label for="newborn_weight" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Weight (kg)</label>
                        <input type="number" id="newborn_weight" name="newborn_weight" value="{{ old('newborn_weight') }}" step="0.01" min="0" placeholder="e.g., 3.2"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="newborn_length" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Length (cm)</label>
                        <input type="number" id="newborn_length" name="newborn_length" value="{{ old('newborn_length') }}" step="0.1" min="0" placeholder="e.g., 50.5"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="newborn_apgar_1min" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">APGAR Score (1 min)</label>
                        <input type="number" id="newborn_apgar_1min" name="newborn_apgar_1min" value="{{ old('newborn_apgar_1min') }}" min="0" max="10" placeholder="0-10"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="newborn_apgar_5min" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">APGAR Score (5 min)</label>
                        <input type="number" id="newborn_apgar_5min" name="newborn_apgar_5min" value="{{ old('newborn_apgar_5min') }}" min="0" max="10" placeholder="0-10"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="newborn_apgar_10min" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">APGAR Score (10 min)</label>
                        <input type="number" id="newborn_apgar_10min" name="newborn_apgar_10min" value="{{ old('newborn_apgar_10min') }}" min="0" max="10" placeholder="0-10"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="newborn_condition" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Condition</label>
                        <select id="newborn_condition" name="newborn_condition"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Condition</option>
                            <option value="healthy" {{ old('newborn_condition') == 'healthy' ? 'selected' : '' }}>Healthy</option>
                            <option value="needs_attention" {{ old('newborn_condition') == 'needs_attention' ? 'selected' : '' }}>Needs Attention</option>
                            <option value="critical" {{ old('newborn_condition') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div></div>
                </div>

                <div>
                    <label for="newborn_complications" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Newborn Complications</label>
                    <textarea id="newborn_complications" name="newborn_complications" rows="2" placeholder="Any newborn complications"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('newborn_complications') }}</textarea>
                </div>

                <!-- Placenta & Postpartum Care -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #fd7e14; padding-bottom: 10px;">Placenta & Postpartum Care</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="placenta_delivery" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Placenta Delivery</label>
                        <select id="placenta_delivery" name="placenta_delivery"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Method</option>
                            <option value="spontaneous" {{ old('placenta_delivery') == 'spontaneous' ? 'selected' : '' }}>Spontaneous</option>
                            <option value="manual" {{ old('placenta_delivery') == 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="d_and_c" {{ old('placenta_delivery') == 'd_and_c' ? 'selected' : '' }}>D&C</option>
                        </select>
                    </div>
                    <div>
                        <label for="placenta_complete" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Placenta Complete</label>
                        <select id="placenta_complete" name="placenta_complete"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="1" {{ old('placenta_complete') ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('placenta_complete') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label for="estimated_blood_loss" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Estimated Blood Loss (mL)</label>
                        <input type="number" id="estimated_blood_loss" name="estimated_blood_loss" value="{{ old('estimated_blood_loss') }}" min="0" placeholder="e.g., 300"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="blood_pressure_systolic" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Blood Pressure Systolic</label>
                        <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" value="{{ old('blood_pressure_systolic') }}" min="0" placeholder="120"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="blood_pressure_diastolic" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Blood Pressure Diastolic</label>
                        <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" value="{{ old('blood_pressure_diastolic') }}" min="0" placeholder="80"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="heart_rate" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Heart Rate (bpm)</label>
                        <input type="number" id="heart_rate" name="heart_rate" value="{{ old('heart_rate') }}" min="0" placeholder="72"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="temperature" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Temperature (°C)</label>
                        <input type="number" id="temperature" name="temperature" value="{{ old('temperature') }}" step="0.1" min="0" placeholder="36.5"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div></div>
                </div>

                <div>
                    <label for="placenta_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Placenta Notes</label>
                    <textarea id="placenta_notes" name="placenta_notes" rows="2" placeholder="Notes about placenta delivery"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('placenta_notes') }}</textarea>
                </div>
            </div>

            <!-- Postpartum Care & Discharge -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #e83e8c; padding-bottom: 10px;">Postpartum Care & Discharge</h3>

                <div style="margin-bottom: 20px;">
                    <label for="postpartum_care" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Postpartum Care</label>
                    <textarea id="postpartum_care" name="postpartum_care" rows="3" placeholder="Immediate postpartum care provided"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('postpartum_care') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="medications_administered" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Medications Administered</label>
                        <textarea id="medications_administered" name="medications_administered" rows="2" placeholder="Medications given during delivery"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('medications_administered') }}</textarea>
                    </div>
                    <div>
                        <label for="breastfeeding_initiation" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Breastfeeding Initiation</label>
                        <textarea id="breastfeeding_initiation" name="breastfeeding_initiation" rows="2" placeholder="Breastfeeding initiation details"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('breastfeeding_initiation') }}</textarea>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="expected_discharge_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Expected Discharge Date</label>
                        <input type="date" id="expected_discharge_date" name="expected_discharge_date" value="{{ old('expected_discharge_date') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div></div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="discharge_instructions" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Discharge Instructions</label>
                    <textarea id="discharge_instructions" name="discharge_instructions" rows="3" placeholder="Instructions for discharge"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('discharge_instructions') }}</textarea>
                </div>

                <div>
                    <label for="follow_up_instructions" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Follow-up Instructions</label>
                    <textarea id="follow_up_instructions" name="follow_up_instructions" rows="2" placeholder="Follow-up care instructions"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('follow_up_instructions') }}</textarea>
                </div>

                <!-- Summary & Notes -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #20c997; padding-bottom: 10px;">Summary & Notes</h3>

                <div style="margin-bottom: 20px;">
                    <label for="delivery_summary" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Delivery Summary</label>
                    <textarea id="delivery_summary" name="delivery_summary" rows="4" placeholder="Comprehensive summary of the delivery"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('delivery_summary') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="additional_notes" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Additional Notes</label>
                        <textarea id="additional_notes" name="additional_notes" rows="3" placeholder="Any additional notes or observations"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('additional_notes') }}</textarea>
                    </div>
                    <div>
                        <label for="quality_indicators" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Quality Indicators</label>
                        <textarea id="quality_indicators" name="quality_indicators" rows="3" placeholder="Quality indicators met or areas for improvement"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('quality_indicators') }}</textarea>
                    </div>
                </div>
            </div>
                    </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('employee.patients.delivery-records', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px;">Cancel</a>
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; cursor: pointer;">Save Delivery Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
