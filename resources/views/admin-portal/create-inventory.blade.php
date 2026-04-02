@extends('admin-portal.layouts.app')

@section('title', 'Add New Inventory Item')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-plus me-2"></i>Add New Inventory Item</h1>
                <p class="text-muted mb-0">Add a new item to your inventory</p>
            </div>
            <a href="{{ route('admin-portal.inventory') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Inventory
            </a>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-box me-2"></i>Item Information</h6>
    </div>
    <div class="card-body">
        <form id="inventoryForm" method="POST" action="{{ route('admin-portal.inventory.store') }}">
            @csrf

            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>

                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="item_code" class="form-label">Item Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('item_code') is-invalid @enderror" id="item_code" name="item_code" value="{{ old('item_code') }}" required>
                        <div class="form-text">Unique identifier for this item</div>
                        @error('item_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-tag me-2"></i>Classification</h6>

                    <div class="mb-3">
                        <label for="item_type" class="form-label">Item Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('item_type') is-invalid @enderror" id="item_type" name="item_type" required>
                            <option value="">Select Item Type</option>
                            <option value="medical_supply" {{ old('item_type') == 'medical_supply' ? 'selected' : '' }}>Medical Supply</option>
                            <option value="equipment" {{ old('item_type') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                            <option value="medication" {{ old('item_type') == 'medication' ? 'selected' : '' }}>Medication</option>
                            <option value="consumable" {{ old('item_type') == 'consumable' ? 'selected' : '' }}>Consumable</option>
                            <option value="durable_medical_equipment" {{ old('item_type') == 'durable_medical_equipment' ? 'selected' : '' }}>Durable Medical Equipment</option>
                            <option value="laboratory_supply" {{ old('item_type') == 'laboratory_supply' ? 'selected' : '' }}>Laboratory Supply</option>
                            <option value="office_supply" {{ old('item_type') == 'office_supply' ? 'selected' : '' }}>Office Supply</option>
                            <option value="other" {{ old('item_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('item_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="surgical_instruments" {{ old('category') == 'surgical_instruments' ? 'selected' : '' }}>Surgical Instruments</option>
                            <option value="diagnostic_equipment" {{ old('category') == 'diagnostic_equipment' ? 'selected' : '' }}>Diagnostic Equipment</option>
                            <option value="medications" {{ old('category') == 'medications' ? 'selected' : '' }}>Medications</option>
                            <option value="bandages_dressings" {{ old('category') == 'bandages_dressings' ? 'selected' : '' }}>Bandages & Dressings</option>
                            <option value="gloves_masks" {{ old('category') == 'gloves_masks' ? 'selected' : '' }}>Gloves & Masks</option>
                            <option value="syringes_needles" {{ old('category') == 'syringes_needles' ? 'selected' : '' }}>Syringes & Needles</option>
                            <option value="laboratory_supplies" {{ old('category') == 'laboratory_supplies' ? 'selected' : '' }}>Laboratory Supplies</option>
                            <option value="office_supplies" {{ old('category') == 'office_supplies' ? 'selected' : '' }}>Office Supplies</option>
                            <option value="furniture" {{ old('category') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Inventory Tracking -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-chart-bar me-2"></i>Inventory Tracking</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_quantity" class="form-label">Current Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('current_quantity') is-invalid @enderror" id="current_quantity" name="current_quantity" value="{{ old('current_quantity', 0) }}" min="0" required>
                                @error('current_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_of_measure" class="form-label">Unit of Measure <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('unit_of_measure') is-invalid @enderror" id="unit_of_measure" name="unit_of_measure" value="{{ old('unit_of_measure', 'pieces') }}" placeholder="pieces, boxes, bottles, etc." required>
                                @error('unit_of_measure')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="minimum_quantity" class="form-label">Minimum Quantity</label>
                                <input type="number" class="form-control @error('minimum_quantity') is-invalid @enderror" id="minimum_quantity" name="minimum_quantity" value="{{ old('minimum_quantity') }}" min="0">
                                <div class="form-text">Reorder point</div>
                                @error('minimum_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="maximum_quantity" class="form-label">Maximum Quantity</label>
                                <input type="number" class="form-control @error('maximum_quantity') is-invalid @enderror" id="maximum_quantity" name="maximum_quantity" value="{{ old('maximum_quantity') }}" min="0">
                                <div class="form-text">Maximum stock level</div>
                                @error('maximum_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt me-2"></i>Location & Storage</h6>

                    <div class="mb-3">
                        <label for="storage_location" class="form-label">Storage Location</label>
                        <input type="text" class="form-control @error('storage_location') is-invalid @enderror" id="storage_location" name="storage_location" value="{{ old('storage_location') }}" placeholder="Main storage, Pharmacy, etc.">
                        @error('storage_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number</label>
                                <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" value="{{ old('room_number') }}" placeholder="Room 101">
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cabinet_drawer" class="form-label">Cabinet/Drawer</label>
                                <input type="text" class="form-control @error('cabinet_drawer') is-invalid @enderror" id="cabinet_drawer" name="cabinet_drawer" value="{{ old('cabinet_drawer') }}" placeholder="Cabinet A, Drawer 3">
                                @error('cabinet_drawer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="storage_conditions" class="form-label">Storage Conditions</label>
                        <select class="form-select @error('storage_conditions') is-invalid @enderror" id="storage_conditions" name="storage_conditions">
                            <option value="">Select Storage Conditions</option>
                            <option value="room_temperature" {{ old('storage_conditions') == 'room_temperature' ? 'selected' : '' }}>Room Temperature</option>
                            <option value="refrigerated" {{ old('storage_conditions') == 'refrigerated' ? 'selected' : '' }}>Refrigerated</option>
                            <option value="frozen" {{ old('storage_conditions') == 'frozen' ? 'selected' : '' }}>Frozen</option>
                            <option value="controlled_room" {{ old('storage_conditions') == 'controlled_room' ? 'selected' : '' }}>Controlled Room</option>
                            <option value="dark_place" {{ old('storage_conditions') == 'dark_place' ? 'selected' : '' }}>Dark Place</option>
                            <option value="other" {{ old('storage_conditions') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('storage_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-dollar-sign me-2"></i>Financial Information</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_cost" class="form-label">Unit Cost (₱)</label>
                                <input type="number" class="form-control @error('unit_cost') is-invalid @enderror" id="unit_cost" name="unit_cost" value="{{ old('unit_cost') }}" min="0" step="0.01">
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="selling_price" class="form-label">Selling Price (₱)</label>
                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" min="0" step="0.01">
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}">
                        @error('supplier_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="supplier_contact" class="form-label">Supplier Contact</label>
                        <input type="text" class="form-control @error('supplier_contact') is-invalid @enderror" id="supplier_contact" name="supplier_contact" value="{{ old('supplier_contact') }}" placeholder="Phone or email">
                        @error('supplier_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-calendar-alt me-2"></i>Regulatory & Compliance</h6>

                    <div class="mb-3">
                        <label for="expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
                        @error('expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="batch_lot_number" class="form-label">Batch/Lot Number</label>
                        <input type="text" class="form-control @error('batch_lot_number') is-invalid @enderror" id="batch_lot_number" name="batch_lot_number" value="{{ old('batch_lot_number') }}">
                        @error('batch_lot_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="fda_registration_number" class="form-label">FDA Registration Number</label>
                        <input type="text" class="form-control @error('fda_registration_number') is-invalid @enderror" id="fda_registration_number" name="fda_registration_number" value="{{ old('fda_registration_number') }}">
                        @error('fda_registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="requires_prescription" name="requires_prescription" value="1" {{ old('requires_prescription') ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_prescription">
                                Requires Prescription
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts & Notifications -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-bell me-2"></i>Alerts & Notifications</h6>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="low_stock_alert" name="low_stock_alert" value="1" {{ old('low_stock_alert', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="low_stock_alert">
                                Enable Low Stock Alerts
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="expiry_alert" name="expiry_alert" value="1" {{ old('expiry_alert', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="expiry_alert">
                                Enable Expiry Alerts
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alert_before_expiry_days" class="form-label">Alert Before Expiry (Days)</label>
                        <input type="number" class="form-control @error('alert_before_expiry_days') is-invalid @enderror" id="alert_before_expiry_days" name="alert_before_expiry_days" value="{{ old('alert_before_expiry_days', 30) }}" min="1" max="365">
                        @error('alert_before_expiry_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>Additional Information</h6>

                    <div class="mb-3">
                        <label for="manufacturer" class="form-label">Manufacturer</label>
                        <input type="text" class="form-control @error('manufacturer') is-invalid @enderror" id="manufacturer" name="manufacturer" value="{{ old('manufacturer') }}">
                        @error('manufacturer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="model_number" class="form-label">Model Number</label>
                        <input type="text" class="form-control @error('model_number') is-invalid @enderror" id="model_number" name="model_number" value="{{ old('model_number') }}">
                        @error('model_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Special Instructions -->
            <div class="mb-4">
                <h6 class="text-primary mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Special Instructions</h6>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="special_handling_instructions" class="form-label">Special Handling Instructions</label>
                            <textarea class="form-control @error('special_handling_instructions') is-invalid @enderror" id="special_handling_instructions" name="special_handling_instructions" rows="3">{{ old('special_handling_instructions') }}</textarea>
                            @error('special_handling_instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="internal_notes" class="form-label">Internal Notes</label>
                            <textarea class="form-control @error('internal_notes') is-invalid @enderror" id="internal_notes" name="internal_notes" rows="3">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin-portal.inventory') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-times me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-admin-primary">
                    <i class="fas fa-save me-1"></i>Create Inventory Item
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('inventoryForm').addEventListener('submit', function(e) {
    // Clear previous errors
    clearValidationErrors();

    let isValid = true;

    // Required field validations
    const requiredFields = [
        { id: 'name', name: 'Item Name' },
        { id: 'item_code', name: 'Item Code' },
        { id: 'item_type', name: 'Item Type' },
        { id: 'category', name: 'Category' },
        { id: 'current_quantity', name: 'Current Quantity' },
        { id: 'unit_of_measure', name: 'Unit of Measure' }
    ];

    requiredFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (!element.value.trim()) {
            showValidationError(field.id, `${field.name} is required.`);
            isValid = false;
        }
    });

    // Numeric validations
    const numericFields = [
        { id: 'current_quantity', name: 'Current Quantity', min: 0 },
        { id: 'minimum_quantity', name: 'Minimum Quantity', min: 0 },
        { id: 'maximum_quantity', name: 'Maximum Quantity', min: 0 },
        { id: 'unit_cost', name: 'Unit Cost', min: 0 },
        { id: 'selling_price', name: 'Selling Price', min: 0 },
        { id: 'alert_before_expiry_days', name: 'Alert Before Expiry Days', min: 1, max: 365 }
    ];

    numericFields.forEach(field => {
        const element = document.getElementById(field.id);
        if (element.value && (isNaN(element.value) || element.value < field.min || (field.max && element.value > field.max))) {
            showValidationError(field.id, `${field.name} must be a valid number${field.min !== undefined ? ` >= ${field.min}` : ''}${field.max ? ` <= ${field.max}` : ''}.`);
            isValid = false;
        }
    });

    // Date validation
    const expiryDate = document.getElementById('expiry_date');
    if (expiryDate.value) {
        const today = new Date().toISOString().split('T')[0];
        if (expiryDate.value < today) {
            showValidationError('expiry_date', 'Expiry date must be in the future.');
            isValid = false;
        }
    }

    // Item code uniqueness check (basic)
    const itemCode = document.getElementById('item_code').value;
    if (itemCode && !/^[A-Za-z0-9_-]+$/.test(itemCode)) {
        showValidationError('item_code', 'Item code can only contain letters, numbers, hyphens, and underscores.');
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
        // Scroll to first error
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

function showValidationError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const feedback = field.parentNode.querySelector('.invalid-feedback') || document.createElement('div');

    field.classList.add('is-invalid');
    feedback.className = 'invalid-feedback d-block';
    feedback.textContent = message;

    if (!field.parentNode.querySelector('.invalid-feedback')) {
        field.parentNode.appendChild(feedback);
    }
}

function clearValidationErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.classList.remove('d-block'));
}
</script>
@endsection
