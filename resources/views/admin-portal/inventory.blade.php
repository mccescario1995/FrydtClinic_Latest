@extends('admin-portal.layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-boxes me-2"></i>Inventory Management</h1>
                <p class="text-muted mb-0">Manage medical supplies, equipment, and inventory items</p>
            </div>
            <a href="{{ route('admin-portal.inventory.create') }}" class="btn btn-admin-primary">
                <i class="fas fa-plus me-1"></i>Add New Item
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="h4 mb-0 text-primary">{{ $totalItems }}</div>
                <small class="text-muted">Total Items</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="h4 mb-0 text-success">{{ $activeItems }}</div>
                <small class="text-muted">Active Items</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="h4 mb-0 text-warning">{{ $lowStockItems }}</div>
                <small class="text-muted">Low Stock</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="h4 mb-0 text-danger">{{ $outOfStockItems }}</div>
                <small class="text-muted">Out of Stock</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="h4 mb-0 text-info">{{ $expiringSoonItems }}</div>
                <small class="text-muted">Expiring Soon</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="h4 mb-0 text-secondary">{{ $expiredItems }}</div>
                <small class="text-muted">Expired</small>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Alerts -->
@if($expiredItemsCollection->count() > 0)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Expired Items:</strong> {{ $expiredItemsCollection->count() }} item(s) have expired.
    <a href="{{ route('admin-portal.inventory') }}?item_type=&category=&stock_status=expired&search=" class="alert-link">View</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($expiringSoonItemsCollection->count() > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Items Expiring Soon:</strong> {{ $expiringSoonItemsCollection->count() }} item(s) will expire within the alert period.
    <a href="{{ route('admin-portal.inventory') }}?item_type=&category=&stock_status=expiring_soon&search=" class="alert-link">View</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($lowStockItemsCollection->count() > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Low Stock Items:</strong> {{ $lowStockItemsCollection->count() }} item(s) are running low on stock.
    <a href="{{ route('admin-portal.inventory') }}?item_type=&category=&stock_status=low_stock&search=" class="alert-link">View</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($outOfStockItemsCollection->count() > 0)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Out of Stock Items:</strong> {{ $outOfStockItemsCollection->count() }} item(s) are currently out of stock.
    <a href="{{ route('admin-portal.inventory') }}?item_type=&category=&stock_status=out_of_stock&search=" class="alert-link">View</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filters -->
<div class="admin-card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin-portal.inventory') }}" class="row g-3">
            <div class="col-md-2">
                <label for="item_type" class="form-label">Item Type</label>
                <select class="form-select" id="item_type" name="item_type">
                    <option value="">All Types</option>
                    <option value="medical_supply" {{ request('item_type') == 'medical_supply' ? 'selected' : '' }}>Medical Supply</option>
                    <option value="equipment" {{ request('item_type') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="medication" {{ request('item_type') == 'medication' ? 'selected' : '' }}>Medication</option>
                    <option value="consumable" {{ request('item_type') == 'consumable' ? 'selected' : '' }}>Consumable</option>
                    <option value="durable_medical_equipment" {{ request('item_type') == 'durable_medical_equipment' ? 'selected' : '' }}>Durable Medical Equipment</option>
                    <option value="laboratory_supply" {{ request('item_type') == 'laboratory_supply' ? 'selected' : '' }}>Laboratory Supply</option>
                    <option value="office_supply" {{ request('item_type') == 'office_supply' ? 'selected' : '' }}>Office Supply</option>
                    <option value="other" {{ request('item_type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <option value="surgical_instruments" {{ request('category') == 'surgical_instruments' ? 'selected' : '' }}>Surgical Instruments</option>
                    <option value="diagnostic_equipment" {{ request('category') == 'diagnostic_equipment' ? 'selected' : '' }}>Diagnostic Equipment</option>
                    <option value="medications" {{ request('category') == 'medications' ? 'selected' : '' }}>Medications</option>
                    <option value="bandages_dressings" {{ request('category') == 'bandages_dressings' ? 'selected' : '' }}>Bandages & Dressings</option>
                    <option value="gloves_masks" {{ request('category') == 'gloves_masks' ? 'selected' : '' }}>Gloves & Masks</option>
                    <option value="syringes_needles" {{ request('category') == 'syringes_needles' ? 'selected' : '' }}>Syringes & Needles</option>
                    <option value="laboratory_supplies" {{ request('category') == 'laboratory_supplies' ? 'selected' : '' }}>Laboratory Supplies</option>
                    <option value="office_supplies" {{ request('category') == 'office_supplies' ? 'selected' : '' }}>Office Supplies</option>
                    <option value="furniture" {{ request('category') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            {{-- <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    <option value="under_maintenance" {{ request('status') == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div> --}}
            <div class="col-md-2">
                <label for="stock_status" class="form-label">Stock Status</label>
                <select class="form-select" id="stock_status" name="stock_status">
                    <option value="">All Stock Status</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="expiring_soon" {{ request('stock_status') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                    <option value="expired" {{ request('stock_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name, code, or description">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin-portal.inventory') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Items Table -->
<div class="admin-card">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Inventory Items ({{ $inventory->total() }} items)</h6>
    </div>
    <div class="card-body">
        @if($inventory->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Current Stock</th>
                            <th>Min/Max</th>
                            <th>Status</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory as $item)
                            <tr>
                                <td>
                                    <code>{{ $item->item_code }}</code>
                                </td>
                                <td>
                                    <strong>{{ $item->name }}</strong>
                                    @if($item->description)
                                        <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $item->item_type)) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $item->isLowStock ? 'bg-warning' : ($item->current_quantity == 0 ? 'bg-danger' : 'bg-success') }}">
                                        {{ $item->current_quantity }} {{ $item->unit_of_measure }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->minimum_quantity)
                                        Min: {{ $item->minimum_quantity }}
                                        @if($item->maximum_quantity)
                                            <br>Max: {{ $item->maximum_quantity }}
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($item->status)
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
                                </td>
                                <td>
                                    @if($item->expiry_date)
                                        @if($item->isExpired)
                                            <span class="text-danger">{{ $item->expiry_date->format('M d, Y') }}</span>
                                        @elseif($item->isExpiringSoon)
                                            <span class="text-warning">{{ $item->expiry_date->format('M d, Y') }}</span>
                                        @else
                                            {{ $item->expiry_date->format('M d, Y') }}
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin-portal.inventory.show', $item->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin-portal.inventory.edit', $item->id) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="deleteInventoryItem({{ $item->id }}, '{{ $item->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $inventory->appends(request()->query())->links('vendor.pagination.admin-portal') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No inventory items found</h5>
                <p class="text-muted">Get started by adding your first inventory item.</p>
                <a href="{{ route('admin-portal.inventory.create') }}" class="btn btn-admin-primary">
                    <i class="fas fa-plus me-1"></i>Add First Item
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Hidden delete form -->
<form id="deleteInventoryForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
function deleteInventoryItem(itemId, itemName) {
    AdminUtils.confirmDelete(
        'Delete Inventory Item',
        `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
        function() {
            // Set the form action and submit
            const form = document.getElementById('deleteInventoryForm');
            form.action = `{{ url('/admin-portal/inventory') }}/${itemId}`;
            form.submit();
        }
    );
}
</script>
@endsection
