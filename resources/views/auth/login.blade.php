@extends('layout.master-no-footer')

@section('content')
  <div class="container mu-auth">
    <form method="POST" action="{{ route('login', app()->getLocale()) }}" class="w-100 d-flex justify-content-center">
      @csrf
      <fieldset class="mu-auth-card">
        <div class="mu-switch" role="tablist">
          <span role="tab" aria-selected="true">{{ __("Login") }}</span>
          <a role="tab" aria-selected="false" href="{{ route('register', app()->getLocale()) }}">{{ __("Register") }}</a>
        </div>

        <h1>{{ __('Login') }}</h1>
        <p class="sub">{{ env("APP_NAME") }}</p>

        <div class="mb-3">
          <label for="email" class="form-label">{{ __('Email Address') }}</label>
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            placeholder="{{ __('Enter Email Address') }}" value="{{ old('email') }}" required autocomplete="email" autofocus/>
          @error('email')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-baseline gap-2">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            @if (Route::has('password.request'))
              <a class="btn-link small p-0" href="{{ route('password.request', app()->getLocale()) }}">{{ __('Forgot Your Password?') }}</a>
            @endif
          </div>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            placeholder="{{ __('Enter Password') }}" required autocomplete="current-password"/>
          @error('password')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="form-check mb-4">
          <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">{{ __('Login') }}</button>
      </fieldset>
    </form>
  </div>
@endsection
