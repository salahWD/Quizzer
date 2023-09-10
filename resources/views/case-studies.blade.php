@extends('layout.master')

@section('styles')
  <style>
    h3 {
      font-size: 30px;
      line-height: 33px;
    }
    .mb32 {
      margin-bottom: 32px;
    }
    p, span {
      font-weight: 400;
    }
    img {
      max-width: 100%;
    }
    .section {
      padding: 96px 0;
      position: relative;
      overflow: hidden;
    }
    .align-children {
      display: -webkit-flex;
      display: -moz-flex;
      display: -ms-flexbox;
      display: flex;
      align-items: center;
      -webkit-align-items: center;
      justify-content: center;
      -webkit-justify-content: center;
      flex-direction: row;
      -webkit-flex-direction: row;
    }
    .widget .text-center .img-responsive {
      margin-left: auto;
      margin-right: auto;
    }
  </style>
@endsection

@section('content')

  @foreach (config("case_studies.CASES") as $case)
    <section style="background: {{ $case['BG'] }}; color: {{ $case['TEXT_COLOR'] }}">
      <div class="container">
        <div class="row align-children">
          <div class="col-md-7 col-sm-6 text-center mb-xs-24" @if($case['IMG_POS'] == 'R') style="order: 1" @endif>
            <img class="img-responsive" alt="{{ $case["NAME"] }}" src="{{ $case['IMAGE'] }}">
          </div>
          <div class="col-md-4 col-md-offset-1 col-sm-5 col-sm-offset-1">
            <div class="">
              <h3>{{ $case["NAME"] }}</h3>
              <div class="mb32">
                <p>{{ $case["DESC"] }}</p>
              </div>
              <a class="btn btn-lg btn-filled" href="{{ $case["BTN"]["LINK"] }}">{{ $case["BTN"]["TEXT"] }}</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endforeach

@endsection
