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
          <form method="post" action="{{ isset($coupon) && $coupon !== null ? route("update_coupon", $coupon->id) : route("store_coupon") }}">
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
                @if(isset($coupon) && $coupon !== null)
                  <h2>Edit Coupon ({{ $coupon->code }})</h2>
                @else
                  <h2>Add Coupon</h2>
                @endif
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="inputBox">
                  <input type="text" name="code" class="input" placeholder="Coupon Code" value="{{ old("code") ?? $coupon->code ?? "" }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="inputBox">
                  <input type="number" name="amount" class="input" step="0.01" placeholder="Amount" value="{{ old("amount") ?? $coupon->amount ?? "" }}">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <div class="inputBox">
                  <input type="text" id="expire-date" name="expire_date" class="input" placeholder="Expire Date" value="{{ old("expire_date") ?? $coupon->expire_date ?? "" }}">
                </div>
              </div>

              <div class="col-sm-6">
                <div class="inputBox">
                  <select name="package_id" class="select">
                    <option value="">No Specific Package</option>
                    @foreach ($packages as $package)
                      <option value="{{ $package["ID"] }}" {{ old("package_id") == $package["ID"] ? "selected" : (isset($coupon) && null !== $coupon && $coupon->package_id == $package["ID"] ? "selected" : "") }}>{{ $package["NAME"] }}</option>
                    @endforeach
                  </select>
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
