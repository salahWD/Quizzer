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
    <form method="POST" action="{{ route('login', app()->getLocale()) }}">
      @csrf
      <div class="center">
        <fieldset class="form-body">
          <div class="btn-group w-100" role="group">
            <a href="{{ route('register', app()->getLocale()) }}" class="btn btn-outline">{{ __("Register") }}</a>
            <button type="button" class="btn btn-primary">{{ __("Login") }}</button>
          </div>

          <div class="mb-4 mt-4">
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

          <div class="row">
            <div class="col-6">
              <div class="mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox"
                  name="remember" id="remember" {{ old('remember') ? 'checked' :
                  '' }}>

                  <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                  </label>
                </div>
              </div>
            </div>
            <div class="col-6 p-0">
              @if (Route::has('password.request'))
                <a class="btn-link" href="{{ route('password.request', app()->getLocale()) }}">
                  {{ __('Forgot Your Password?') }}
                </a>
              @endif
            </div>
          </div>

          <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100">
              {{ __('Login') }}
            </button>

          </div>


        </fieldset>
      </div>
    </form>
  </div>

@endsection
