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
          <form method="post" action="{{ isset($user) && $user !== null ? route("update_user", $user->id) : route("store_user") }}">
            @csrf
            <div class="row">

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
                  <h2>Edit User ({{ $user->name }})</h2>
                @else
                  <h2>Add User</h2>
                @endif
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="inputBox">
                  <input
                    id="name" type="text"
                    class="input @error('name') border-danger is-invalid @enderror"
                    name="name" placeholder="{{ __('Name') }}"
                    value="{{ old('name') ?? $user->name ?? "" }}" required autocomplete="name">
                  @error('name')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
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
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="inputBox">
                  <input type="text" id="expire-date" name="subscription_end" class="input" placeholder="Subscription Finish Date" value="{{ old("subscription_end") ?? $user->subscription_end ?? "" }}">
                </div>
              </div>

              <div class="col-sm-6">
                <div class="inputBox">
                  <select name="status" class="select">
                    <option value="0">Inactive</option>
                    @php $largest_id = 0; @endphp
                    @foreach ($packages as $package)
                    @php
                      if ($package["ID"] > $largest_id) {
                        $largest_id = $package["ID"];
                      }
                    @endphp
                      <option value="{{ $package["ID"] }}" {{ old("status") == $package["ID"] ? "selected" : (isset($user) && null !== $user && $user->get_package_id() == $package["ID"] ? "selected" : "") }}>{{ $package["NAME"] }}</option>
                    @endforeach
                    <option value="{{ $largest_id + 1 }}">Admin</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="inputBox">
                  <input
                      id="password"
                      type="text"
                      class="input @error('password') border-danger is-invalid @enderror"
                      name="password"
                      placeholder="{{ __('Enter Password') }}"
                      value="{{ old('password') }}"
                      required
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
                <input type="submit" name="" class="button" value="Send Message">
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
