@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
@endsection

@section('content')
<div class="container-xl">
    <h1 class="text-center my-5">{{ __('Coupons Managment') }}</h1>
      <div class="table-responsive mb-5 shadow">
          <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-5">
                      <h2><b>{{ __('Coupons') }}</b></h2>
                    </div>
                    <div class="col-sm-7">
                      <a href="{{ route("create_coupon") }}" class="btn btn-secondary"><i class="fa fa-plus"></i> <span>Add New {{ __('Coupon') }}</span></a>
                    </div>
                </div>
            </div>
            <div class="table table-striped table-hover w-100 table-8-col">
                <div class="row">
                    <div class="cell">#</div>
                    <div class="cell code">Code</div>
                    <div class="cell">Amount</div>
                    <div class="cell">Package</div>
                    <div class="cell">Status</div>
                    <div class="cell">Expire Date</div>
                    <div class="cell">Usage</div>
                    <div class="cell">Action</div>
                </div>
                @foreach($coupons as $i => $coupon)
                  <div class="row">
                    <div class="cell">{{ $i + 1 }}</div>
                    <div class="cell code">
                      <a href="{{ route("edit_coupon", $coupon->id) }}">{{ $coupon->code }}</a>
                    </div>
                    <div class="cell">{{ $coupon->amount ?? 0 }}</div>
                    <div class="cell">{{ $coupon->has_package() ? $coupon->package()["NAME"] : "-" }}</div>
                    <div class="cell">
                      @if($coupon->is_expired())
                        <span class="status text-danger">&bull;</span> Expired
                      @else
                        <span class="status text-success">&bull;</span> Active
                      @endif
                    </div>
                    <div class="cell">{{ $coupon->expire_date ?? "-" }}</div>
                    <div class="cell">{{ $coupon->usage ?? 0 }}</div>
                    <div class="cell">
                        <a href="{{ route("edit_coupon", $coupon->id) }}" class="settings" title="Settings" data-toggle="tooltip"><i class="fa fa-gear"></i></a>
                        <a href="{{ route("delete_coupon", $coupon->id) }}" class="delete" title="Delete" data-toggle="tooltip"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="clearfix">
              <div class="hint-text">Showing <b>{{ count($coupons->items()) }}</b> out of <b>{{ $coupons->total() }}</b> entries</div>
              @if($coupons->hasPages())
                <ul class="pagination">
                  <li class="page-item"><a href="{{ $coupons->previousPageUrl() }}" class="page-link">Previous</a></li>

                  @if($coupons->currentPage() == 1)
                    <li class="page-item active"><a class="page-link">{{ $coupons->currentPage() }}</a></li>
                    <li class="page-item">
                      <a href="{{ $coupons->url($coupons->currentPage() + 1) }}" class="page-link">{{ $coupons->currentPage() + 1 }}</a>
                    </li>
                  @else
                    <li class="page-item">
                      <a href="{{ $coupons->url($coupons->currentPage() - 1) }}" class="page-link">{{ $coupons->currentPage() - 1 }}</a>
                    </li>
                    <li class="page-item active"><a class="page-link">{{ $coupons->currentPage() }}</a></li>
                  @endif
                  @if($coupons->total() > 2)
                    @if($coupons->currentPage() == 1 && $coupons->lastPage() > 2)
                      <li class="page-item">
                        <a href="{{ $coupons->url($coupons->currentPage() + 2) }}" class="page-link">{{ $coupons->currentPage() + 2 }}</a>
                      </li>
                    @elseif($coupons->currentPage() > 1 && $coupons->lastPage() > $coupons->currentPage())
                      <li class="page-item">
                        <a href="{{ $coupons->url($coupons->currentPage() + 1) }}" class="page-link">{{ $coupons->currentPage() + 1 }}</a>
                      </li>
                    @endif
                  @endif

                  <li class="page-item"><a href="{{ $coupons->nextPageUrl() }}" class="page-link">Next</a></li>
                </ul>
              @endif
            </div>
      </div>
  </div>
@endsection
