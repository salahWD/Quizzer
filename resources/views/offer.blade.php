@extends('layout.master')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  <style>

    body{
      background:#f5f6f4;
    }

    .card {
        box-shadow: 0 20px 27px 0 rgb(0 0 0 / 5%);
    }

    .card {
      position: relative;
      display: flex;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #fff;
      background-clip: border-box;
      border: 0 solid rgba(0,0,0,.125);
      border-radius: 1rem;
    }

    .card-body {
      -webkit-box-flex: 1;
      -ms-flex: 1 1 auto;
      flex: 1 1 auto;
      padding: 1.5rem 1.5rem;
    }

    #upload-img {
      opacity: 0;
    }

    #upload-label {
      position: absolute;
      top: 50%;
      left: 1rem;
      transform: translateY(-50%);
    }

    .image-area {
      border: 2px dashed rgba(34, 34, 34, 0.5);
      padding: 1rem;
      position: relative;
    }

    .image-area::before {
      content: 'Selected Image';
      color: #555;
      font-weight: bold;
      text-transform: uppercase;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 0.8rem;
      z-index: 1;
    }

    .image-area img {
      z-index: 2;
      position: relative;
    }
  </style>
@endsection

@section('content')
  <form action="{{ route("process_payment", $plan->id) }}" method="POST" enctype="multipart/form-data" id="payment-form">
    @csrf
    <div class="container pb-4">
      <h1 class="h3 mb-5">Payment</h1>
      <div class="row">
        <!-- Left -->
        <div class="col-lg-9">

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="accordion" id="accordionPayment">
            <!-- Bank -->
            <div class="accordion-item mb-3">
              <h2 class="h5 px-4 py-3 accordion-header d-flex justify-content-between align-items-center">
                <label data-bs-toggle="collapse" data-bs-target="#option-1" class="form-check w-100 collapsed form-check-label pt-1" for="payment1">
                  <input class="form-check-input" type="radio" name="payment_method" id="payment1" checked value="bank">
                  {{ __("Bank Transfer") }}
                </label>
                <span>
                  <svg fill="#000000" viewBox="4 -2 34 40" width="34" height="25" xmlns="http://www.w3.org/2000/svg">
                    <path id="_04.Bank" data-name="04.Bank" d="M46,44.438H2a1,1,0,0,1,0-2H46a1,1,0,0,1,0,2Zm-30-10a1,1,0,0,1,0,2H8a1,1,0,0,1,0-2H9v-13H8a1,1,0,0,1,0-2h8a1,1,0,0,1,0,2H15v13Zm-3-13H11v13h2Zm15,13a1,1,0,0,1,0,2H20a1,1,0,0,1,0-2h1v-13H20a1,1,0,0,1,0-2h8a1,1,0,0,1,0,2H27v13Zm-3-13H23v13h2Zm19,18a1,1,0,0,1-1,1H5a1,1,0,0,1,0-2H43A1,1,0,0,1,44,39.438Zm-4-5a1,1,0,0,1,0,2H32a1,1,0,0,1,0-2h1v-13H32a1,1,0,0,1,0-2h8a1,1,0,0,1,0,2H39v13Zm-3-13H35v13h2Zm-34-6L24,4,45,15.438v2H3Zm37.541,0L24,6.886,7.4,15.438Z" transform="translate(-1 -4)" fill-rule="evenodd"/>
                  </svg>
                </span>
              </h2>
              <div id="option-1" class="accordion-collapse collapse show" data-bs-parent="#accordionPayment" style="">
                <div class="accordion-body">
                  <div class="mb-3">
                    <label class="form-label">Bank Account (iban)</label>
                    <input type="text" class="form-control" value="123 123 123 123" readonly>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="mb-3">
                        <label class="form-label">Name of Bank</label>
                        <input type="text" class="form-control" value="Al-rajhi" readonly>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="mb-3">
                        <label class="form-label">Other info</label>
                        <input type="text" class="form-control" value="123465" readonly>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="mb-3">
                        <label class="form-label">Other info</label>
                        <input type="text" class="form-control" value="123465" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="row py-4">
                    <div class="col-lg-6 mx-auto">
                      <!-- Upload image input-->
                      <div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow">
                        <input name="bank_bill" id="upload-img" type="file" onchange="readURL(this);" class="form-control border-0">
                        <label id="upload-label" for="upload-img" class="font-weight-light text-muted">Choose file</label>
                        <div class="input-group-append">
                          <label for="upload-img" class="btn btn-light m-0 rounded-pill px-4"> <i style="margin-right: 6px" class="fa fa-cloud-upload text-light"></i><small class="text-uppercase font-weight-bold text-light">Choose file</small></label>
                        </div>
                      </div>
                      <!-- Uploaded image area-->
                      <div class="image-area mt-4"><img id="imageResult" src="#" alt="" class="img-fluid rounded shadow-sm mx-auto d-block"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- PayPal -->
            <div class="accordion-item mb-3 border">
              <h2 class="h5 px-4 py-3 accordion-header d-flex justify-content-between align-items-center">
                <label data-bs-toggle="collapse" data-bs-target="#option-2" class="form-check w-100 collapsed form-check-label pt-1" for="payment2">
                <input class="form-check-input" type="radio" name="payment_method" id="payment2" value="paypal">
                  PayPal
                </label>
                <span>
                  <svg width="103" height="25" xmlns="http://www.w3.org/2000/svg">
                    <g fill="none" fill-rule="evenodd">
                      <path d="M8.962 5.857h7.018c3.768 0 5.187 1.907 4.967 4.71-.362 4.627-3.159 7.187-6.87 7.187h-1.872c-.51 0-.852.337-.99 1.25l-.795 5.308c-.052.344-.233.543-.505.57h-4.41c-.414 0-.561-.317-.452-1.003L7.74 6.862c.105-.68.478-1.005 1.221-1.005Z" fill="#009EE3"></path>
                      <path d="M39.431 5.542c2.368 0 4.553 1.284 4.254 4.485-.363 3.805-2.4 5.91-5.616 5.919h-2.81c-.404 0-.6.33-.705 1.005l-.543 3.455c-.082.522-.35.779-.745.779h-2.614c-.416 0-.561-.267-.469-.863l2.158-13.846c.106-.68.362-.934.827-.934h6.263Zm-4.257 7.413h2.129c1.331-.051 2.215-.973 2.304-2.636.054-1.027-.64-1.763-1.743-1.757l-2.003.009-.687 4.384Zm15.618 7.17c.239-.217.482-.33.447-.062l-.085.642c-.043.335.089.512.4.512h2.323c.391 0 .581-.157.677-.762l1.432-8.982c.072-.451-.039-.672-.38-.672H53.05c-.23 0-.343.128-.402.48l-.095.552c-.049.288-.18.34-.304.05-.433-1.026-1.538-1.486-3.08-1.45-3.581.074-5.996 2.793-6.255 6.279-.2 2.696 1.732 4.813 4.279 4.813 1.848 0 2.674-.543 3.605-1.395l-.007-.005Zm-1.946-1.382c-1.542 0-2.616-1.23-2.393-2.738.223-1.507 1.665-2.737 3.206-2.737 1.542 0 2.616 1.23 2.394 2.737-.223 1.508-1.664 2.738-3.207 2.738Zm11.685-7.971h-2.355c-.486 0-.683.362-.53.808l2.925 8.561-2.868 4.075c-.241.34-.054.65.284.65h2.647a.81.81 0 0 0 .786-.386l8.993-12.898c.277-.397.147-.814-.308-.814H67.6c-.43 0-.602.17-.848.527l-3.75 5.435-1.676-5.447c-.098-.33-.342-.511-.793-.511h-.002Z" fill="#113984"></path>
                      <path d="M79.768 5.542c2.368 0 4.553 1.284 4.254 4.485-.363 3.805-2.4 5.91-5.616 5.919h-2.808c-.404 0-.6.33-.705 1.005l-.543 3.455c-.082.522-.35.779-.745.779h-2.614c-.417 0-.562-.267-.47-.863l2.162-13.85c.107-.68.362-.934.828-.934h6.257v.004Zm-4.257 7.413h2.128c1.332-.051 2.216-.973 2.305-2.636.054-1.027-.64-1.763-1.743-1.757l-2.004.009-.686 4.384Zm15.618 7.17c.239-.217.482-.33.447-.062l-.085.642c-.044.335.089.512.4.512h2.323c.391 0 .581-.157.677-.762l1.431-8.982c.073-.451-.038-.672-.38-.672h-2.55c-.23 0-.343.128-.403.48l-.094.552c-.049.288-.181.34-.304.05-.433-1.026-1.538-1.486-3.08-1.45-3.582.074-5.997 2.793-6.256 6.279-.199 2.696 1.732 4.813 4.28 4.813 1.847 0 2.673-.543 3.604-1.395l-.01-.005Zm-1.944-1.382c-1.542 0-2.616-1.23-2.393-2.738.222-1.507 1.665-2.737 3.206-2.737 1.542 0 2.616 1.23 2.393 2.737-.223 1.508-1.665 2.738-3.206 2.738Zm10.712 2.489h-2.681a.317.317 0 0 1-.328-.362l2.355-14.92a.462.462 0 0 1 .445-.363h2.682a.317.317 0 0 1 .327.362l-2.355 14.92a.462.462 0 0 1-.445.367v-.004Z" fill="#009EE3"></path>
                      <path d="M4.572 0h7.026c1.978 0 4.326.063 5.895 1.45 1.049.925 1.6 2.398 1.473 3.985-.432 5.364-3.64 8.37-7.944 8.37H7.558c-.59 0-.98.39-1.147 1.449l-.967 6.159c-.064.399-.236.634-.544.663H.565c-.48 0-.65-.362-.525-1.163L3.156 1.17C3.28.377 3.717 0 4.572 0Z" fill="#113984"></path>
                      <path d="m6.513 14.629 1.226-7.767c.107-.68.48-1.007 1.223-1.007h7.018c1.161 0 2.102.181 2.837.516-.705 4.776-3.793 7.428-7.837 7.428H7.522c-.464.002-.805.234-1.01.83Z" fill="#172C70"></path>
                    </g>
                  </svg>
                </span>
              </h2>
              <div id="option-2" class="accordion-collapse collapse" data-bs-parent="#accordionPayment" style="">
                <div class="accordion-body">
                  <div class="px-2 col-lg-6 mb-3">
                    <h6 class="title">Online Payment Method</h6>
                    <p class="">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quod ratione molestiae vitae voluptatem!</p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Stripe -->
            <div class="accordion-item mb-3 border">
              <h2 class="h5 px-4 py-3 accordion-header d-flex justify-content-between align-items-center">
                <label data-bs-toggle="collapse" data-bs-target="#option-3" class="form-check w-100 collapsed form-check-label pt-1" for="payment3">
                <input class="form-check-input" type="radio" name="payment_method" id="payment3" value="stripe">
                  Stripe
                </label>
                <span class="logo">
                  <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                      width="103px" height="25pt" viewBox="0 0 600.000000 206.000000"
                      preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,206.000000) scale(0.100000,-0.100000)" fill="#635bff" stroke="none">
                      <path d="M470 1640 c0 -5 -13 -10 -30 -10 -16 0 -30 -4 -30 -10 0 -5 -9 -10
                      -20 -10 -11 0 -20 -4 -20 -10 0 -5 -8 -10 -18 -10 -35 0 -132 -120 -132 -164
                      0 -14 -4 -26 -10 -26 -6 0 -10 -40 -10 -100 0 -60 4 -100 10 -100 6 0 10 -13
                      10 -30 0 -16 5 -30 10 -30 6 0 10 -6 10 -14 0 -19 62 -82 89 -91 11 -3 21 -11
                      21 -16 0 -5 11 -9 24 -9 14 0 28 -5 31 -10 3 -5 19 -10 36 -10 16 0 29 -4 29
                      -10 0 -5 18 -10 39 -10 22 0 43 -4 46 -10 3 -5 24 -10 46 -10 21 0 39 -4 39
                      -10 0 -5 18 -10 40 -10 22 0 40 -4 40 -10 0 -5 14 -10 30 -10 17 0 30 -4 30
                      -10 0 -5 5 -10 10 -10 21 0 60 -49 60 -76 0 -26 -28 -55 -78 -81 -32 -17 -205
                      -17 -235 0 -32 17 -67 59 -67 79 0 9 -4 20 -10 23 -5 3 -10 15 -10 26 0 18 -8
                      19 -136 19 l-136 0 6 -47 c11 -98 18 -133 27 -133 5 0 9 -7 9 -15 0 -17 8 -29
                      55 -78 46 -48 83 -77 100 -77 8 0 15 -4 15 -10 0 -5 14 -10 30 -10 17 0 30 -4
                      30 -10 0 -5 20 -10 45 -10 25 0 45 -4 45 -10 0 -6 45 -10 114 -10 66 0 117 4
                      121 10 3 6 21 10 40 10 32 0 52 6 115 33 34 15 140 122 140 141 0 9 5 16 10
                      16 6 0 10 9 10 20 0 11 5 20 10 20 6 0 10 46 10 119 0 69 -4 122 -10 126 -5 3
                      -10 15 -10 26 0 10 -3 19 -8 19 -4 0 -13 13 -20 29 -7 17 -26 36 -43 43 -16 7
                      -29 16 -29 20 0 5 -7 8 -15 8 -8 0 -15 5 -15 10 0 6 -13 10 -30 10 -16 0 -30
                      5 -30 10 0 6 -13 10 -30 10 -16 0 -30 5 -30 10 0 6 -17 10 -37 10 -21 0 -47 7
                      -57 15 -11 8 -29 15 -40 15 -11 0 -33 4 -50 9 -17 6 -57 17 -88 26 -31 9 -66
                      24 -77 34 -51 44 -2 141 71 141 10 0 18 5 18 10 0 6 23 10 50 10 28 0 50 -4
                      50 -10 0 -5 12 -10 26 -10 30 0 93 -63 94 -95 1 -48 19 -55 147 -55 98 0 123
                      3 134 16 10 12 10 20 1 35 -7 10 -12 39 -12 64 0 25 -4 45 -10 45 -5 0 -10 11
                      -10 25 0 14 -4 25 -10 25 -5 0 -10 6 -10 13 0 23 -90 107 -115 107 -8 0 -15 5
                      -15 10 0 6 -9 10 -20 10 -11 0 -20 5 -20 10 0 6 -13 10 -30 10 -16 0 -30 5
                      -30 10 0 6 -62 10 -165 10 -103 0 -165 -4 -165 -10z"/>
                      <path d="M1147 1623 c-4 -3 -7 -57 -7 -120 l0 -113 165 0 165 0 0 -440 0 -440
                      135 0 135 0 0 440 0 440 165 0 165 0 0 120 0 120 -458 0 c-252 0 -462 -3 -465
                      -7z"/>
                      <path d="M2140 1070 l0 -560 130 0 130 0 0 210 0 210 129 0 c76 0 132 -4 136
                      -10 3 -5 15 -10 26 -10 24 0 49 -22 49 -44 0 -8 5 -18 10 -21 6 -4 10 -58 10
                      -129 0 -67 4 -126 9 -131 5 -6 11 -23 13 -40 l3 -30 153 -3 152 -3 0 29 c0 16
                      -9 37 -19 46 -16 15 -21 36 -25 114 -12 225 -21 267 -71 317 -29 28 -31 35
                      -11 35 17 0 86 106 86 132 0 9 5 20 10 23 24 15 3 235 -22 235 -5 0 -8 9 -8
                      20 0 26 -103 130 -129 130 -12 0 -21 5 -21 10 0 6 -15 10 -34 10 -19 0 -38 5
                      -41 10 -4 6 -127 10 -336 10 l-329 0 0 -560z m607 288 c30 -29 33 -38 33 -87
                      -1 -62 -23 -101 -59 -101 -12 0 -21 -4 -21 -10 0 -6 -57 -10 -150 -10 l-150 0
                      0 120 0 120 157 0 157 0 33 -32z"/>
                      <path d="M3220 1070 l0 -560 135 0 135 0 0 560 0 560 -135 0 -135 0 0 -560z"/>
                      <path d="M3660 1070 l0 -560 135 0 135 0 0 190 0 190 160 0 c100 0 160 4 160
                      10 0 6 20 10 45 10 25 0 45 5 45 10 0 6 9 10 20 10 11 0 20 5 20 10 0 6 7 10
                      15 10 22 0 84 59 100 95 22 50 28 85 32 203 4 97 1 121 -18 175 -27 75 -60
                      121 -99 137 -17 7 -30 16 -30 21 0 5 -9 9 -20 9 -11 0 -20 5 -20 10 0 6 -18
                      10 -39 10 -22 0 -43 5 -46 10 -4 6 -115 10 -301 10 l-294 0 0 -560z m520 310
                      c0 -6 11 -15 25 -20 24 -9 55 -65 55 -98 0 -33 -43 -112 -62 -112 -10 0 -18
                      -4 -18 -10 0 -6 -48 -10 -125 -10 l-125 0 0 130 0 130 125 0 c79 0 125 -4 125
                      -10z"/>
                      <path d="M4660 1070 l0 -560 435 0 436 0 -3 112 -3 113 -297 3 -298 2 0 115 0
                      115 260 0 261 0 -3 123 -3 122 -257 3 -258 2 0 85 0 85 284 0 c250 0 285 2
                      290 16 3 9 6 55 6 104 0 49 -3 95 -6 104 -5 14 -52 16 -425 16 l-419 0 0 -560z"/>
                    </g>
                  </svg>
                </span>
              </h2>
              <div id="option-3" class="accordion-collapse collapse" data-bs-parent="#accordionPayment" style="">
                <div class="accordion-body">
                  <div class="px-2 col-lg-6 mb-3">
                    {{-- <h6 class="title">Online Payment Method</h6>
                    <p class="">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Quod ratione molestiae vitae voluptatem!</p> --}}
                    <div class="form-group mb-3">
                      <label for="">Name</label>
                      <input type="text" name="name" id="card-holder-name" class="form-control" value="" placeholder="Name on the card">
                    </div>
                    <div class="form-group">
                      <label for="">Card details</label>
                      <div id="card-element"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Right -->
        <div class="col-lg-3">
          <div class="card position-sticky top-0">
            <div class="p-3 bg-opacity-10">
              <h6 class="card-title mb-3">Order Summary</h6>
              <div class="d-flex justify-content-between mb-1 small">
                <span>{{ __('Price') }}</span> <span>{{ config("pricing.CURRENCY") }}
                  {{-- @if(auth()->user()->get_remaining_paid_value() > 0)
                    {{ $plan->price - auth()->user()->get_remaining_paid_value() > 0 ? $plan->price - auth()->user()->get_remaining_paid_value(): 0 }}
                  @else --}}
                    {{ $plan->price }}
                  {{-- @endif --}}
                </span>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
                </symbol>
                <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                </symbol>
                <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                </symbol>
              </svg>
              <div id="alerts">
              </div>
              <div id="coupon-holder" class="d-flex justify-content-between mb-1 small d-none">
                <span>{{ __('Coupon') }} (Code: <span id="coupon-code">XXXX</span>)</span> <span class="text-danger">-{{ config("pricing.CURRENCY") }}<span id="coupon-price">5</span></span>
              </div>
              <div class="input-group my-3">
                <input name="coupon" type="text" id="coupon-input" class="form-control form-control-sm" placeholder="Enter Coupon">
                <div class="input-group-append">
                  <button id="coupon-btn" type="button" class="btn"><i class="fa fa-send"></i></button>
                </div>
              </div>
              <hr>
              <div class="d-flex justify-content-between mb-4 small">
                <span>TOTAL</span>
                <strong class="text-dark">
                  {{ config("pricing.CURRENCY") }}
                  <?php /* $price =
                    auth()->user()->get_remaining_paid_value() > 0 &&
                    $plan->price - auth()->user()->get_remaining_paid_value() > 0 ?
                    $plan->price - auth()->user()->get_remaining_paid_value():
                    $plan->price; */?>

                  <span id="total-price" data-price="{{ $plan->price }}">{{ $plan->price }}</span>
                  {{-- <span id="total-price" data-price="$price">$price</span> --}}
                </strong>
              </div>
              {{-- <div class="form-check mb-1 small">
                <input class="form-check-input" type="checkbox" value="" id="tnc">
                <label class="form-check-label" for="tnc">
                  I agree to the <a href="#">terms and conditions</a>
                </label>
              </div>
              <div class="form-check mb-3 small">
                <input class="form-check-input" type="checkbox" value="" id="subscribe">
                <label class="form-check-label" for="subscribe">
                  Get emails about product updates and events. If you change your mind, you can unsubscribe at any time. <a href="#">Privacy Policy</a>
                </label>
              </div> --}}
              <button type="submit" id="card-button" data-secret="{{ $intent->client_secret }}" class="btn btn-primary w-100 mt-2">{{ __('Order Now') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection

@section("scripts")
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
  <script src="https://js.stripe.com/v3/"></script>
  <script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}')

    const elements = stripe.elements()
    const cardElement = elements.create('card')

    cardElement.mount('#card-element')

    const form = document.getElementById('payment-form')
    const cardBtn = document.getElementById('card-button')
    const cardHolderName = document.getElementById('card-holder-name')

    form.addEventListener('submit', async (e) => {
      if (document.querySelector("input#payment3").checked == true) {
        e.preventDefault()

        cardBtn.disabled = true
        const { setupIntent, error } = await stripe.confirmCardSetup(
          cardBtn.dataset.secret, {
            payment_method: {
              card: cardElement,
              billing_details: {
                name: cardHolderName.value
              }
            }
          }
        )

        if(error) {
          cardBtn.disable = false
        } else {
          let token = document.createElement('input')
          token.setAttribute('type', 'hidden')
          token.setAttribute('name', 'token')
          token.setAttribute('value', setupIntent.payment_method)
          form.appendChild(token)
          form.submit();
        }
      }
    })
  </script>
  <script>

    /*  ==========================================
        SHOW UPLOADED IMAGE
    * ========================================== */
    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#imageResult').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
      }
    }

    $(function () {
      $('#upload-img').on('change', function () {
        readURL(input);
      });
    });

    $("#coupon-btn").click(function () {

      if ($("#coupon-input").val() != null && $("#coupon-input").val().length == 6) {

        $("#coupon-input").removeClass("border-danger");

        let btn = $(this);
        btn.attr("disabled", true);

        let code = $("#coupon-input").val();

        $.ajax({
          url: "{{ route("check_coupon") }}",
          headers: {
            "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr('content'),
          },
          method: "POST",
          data: {
            "code": code,
            "package": {{ $plan->id }},
          },
          success: function (res) {
            btn.attr("disabled", false);
            $("#coupon-price").text(res.amount);
            if ($("#total-price").data("price") - res.amount > 0) {
              $("#total-price").text($("#total-price").data("price") - res.amount);
            }else {
              $("#total-price").text(0);
            }
            $("#coupon-holder").removeClass("d-none");
            $("#coupon-code").text(code);
          },
          error: function (err) {
            btn.attr("disabled", false);
            $("#coupon-holder").addClass("d-none");
            $("#total-price").text($("#total-price").data("price"));
            $("#coupon-input").addClass("border-danger");
            let alert = makeErrorAlert("Invalid Coupon");
            setTimeout(() => {
              $("#coupon-input").removeClass("border-danger");
              alert.close();
            }, 1250);
          },
        });
      }else {
        $("#coupon-input").addClass("border-danger");
      }
    });

    function makeErrorAlert(msg) {
      let id = Date.now();
      let el = `
      <div id="${id}" class="action-alert text-danger px-2 py-0" role="alert" style="margin-bottom:-13px">
        <svg class="bi flex-shrink-0 me-1" width="14" height="14" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
        ${msg}
      </div>`;
      $("#alerts").html(el);
      var myAlert = document.getElementById(id);
      return new bootstrap.Alert(myAlert);
    }

  </script>
@endsection
