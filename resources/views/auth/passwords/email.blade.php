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
    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="center">
        <fieldset class="form-body">
          <div class="btn-group w-100" role="group">
            <a href="{{ route("login") }}" class="btn btn-outline">{{ __("Login") }}</a>
            <button type="button" class="btn btn-primary">{{ __("Reset Password") }}</button>
          </div>

          <div class="mb-4 mt-4">
            @if (session('status'))
              <div class="alert alert-success" role="alert">
                {{ session('status') }}
              </div>
            @endif
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input
                id="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email"
                placeholder="{{ __('Enter Email Address') }}"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                autofocus/>
            @error('email')
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
