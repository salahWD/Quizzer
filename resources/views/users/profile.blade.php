@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/create-tables.scss"])
@endsection

@section('content')
  <div class="container pb-5 mb-5">
    <div class="row">
      <div class="col-2"></div>
      <div class="col-8">
        <div class="formBox shadow bg-light">
          <form method="post" action="{{ route("update_profile", $user->id) }}">
            @csrf
            <div class="row">

              @if(Session::has('success') && session('success') == true)
                <p class="alert alert-success">
                  <b>{{ __('Success! Your profile information has been updated') }}</b>
                </p>
              @endif

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <div class="col-sm-12">
                @if(isset($user) && $user !== null)
                  <h2>{{ __('Profile') }} ({{ $user->name }})</h2>
                @else
                  <h2>{{ __('Add User') }}</h2>
                @endif
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="inputBox">
                  <input
                    id="first_name" type="text"
                    class="input @error('first_name') border-danger is-invalid @enderror"
                    name="first_name" placeholder="{{ __('First Name') }}"
                    value="{{ old('first_name') ?? $user->first_name ?? "" }}" autocomplete="first_name">
                  @error('first_name')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
              <div class="col-sm-6">
                <div class="inputBox">
                  <input
                    id="last_name" type="text"
                    class="input @error('last_name') border-danger is-invalid @enderror"
                    name="last_name" placeholder="{{ __('Last Name') }}"
                    value="{{ old('last_name') ?? $user->last_name ?? "" }}" autocomplete="last_name">
                  @error('first_name')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="inputBox">
                  <input
                      id="email"
                      type="email"
                      class="input @error('email') border-danger is-invalid @enderror"
                      name="email"
                      placeholder="{{ __('Enter Email Address') }}"
                      value="{{ old('email') ?? $user->email ?? "" }}"
                      required autocomplete="email"/>
                  @error('email')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>

              <div class="col-sm-6">
                <div class="inputBox">
                  <input
                      id="password"
                      type="text"
                      class="input @error('password') border-danger is-invalid @enderror"
                      name="password"
                      placeholder="{{ __('Enter A New Password') }}"
                      value="{{ old('password') }}"
                      autocomplete="current-password"/>
                  @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12">
                <input type="submit" name="" class="button" value="{{ __('Save') }}">
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="col-2"></div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
  <script src="{{ url("js/datepicker.js") }}"></script>
  <script>
    $('#expire-date').datepicker({
      weekStart:1,
      color: 'red'
    });
  </script>
@endsection
