@extends('layout.master')

@section('content')
<div class="bg-white" style="margin-top: -24px">
  <div class="container">
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8">
        @if(empty($plan_name))
          <div class="alert alert-danger mt-5">
            <h2 class="title">{{ __("Not Subscribed") }}</h2>
            <p class="lead">{{ __("you don not have an active subscription, please") }} <a href="{{ route("pricing") }}">{{ __("choose a package") }}</a></p>
          </div>
        @endif
        <div class="card p-3 py-0 mb-3 @if(empty($plan_name)) mt-2 @else mt-5 @endif">
          <div class="card-body">
            <div class="row">
              <h6 class="card-title p-3 py-2 border-bottom">{{ __('Current Plan') }}</h6>
              <div class="d-flex justify-content-between">
                <h3 class="card-title">{{ __($plan_name ?? "No Plan") }}</h3>
                <div class="price text-primary d-flex align-items-center @if(app()->getLocale() == "ar") flex-row-reverse @endif">
                  <h4>{{ config("pricing.CURRENCY") . $package_price }}</h4>
                  <small class="text-secondary mx-2">{{ __('price of package') }}</small>
                </div>
              </div>
              <small class="text-dark">{{ __('your next payment is') }} {{ $next_payment ?? "No Date" }}</small>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <a href="{{ route("pricing") }}" class="btn btn-primary">{{ __('Change Plan') }}</a>
              <a href="{{ route("subscription_cancel") }}" class="btn btn-outline-danger">{{ __('Cancel Plan') }}</a>
            </div>
          </div>
        </div>
        <div class="card p-3 py-0 mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <p class="m-0 text-secondary">{{ __('Monthly Responses') }} <small class="text-dark">{{ auth()->user()->total_responses_count() }} / {{ auth()->user()->allowed_responses() }}</small></p>
              <p class="m-0 text-secondary">{{ __('Renewal Date') }} <small class="text-dark">{{ $renew_plan_features_date }}</small></p>
            </div>
          </div>
        </div>
        <div class="card p-3 py-0 mb-4">
          <div class="card-body">
            <h6 class="card-title">{{ __('Billing History') }}</h6>
            <table class="table table-hover text-secondary">
              <thead>
                <tr>
                  <th scope="col">{{ __('Method') }}</th>
                  <th scope="col">{{ __('Amount') }}</th>
                  <th scope="col">{{ __('Package') }}</th>
                  <th scope="col">{{ __('Date') }}</th>
                </tr>
              </thead>
              <tbody>
                @if($history_payment != null && !empty($history_payment) && count($history_payment) > 0)
                  @foreach($history_payment as $payment)
                    <tr>
                      <th scope="row">{{ $payment->method }}</th>
                      <td>{{ $payment->get_currency() }}{{ $payment->amount }}</td>
                      <td>{{ __($payment->get_package_name()) }}</td>
                      <td>{{ $payment->created_at->format("Y-m-d") }}</td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-2"></div>
    </div>
  </div>
</div>
@endsection
