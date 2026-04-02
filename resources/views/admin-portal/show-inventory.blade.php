@extends('admin-portal.layouts.app')

@section('title', 'Inventory Item Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-box me-2"></i>{{ $inventory->name }}</h1>
                <p class="text-muted mb-0">Item Code: {{ $inventory->item_code }}</p>
            </div>
            <div>
                <a href="{{ route('admin-portal.inventory.edit', $inventory->id) }}" class="btn btn-admin-primary me-2">
                    <i class="fas fa-edit me-1"></i>Edit Item
                </a>
                <a href="{{ route('admin-portal.inventory') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Inventory
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Status Alerts -->
@if($inventory->isLowStock)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Low Stock Alert:</strong> Current quantity ({{ $inventory->current_quantity }} {{ $inventory->unit_of_measure }}) is below minimum level ({{ $inventory->minimum_quantity }} {{ $inventory->unit_of_measure }}).
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($inventory->isExpired)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Expired Item:</strong> This item expired on {{ $inventory->expiry_date->format('M d, Y') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@elseif($inventory->isExpiringSoon)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-clock me-2"></i>
        <strong>Expiring Soon:</strong> This item will expire on {{ $inventory->expiry_date->format('M d, Y') }}
        @php
            $days = ceil(now()->diffInSeconds($inventory->expiry_date) / 86400);
        @endphp
        ({{ $days }} day{{ $days != 1 ? 's' : '' }} remaining).
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($inventory->current_quantity == 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-2"></i>
        <strong>Out of Stock:</strong> This item is currently out of stock.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Main Information -->
    <div class="col-lg-8 mb-4">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Item Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Name</label>
                            <p class="mb-0">{{ $inventory->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Code</label>
                            <p class="mb-0"><code>{{ $inventory->item_code }}</code></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $inventory->item_type)) }}</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $inventory->category)) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="mb-0">
                                @switch($inventory->status)
                                    @case('active')
                                        <span class="badge bg-success">Active</span>
                                        @break
                                    @case('inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                        @break
                                    @case('discontinued')
                                        <span class="badge bg-warning">Discontinued</span>
                                        @break
                                    @case('under_maintenance')
                                        <span class="badge bg-info">Under Maintenance</span>
                                        @break
                                    @case('out_of_stock')
                                        <span class="badge bg-danger">Out of Stock</span>
                                        @break
                                    @case('expired')
                                        <span class="badge bg-dark">Expired</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Stock</label>
                            <p class="mb-0">
                                <span class="h5 {{ $inventory->isLowStock ? 'text-warning' : ($inventory->current_quantity == 0 ? 'text-danger' : 'text-success') }}">
                                    {{ $inventory->current_quantity }} {{ $inventory->unit_of_measure }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Stock Levels</label>
                            <p class="mb-0">
                                @if($inventory->minimum_quantity)
                                    Min: {{ $inventory->minimum_quantity }}
                                    @if($inventory->maximum_quantity)
                                        | Max: {{ $inventory->maximum_quantity }}
                                    @endif
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Expiry Date</label>
                            <p class="mb-0">
                                @if($inventory->expiry_date)
                                    @if($inventory->isExpired)
                                        <span class="text-danger">{{ $inventory->expiry_date->format('M d, Y') }}</span>
                                    @elseif($inventory->isExpiringSoon)
                                        <span class="text-warning">{{ $inventory->expiry_date->format('M d, Y') }}</span>
                                    @else
                                        {{ $inventory->expiry_date->format('M d, Y') }}
                                    @endif
                                @else
                                    <span class="text-muted">No expiry</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($inventory->description)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p class="mb-0">{{ $inventory->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Financial Information -->
        <div class="admin-card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Financial Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit Cost</label>
                            <p class="mb-0 h5">{{ $inventory->formatted_unit_cost }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Selling Price</label>
                            <p class="mb-0 h5">{{ $inventory->formatted_selling_price }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Supplier</label>
                            <p class="mb-0">
                                @if($inventory->supplier_name)
                                    {{ $inventory->supplier_name }}
                                    @if($inventory->supplier_contact)
                                        <br><small class="text-muted">{{ $inventory->supplier_contact }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location & Storage -->
        <div class="admin-card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location & Storage</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Storage Location</label>
                            <p class="mb-0">{{ $inventory->storage_location ?: 'Not specified' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Room Number</label>
                            <p class="mb-0">{{ $inventory->room_number ?: 'Not specified' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cabinet/Drawer</label>
                            <p class="mb-0">{{ $inventory->cabinet_drawer ?: 'Not specified' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Storage Conditions</label>
                            <p class="mb-0">
                                @if($inventory->storage_conditions)
                                    {{ ucfirst(str_replace('_', ' ', $inventory->storage_conditions)) }}
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Special Instructions -->
        @if($inventory->special_handling_instructions || $inventory->internal_notes)
            <div class="admin-card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Special Instructions</h6>
                </div>
                <div class="card-body">
                    @if($inventory->special_handling_instructions)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Special Handling Instructions</label>
                            <p class="mb-0">{{ $inventory->special_handling_instructions }}</p>
                        </div>
                    @endif
                    @if($inventory->internal_notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Internal Notes</label>
                            <p class="mb-0">{{ $inventory->internal_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="admin-card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin-portal.inventory.edit', $inventory->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>Edit Item
                    </a>
                    <button class="btn btn-outline-success" onclick="updateStock()">
                        <i class="fas fa-plus me-1"></i>Update Stock
                    </button>
                    <button class="btn btn-outline-info" onclick="viewMovements()">
                        <i class="fas fa-history me-1"></i>View Movements
                    </button>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Additional Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Manufacturer</label>
                    <p class="mb-0">{{ $inventory->manufacturer ?: 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Model Number</label>
                    <p class="mb-0">{{ $inventory->model_number ?: 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Serial Number</label>
                    <p class="mb-0">{{ $inventory->serial_number ?: 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Batch/Lot Number</label>
                    <p class="mb-0">{{ $inventory->batch_lot_number ?: 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">FDA Registration</label>
                    <p class="mb-0">{{ $inventory->fda_registration_number ?: 'Not specified' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Requires Prescription</label>
                    <p class="mb-0">
                        @if($inventory->requires_prescription)
                            <span class="badge bg-warning">Yes</span>
                        @else
                            <span class="badge bg-success">No</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Usage Statistics -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Usage Statistics</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Usage Count</label>
                    <p class="mb-0 h5">{{ $inventory->usage_count }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Used</label>
                    <p class="mb-0">
                        @if($inventory->last_used_date)
                            {{ $inventory->last_used_date->format('M d, Y') }}
                        @else
                            <span class="text-muted">Never used</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Last Inventory Check</label>
                    <p class="mb-0">
                        @if($inventory->last_inventory_check)
                            {{ $inventory->last_inventory_check->format('M d, Y') }}
                        @else
                            <span class="text-muted">Never checked</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Movements -->
        @if($recentMovements->count() > 0)
            <div class="admin-card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Movements</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($recentMovements as $movement)
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-{{ $movement->movement_type == 'stock_in' ? 'success' : 'primary' }}"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">{{ $movement->created_at->format('M d, Y H:i') }}</small>
                                    <div>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}</strong>
                                        @if($movement->quantity_moved > 0)
                                            <span class="text-success">+{{ $movement->quantity_moved }}</span>
                                        @else
                                            <span class="text-danger">{{ $movement->quantity_moved }}</span>
                                        @endif
                                        <br>
                                        <small>New total: {{ $movement->new_quantity }} {{ $inventory->unit_of_measure }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-left: 15px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 0.9rem;
}

.timeline-content small {
    display: block;
    margin-bottom: 2px;
}
</style>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStockModalLabel">Update Stock for {{ $inventory->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin-portal.inventory.update-stock', $inventory->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_quantity" class="form-label">Current Quantity</label>
                        <input type="text" class="form-control" id="current_quantity" value="{{ $inventory->current_quantity }} {{ $inventory->unit_of_measure }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="new_quantity" class="form-label">New Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="new_quantity" name="new_quantity" min="0" value="{{ $inventory->current_quantity }}" required>
                        <div class="form-text">Enter the new stock quantity.</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optional notes about this stock update"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStock() {
    // Show the update stock modal
    var modal = new bootstrap.Modal(document.getElementById('updateStockModal'));
    modal.show();
}

function viewMovements() {
    // Redirect to movements page
    window.location.href = '{{ route("admin-portal.inventory.movements", $inventory->id) }}';
}
</script>
@endsection
