@extends(backpack_view('blank'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h1 class="h2 d-inline-block">{{ $title }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3">
                <form action="{{ route('inventory.update-stock', $inventory->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="current_quantity" class="form-label">Current Quantity</label>
                        <input type="text" class="form-control" id="current_quantity" value="{{ $inventory->current_quantity }}" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="quantity_moved" class="form-label">Quantity to Add/Remove</label>
                        <input type="number" name="quantity_moved" id="quantity_moved" class="form-control" placeholder="Use a negative value to remove stock" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="movement_type" class="form-label">Movement Type</label>
                        <select name="movement_type" id="movement_type" class="form-control" required>
                            <option value="">-- Select Type --</option>
                            <option value="stock_in">Stock In</option>
                            <option value="stock_out">Stock Out</option>
                            <option value="sale">Sale</option>
                            <option value="loss">Loss/Damage</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
