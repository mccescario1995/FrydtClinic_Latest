@extends('admin-portal.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Stock Movements for {{ $inventory->name }}
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('admin-portal.inventory.show', $inventory->id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Item
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Inventory Item Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text">Item Code</span>
                                    <span class="info-box-number">{{ $inventory->item_code }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text">Current Quantity</span>
                                    <span class="info-box-number">{{ $inventory->current_quantity }} {{ $inventory->unit_of_measure }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Movements Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Movement Type</th>
                                    <th>Quantity</th>
                                    <th>Reason</th>
                                    <th>Performed By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('M j, Y g:i A') }}</td>
                                    <td>
                                        <span class="badge
                                            @if($movement->movement_type === 'in')
                                                bg-primary text-white
                                            @elseif($movement->movement_type === 'out')
                                                bg-danger text-white
                                            @elseif($movement->movement_type === 'adjustment')
                                                bg-success text-white
                                            @else
                                                bg-secondary text-white
                                            @endif">
                                            {{ ucfirst($movement->movement_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($movement->movement_type === 'in')
                                            <span class="text-success">+{{ $movement->quantity_moved }}</span>
                                        @elseif($movement->movement_type === 'out')
                                            <span class="text-danger">{{ $movement->quantity_moved }}</span>
                                        @else
                                            {{ $movement->quantity_moved }}
                                        @endif
                                    </td>
                                    <td>{{ $movement->reason ?: 'N/A' }}</td>
                                    <td>{{ $movement->user ? $movement->user->name : 'System' }}</td>
                                    <td>{{ $movement->notes ?: 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No stock movements found for this item.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($movements->hasPages())
                    <div class="mt-4">
                        {{ $movements->appends(request()->query())->links('vendor.pagination.admin-portal') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
