<h2 class="card-title text-center my-4">{{ trans('backpack::base.register') }}</h2>
<form role="form" method="POST" action="{{ route('backpack.auth.register') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="name">{{ trans('backpack::base.name') }}</label>
        <input autofocus tabindex="1" type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
            name="name" id="name" value="{{ old('name') }}">
        @if ($errors->has('name'))
            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label"
            for="{{ backpack_authentication_column() }}">{{ trans('backpack::base.' . strtolower(config('backpack.base.authentication_column_name'))) }}</label>
        <input tabindex="2"
            type="{{ backpack_authentication_column() == backpack_email_column() ? 'email' : 'text' }}"
            class="form-control {{ $errors->has(backpack_authentication_column()) ? 'is-invalid' : '' }}"
            name="{{ backpack_authentication_column() }}" id="{{ backpack_authentication_column() }}"
            value="{{ old(backpack_authentication_column()) }}">
        @if ($errors->has(backpack_authentication_column()))
            <div class="invalid-feedback">{{ $errors->first(backpack_authentication_column()) }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="phone">Phone Number</label>
        <input tabindex="3" type="text" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
            name="phone" id="phone" value="{{ old('phone') }}" placeholder="e.g., +63 912 345 6789">
        @if ($errors->has('phone'))
            <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="birth_date">Birth Date</label>
            <input tabindex="4" type="date"
                class="form-control {{ $errors->has('birth_date') ? 'is-invalid' : '' }}" name="birth_date"
                id="birth_date" value="{{ old('birth_date') }}">
            @if ($errors->has('birth_date'))
                <div class="invalid-feedback">{{ $errors->first('birth_date') }}</div>
            @endif
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="gender">Gender</label>
            <select tabindex="5" class="form-control {{ $errors->has('gender') ? 'is-invalid' : '' }}" name="gender"
                id="gender">
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
            @if ($errors->has('gender'))
                <div class="invalid-feedback">{{ $errors->first('gender') }}</div>
            @endif
        </div>
    </div>

    <div class=" mb-3">
        <label class="form-label" for="password">{{ trans('backpack::base.password') }}</label>
        <input tabindex="6" type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
            name="password" id="password" value="">
        @if ($errors->has('password'))
            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="password_confirmation">{{ trans('backpack::base.confirm_password') }}</label>
        <input tabindex="7" type="password"
            class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
            name="password_confirmation" id="password_confirmation" value="">
        @if ($errors->has('password_confirmation'))
            <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label class="form-label" for="address">Address</label>
        <textarea tabindex="8" class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" name="address"
            id="address" rows="2" placeholder="Enter your complete address">{{ old('address') }}</textarea>
        @if ($errors->has('address'))
            <div class="invalid-feedback">{{ $errors->first('address') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input tabindex="9" class="form-check-input {{ $errors->has('terms') ? 'is-invalid' : '' }}"
                type="checkbox" id="terms" name="terms" value="1" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#"
                    target="_blank">Privacy Policy</a>
            </label>
            @if ($errors->has('terms'))
                <div class="invalid-feedback">{{ $errors->first('terms') }}</div>
            @endif
        </div>
    </div>

    <div class="form-group">
        <div>
            <button tabindex="5" type="submit" class="btn btn-primary w-100">
                {{ trans('backpack::base.register') }}
            </button>
        </div>
    </div>
</form>
