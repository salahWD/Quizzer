@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
  @vite(["resources/css/user-info.css"])
@endsection

@section('content')
  {{-- <div class="container pb-5 mb-5">
    <div class="row">
      <div class="col-2"></div>
      <div class="col-8">
        <div class="formBox shadow bg-light">
        </div>
      </div>
      <div class="col-2"></div>
    </div>
  </div> --}}

  <!-- Page content -->
  <div class="container pb-5 mb-5 mt-5">
      <div class="row">
        <div class="offset-1 col-xl-10 order-xl-1">
          <div class="card bg-secondary shadow">
            <div class="card-header bg-white border-0">
              <div class="row align-items-center">
                <div class="col-8">
                  <h3 class="mb-0">{{ __('User Information') }}</h3>
                </div>
              </div>
            </div>
            <div class="card-body">
              <form>
                <div class="pl-lg-4">
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="input-username">{{ __("Username") }}</label>
                        <input readonly type="text" id="input-username" class="form-control form-control-alternative" placeholder="Username" value="{{ $user->name }}">
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="first_name">{{ __("First Name") }}</label>
                        <input readonly type="email" id="first_name" class="form-control form-control-alternative" value="{{ $user->first_name ?? "--" }}">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="last_name">{{ __("Last Name") }}</label>
                        <input readonly type="email" id="last_name" class="form-control form-control-alternative" value="{{ $user->last_name ?? "--" }}">
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="EMail">{{ __("Email") }}</label>
                        <input readonly type="text" id="EMail" class="form-control form-control-alternative" value="{{ $user->email }}">
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="my-4">
                <!-- Address -->
                <h6 class="heading-small text-muted mb-4">{{ __("Subscription Info") }}</h6>
                <div class="pl-lg-4">
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group focused">
                        <label class="form-control-label" for="plan">{{ __("Plan") }}</label>
                        <input readonly id="plan" class="form-control form-control-alternative" value="{{ $plan_name }}">
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group">
                        <label class="form-control-label" for="end_date">{{ __("End Date") }}</label>
                        <input readonly id="end_date" class="form-control form-control-alternative" value="{{ $renew_plan_date }}">
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="my-4">
                <h6 class="heading-small text-muted mb-4">{{ __("Payment History") }}</h6>
                <div class="pl-lg-4">
                  <div class="form-group focused">
                    @if($history_payment->count() > 0)
                      <div class="table table-striped table-hover w-100 table-4-col">
                        <div class="row">
                          <div class="cell">{{ __('Image') }}</div>
                          <div class="cell">{{ __('Method') }}</div>
                          <div class="cell code">{{ __('Date') }}</div>
                          <div class="cell">{{ __('Amount') }}</div>
                        </div>
                        @foreach($history_payment as $i => $payment)
                          <div class="row">
                            <div class="cell p-0">
                              @if ($payment->bank_bill)
                                <img src="{{ url("images/bills/" . $payment->bank_bill) }}" class="img img-fluid">
                              @else
                              -
                              @endif
                            </div>
                            <div class="cell">{{ $payment->method }}</div>
                            <div class="cell code">{{ $payment->created_at ?? "-" }}</div>
                            <div class="cell">{{ config('pricing.CURRENCY') . $payment->amount }}</div>
                          </div>
                        @endforeach
                      </div>
                    @else
                      <div class="bd-callout bd-callout-danger">
                        <h6>{{ __("there are no records") }}</h6>
                      </div>
                    @endif
                  </div>
                </div>
                <hr class="my-4">
                <h6 class="heading-small text-muted mb-4">{{ __("Quizzes") }}</h6>
                <div class="pl-lg-4">
                  <div class="form-group focused">
                    @if($quizzes->count() > 0)
                      <div class="table table-striped table-hover w-100 table-2-col">
                        <div class="row">
                          <div class="cell col-9">{{ __('Title') }}</div>
                          <div class="cell col-3">{{ __('Status') }}</div>
                        </div>
                        @foreach($quizzes as $quiz)
                          <div class="row">
                            <div class="cell justify-content-start col-9"><a href="{{ route("build_quiz", $quiz->id) }}">{{ $quiz->name }}</a></div>
                            <div class="cell col-3">{{ $quiz->status }}</div>
                          </div>
                        @endforeach
                      </div>
                    @else
                      <div class="bd-callout bd-callout-danger">
                        <h6>{{ __("there are no records") }}</h6>
                      </div>
                    @endif
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
