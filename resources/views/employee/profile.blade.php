@extends('employee.layouts.app')

@section('title', 'Employee Profile')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">My Profile</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('employee.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Profile Image Section -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <img src="{{ $employeeProfile && $employeeProfile->image_path ? asset('storage/app/public/' . $employeeProfile->image_path) : 'https://via.placeholder.com/150x150?text=No+Image' }}"
                                         alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" name="profile_image" id="profile_image"
                                        class="form-control @error('profile_image') is-invalid @enderror"
                                        accept="image/*">
                                    <div class="form-text">Upload a profile image (JPG, PNG, GIF). Max size: 2MB.</div>
                                    @error('profile_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @if ($employeeProfile)
                                @php
                                    // dd($employeeProfile)
                                @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ old('phone', $employeeProfile->phone) }}">
                                            @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $employeeProfile->address) }}</textarea>
                                            @error('address')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Employee Number</label>
                                            <input type="text" class="form-control"
                                                value="{{ $employeeProfile->employee_id }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Position</label>
                                            <input type="text" class="form-control" name="position"
                                                value="{{ $employeeProfile->position }}"
                                                @if ($employeeProfile->position != null) readonly @endif>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Hire Date</label>
                                            <input type="text" class="form-control"
                                                value="{{ $employeeProfile->hire_date ? $employeeProfile->hire_date->format('M d, Y') : 'N/A' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <input type="text" class="form-control"
                                                value="{{ ucfirst($employeeProfile->status) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">PIN (6 digits)</label>
                                            <input type="password" class="form-control @error('user_pin') is-invalid @enderror"
                                                name="user_pin" maxlength="6" pattern="[0-9]{6}"
                                                placeholder="Enter 6-digit PIN" inputmode="numeric">
                                            <div class="form-text">Leave empty to keep current PIN</div>
                                            @error('user_pin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm PIN</label>
                                            <input type="password" class="form-control @error('user_pin_confirmation') is-invalid @enderror"
                                                name="user_pin_confirmation" maxlength="6" pattern="[0-9]{6}"
                                                placeholder="Confirm PIN" inputmode="numeric">
                                            @error('user_pin_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    Employee profile not found. Please contact administrator.
                                </div>
                            @endif

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
