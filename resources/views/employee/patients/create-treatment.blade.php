@extends('employee.layouts.app')

@section('title', 'Create Prescription - ' . $patient->name)

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin: 0; font-size: 28px;">Create Prescription</h1>
            <p style="color: #666; margin: 5px 0 0 0;">Patient: {{ $patient->name }}</p>
        </div>
        <a href="{{ route('employee.patients.treatments', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 14px;">← Back to Prescriptions</a>
    </div>

    <!-- Form -->
    <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #ddd;">
        <form method="POST" action="{{ route('employee.patients.store-treatment', $patient->id) }}">
            @csrf

            <!-- Medication Selection -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Medication Details</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="inventory_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Medication <span style="color: #dc3545;">*</span></label>
                        <select id="inventory_id" name="inventory_id" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('inventory_id') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Medication</option>
                            @foreach($inventory as $item)
                                <option value="{{ $item->id }}" {{ old('inventory_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} ({{ $item->manufacturer ?? 'Generic' }}) - Stock: {{ $item->current_quantity }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('inventory_id'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('inventory_id') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="quantity_dispensed" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Quantity to Dispense <span style="color: #dc3545;">*</span></label>
                        <input type="number" id="quantity_dispensed" name="quantity_dispensed" value="{{ old('quantity_dispensed') }}" min="1" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('quantity_dispensed') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('quantity_dispensed'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('quantity_dispensed') }}</div>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="dosage" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Dosage</label>
                        <input type="text" id="dosage" name="dosage" value="{{ old('dosage') }}" placeholder="e.g., 500mg"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="frequency" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Frequency</label>
                        <input type="text" id="frequency" name="frequency" value="{{ old('frequency') }}" placeholder="e.g., twice daily"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label for="route" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Route</label>
                        <select id="route" name="route"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Route</option>
                            <option value="oral" {{ old('route') == 'oral' ? 'selected' : '' }}>Oral</option>
                            <option value="intravenous" {{ old('route') == 'intravenous' ? 'selected' : '' }}>Intravenous</option>
                            <option value="intramuscular" {{ old('route') == 'intramuscular' ? 'selected' : '' }}>Intramuscular</option>
                            <option value="subcutaneous" {{ old('route') == 'subcutaneous' ? 'selected' : '' }}>Subcutaneous</option>
                            <option value="topical" {{ old('route') == 'topical' ? 'selected' : '' }}>Topical</option>
                            <option value="inhalation" {{ old('route') == 'inhalation' ? 'selected' : '' }}>Inhalation</option>
                            <option value="other" {{ old('route') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Prescription Details -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #28a745; padding-bottom: 10px;">Prescription Details</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="prescribed_by" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Prescribed By <span style="color: #dc3545;">*</span></label>
                        <select id="prescribed_by" name="prescribed_by" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('prescribed_by') ? 'border-color: #dc3545;' : '' }}">
                            <option value="">Select Prescriber</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ old('prescribed_by') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('prescribed_by'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('prescribed_by') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="prescribed_date" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Prescription Date <span style="color: #dc3545;">*</span></label>
                        <input type="date" id="prescribed_date" name="prescribed_date" value="{{ old('prescribed_date', date('Y-m-d')) }}" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('prescribed_date') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('prescribed_date'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('prescribed_date') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="duration_days" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Duration (Days)</label>
                        <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days') }}" min="1"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="priority" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Priority <span style="color: #dc3545;">*</span></label>
                        <select id="priority" name="priority" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('priority') ? 'border-color: #dc3545;' : '' }}">
                            <option value="routine" {{ old('priority', 'routine') == 'routine' ? 'selected' : '' }}>Routine</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="stat" {{ old('priority') == 'stat' ? 'selected' : '' }}>STAT</option>
                        </select>
                        @if($errors->has('priority'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('priority') }}</div>
                        @endif
                    </div>
                    <div>
                        <label for="indication" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Indication <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="indication" name="indication" value="{{ old('indication') }}" required placeholder="Reason for prescription"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; {{ $errors->has('indication') ? 'border-color: #dc3545;' : '' }}">
                        @if($errors->has('indication'))
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $errors->first('indication') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Special Instructions -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; margin: 0 0 20px 0; font-size: 18px; border-bottom: 2px solid #6f42c1; padding-bottom: 10px;">Special Instructions</h3>

                <div>
                    <label for="special_instructions" style="display: block; margin-bottom: 5px; font-weight: bold; color: #333;">Special Instructions</label>
                    <textarea id="special_instructions" name="special_instructions" rows="4"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ old('special_instructions') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div style="border-top: 1px solid #eee; padding-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('employee.patients.treatments', $patient->id) }}" style="background: #6c757d; color: white; text-decoration: none; padding: 12px 24px; border-radius: 5px; font-size: 14px;">Cancel</a>
                <button type="submit" style="background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 5px; font-size: 14px; cursor: pointer;">Create Prescription</button>
            </div>
        </form>
    </div>
</div>
@endsection
