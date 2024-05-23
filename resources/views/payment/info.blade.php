@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
  @vite(["resources/css/user-info.css"])
@endsection

@section('content')

  <!-- Page content -->
  <div class="container pb-5 mb-5 mt-5">
      <div class="row">
        <div class="offset-1 col-xl-10 order-xl-1">
          <div class="card bg-secondary shadow">
            <div class="card-header bg-white border-0">
              <div class="row align-items-center">
                <div class="col-8">
                  <h3 class="mb-0">{{ __('Payment Information') }}</h3>
                </div>
              </div>
            </div>
            <div class="card-body">
              <form>
                <div class="pl-lg-4">
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-user">{{ __("User") }}</label>
                        <input readonly type="text" id="input-user" class="form-control form-control-alternative" placeholder="username" value="{{ $payment->user->name }}">
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-payment">{{ __("Plan") }}</label>
                        <input readonly type="text" id="input-payment" class="form-control form-control-alternative" placeholder="payment" value="{{ __($payment->get_package_name()) }}">
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="price">{{ __("Price") }}</label>
                        <input readonly id="price" class="form-control form-control-alternative" value="${{ $payment->amount }}">
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="method">{{ __("Payment Method") }}</label>
                        <input readonly type="text" id="method" class="form-control form-control-alternative" value="{{ $payment->method }}">
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="my-4">
                <!-- Address -->
                <h6 class="heading-small text-muted mb-4">{{ __("Additional Info") }}</h6>
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="date">{{ __("Payment Date") }}</label>
                        <input readonly id="date" class="form-control form-control-alternative" value="{{ $payment->created_at->format("Y-m-d") }}">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="status">{{ __("Status") }}</label>
                        <input readonly id="status" class="form-control form-control-alternative" value="{{ $payment->status }}">
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
@endsection
