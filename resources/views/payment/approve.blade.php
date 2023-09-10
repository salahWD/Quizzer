@extends('layout.app-master')

@section('styles')
  @vite(["resources/sass/manage-tables.scss"])
@endsection

@section('content')
<div class="container-xl">
  <h1 class="text-center my-5">{{ __('Approve Bank Bills') }}</h1>
  <div class="table-responsive mb-5 shadow">
      <div class="table-wrapper">
        <div class="table-title">
          <div class="row">
            <div class="col-sm-5">
              <h2><b>{{ __('Bank Bills') }}</b></h2>
            </div>
          </div>
        </div>
        <div class="table table-striped table-hover w-100 table-7-col">
          <div class="row">
            <div class="cell">Image</div>
            <div class="cell">Method</div>
            <div class="cell code">Date</div>
            <div class="cell">Package</div>
            <div class="cell">Amount</div>
            <div class="cell">Is Approved</div>
            <div class="cell">Action</div>
          </div>
          @foreach($payments as $i => $payment)
            <div class="row">
              <div class="cell p-0">
                <img src="{{ url("/images/bills/" . $payment->bank_bill) }}" class="img img-fluid">
              </div>
              <div class="cell">{{ $payment->method }}</div>
              <div class="cell code">{{ $payment->created_at ?? "-" }}</div>
              <div class="cell">{{ $payment->package_name() }}</div>
              <div class="cell">{{ config('pricing.CURRENCY') . $payment->amount }}</div>
              <div class="cell">
                @if($payment->is_approved())
                  <span class="status text-success">&bull;</span> Approved
                @else
                  <span class="status text-danger">&bull;</span> Not Approved
                @endif
              </div>
              <div class="cell">
                <a href="{{ route("approve_payment", $payment->id) }}" class="settings" title="Approve" data-toggle="tooltip"><i class="fa fa-check"></i></a>
                <a href="{{ route("delete_payment", $payment->id) }}" class="delete" title="Delete" data-toggle="tooltip"><i class="fa fa-trash"></i></a>
              </div>
            </div>
          @endforeach
        </div>
        <div class="clearfix">
          <div class="hint-text">Showing <b>{{ count($payments->items()) }}</b> out of <b>{{ $payments->total() }}</b> entries</div>
          @if($payments->hasPages())
            <ul class="pagination">
              <li class="page-item"><a href="{{ $payments->previousPageUrl() }}" class="page-link">Previous</a></li>

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

              <li class="page-item"><a href="{{ $payments->nextPageUrl() }}" class="page-link">Next</a></li>
            </ul>
          @endif
        </div>
    </div>
  </div>
  <div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="img01">
    <div id="caption"></div>
  </div>

  <script>

    var modal = document.getElementById("myModal");

    var imgs = document.querySelectorAll(".table .cell .img");
    var modalImg = document.getElementById("img01");

    imgs.forEach(img => {

      img.onclick = function() {
        modal.style.display = "block";
        modalImg.src = this.src;
      }

      var span = document.querySelector("#myModal .close");
      span.onclick = function() {
        modal.style.display = "none";
      }

    });

    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
@endsection
