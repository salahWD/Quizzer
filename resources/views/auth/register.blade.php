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
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form method="POST" action="{{ route('register') }}">
          @csrf
          <div class="center">
            <fieldset class="form-body">
              <div class="btn-group w-100" role="group">
                <button type="button" class="btn btn-primary">{{ __("Register") }}</button>
                <a href="{{ route('login') }}" class="btn btn-outline">{{ __("Login") }}</a>
              </div>


              <div class="mb-3 mt-4">
                <label for="name" class="col-md-4 p-0 col-form-label">{{ __('Name') }}</label>

                <input
                    id="name"
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    name="name"
                    placeholder="{{ __('Name') }}"
                    value="{{ old('name') }}" required autocomplete="name">

                @error('name')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input
                    id="email"
                    type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    name="email"
                    placeholder="{{ __('Enter Email Address') }}"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"/>
                @error('email')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input
                    id="password"
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password"
                    placeholder="{{ __('Enter Password') }}"
                    required
                    autocomplete="current-password"/>
                @error('password')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror

              </div>

              <div class="mb-3">
                <label for="password-confirm" class="col-form-label">{{ __('Confirm Password') }}</label>
                <input
                    id="password-confirm"
                    type="password"
                    class="form-control mb-4"
                    placeholder="{{ __('Enter Confirm Password') }}"
                    name="password_confirmation" required autocomplete="new-password">
              </div>

              <div class="mb-0">
                <button type="submit" class="btn btn-primary w-100">
                  {{ __('Register') }}
                </button>
              </div>


            </fieldset>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
