@extends('layout.master-no-footer')

@section('styles')
  <style>
    .center {
      display: flex;
      align-content: center;
      justify-content: center;
      height: calc(100dvh - 80px);
      flex-direction: column;
    }
    .mw-500, form .form-body {
      width: 500px;
    }
  </style>
@endsection

@section('content')

  <div class="container">
    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="center">
        <fieldset class="form-body">
          <div class="btn-group w-100" role="group">
            <a href="{{ route("login") }}" class="btn btn-outline">{{ __("Login") }}</a>
            <button type="button" class="btn btn-primary">{{ __("Reset Password") }}</button>
          </div>

          <div class="mb-4 mt-4">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input
                id="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email"
                placeholder="{{ __('Enter Email Address') }}"
                value="{{ $email ?? old('email') }}"
                required
                autocomplete="email"
                autofocus/>
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="mb-4 mt-4">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input
                id="password"
                type="password"
                class="form-control
                @error('password') is-invalid @enderror"
                name="password"
                required
                autocomplete="new-password">
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="mb-4 mt-4">
            <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
            <input
                id="password-confirm"
                type="password"
                class="form-control"
                name="password_confirmation"
                required
                autocomplete="new-password">
            @error('password_confirmation')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>

          <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100">
              {{ __('Send Password Reset Link') }}
            </button>

          </div>


        </fieldset>
      </div>
    </form>
  </div>

@endsection
