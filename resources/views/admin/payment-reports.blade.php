@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
@endsection

@section('content')

  @include('admin.reports-header')
  <div class="container-xl">
    <h1 class="text-center my-5">{{ __('Payments Managment') }}</h1>
    <div class="table-responsive mb-5 shadow">
      <div class="table-wrapper">
        <div class="table-title">
          <div class="row">
            <div class="col-sm-5">
              <h2><b>{{ __('Payments') }}</b></h2>
            </div>
          </div>
        </div>
        <div class="table table-striped table-5-col table-hover w-100" id="table">
          <div class="row table-header">
            <div class="cell">{{ __('User') }}</div>
            <div class="cell">{{ __('Plan') }}</div>
            <div class="cell">{{ __('Price') }}</div>
            <div class="cell">{{ __('Method') }}</div>
            <div class="cell">{{ __('Date') }}</div>
          </div>
          @foreach($payments as $i => $payment)
            <div class="row og-item">
              <div class="cell">{{ $payment->user->name }}</div>
              <div class="cell"><a href="{{ route("payment_info", $payment->id) }}">{{ __($payment->get_package_name()) }}</a></div>
              <div class="cell">${{ $payment->amount }}</div>
              <div class="cell">{{ $payment->method }}</div>
              <div class="cell">{{ $payment->created_at->format("Y-m-d") }}</div>
            </div>
          @endforeach
        </div>
        <div class="clearfix">
          <div class="hint-text">{{ __('Showing') }} <b>{{ count($payments->items()) }}</b> {{ __('out of') }} <b>{{ $payments->total() }}</b> {{ __('entries') }}</div>
          @if($payments->hasPages())
            <ul class="pagination" id="pagination">
              <li class="page-item"><a href="{{ $payments->previousPageUrl() }}" class="page-link">{{ __('Previous') }}</a></li>

              @if($payments->currentPage() == 1)
                <li class="page-item active"><a class="page-link">{{ $payments->currentPage() }}</a></li>
                <li class="page-item">
                  <a href="{{ $payments->url($payments->currentPage() + 1) }}" class="page-link">{{ $payments->currentPage() + 1 }}</a>
                </li>
              @else
                <li class="page-item">
                  <a href="{{ $payments->url($payments->currentPage() - 1) }}" class="page-link">{{ $payments->currentPage() - 1 }}</a>
                </li>
                <li class="page-item active"><a class="page-link">{{ $payments->currentPage() }}</a></li>
              @endif
              @if($payments->total() > 2)
                @if($payments->currentPage() == 1 && $payments->lastPage() > 2)
                  <li class="page-item">
                    <a href="{{ $payments->url($payments->currentPage() + 2) }}" class="page-link">{{ $payments->currentPage() + 2 }}</a>
                  </li>
                @elseif($payments->currentPage() > 1 && $payments->lastPage() > $payments->currentPage())
                  <li class="page-item">
                    <a href="{{ $payments->url($payments->currentPage() + 1) }}" class="page-link">{{ $payments->currentPage() + 1 }}</a>
                  </li>
                @endif
              @endif

              <li class="page-item"><a href="{{ $payments->nextPageUrl() }}" class="page-link">{{ __('Next') }}</a></li>
            </ul>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@section("scripts")
  <script>
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
@endsection
