@extends('layout.master-no-footer')

@section('content')
  <div class="container mu-auth">
    <form method="POST" action="{{ route('register') }}" class="w-100 d-flex justify-content-center">
      @csrf
      <fieldset class="mu-auth-card">
        <div class="mu-switch" role="tablist">
          <a role="tab" aria-selected="false" href="{{ route('login') }}">{{ __("Login") }}</a>
          <span role="tab" aria-selected="true">{{ __("Register") }}</span>
        </div>

        <h1>{{ __('Register') }}</h1>
        <p class="sub">{{ __('Get a free 14-day trial for your first time') }}</p>

        <div class="mb-3">
          <label for="name" class="form-label">{{ __('Name') }}</label>
          <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
            placeholder="{{ __('Name') }}" value="{{ old('name') }}" required autocomplete="name">
          @error('name')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">{{ __('Email Address') }}</label>
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            placeholder="{{ __('Enter Email Address') }}" value="{{ old('email') }}" required autocomplete="email"/>
          @error('email')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">{{ __('Password') }}</label>
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            placeholder="{{ __('Enter Password') }}" required autocomplete="new-password"/>
          @error('password')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="mb-4">
          <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
            placeholder="{{ __('Enter Confirm Password') }}" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">{{ __('Register') }}</button>
      </fieldset>
    </form>
  </div>
@endsection
