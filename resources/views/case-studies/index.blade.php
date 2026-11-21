@extends('layout.master')

@section('styles')
  <style>
    .case .title {
      font-size: 18px;
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
    a {
      color: #0d4b45;
    }
    html[lang="ar"] .carousel-item {
      margin-right: 0;
    }
    html[lang="ar"] .carousel-control-next {
      left: 0;
      right: unset;
    }
  </style>
@endsection

@section('content')
  @if($best_cases != null && count($best_cases) > 0)
    <div style="background-color: #0d4b45;margin-top:-1.5rem" class="py-3 pb-5 text-white">
      <div class="container">
        <h2 class="text-center my-5">{{ __('Case Studies to Inspire Your Marketing') }}</h2>
        <div id="BestCases" class="carousel carousel-dark slide">
          <div class="carousel-inner">
            @foreach($best_cases as $i => $case)
              <div class="carousel-item @if($i == 0) active @endif">
                <div class="py-4" style="color:white;margin-top:-24px;">
                  <div class="row align-items-center">
                    <div class="col-md-2"></div>
                    <div class="col-md-3">
                      <div class="box">
                        <a href="{{ route("case-studies.show", $case->id) }}" style="color: inherit;text-decoration: none">
                          <img
                              src="{{ url("images/uploads/" . $case->image) }}"
                              alt="{{ $case->name }} Case Study Image"
                              class="img-fluid box-img shadow">
                        </a>
                      </div>
                    </div>
                    <div class="col-md-5 text-white">
                      <p>{{ __('Featured Case Study') }}</p>
                      <h2 class="case-name">
                        <a href="{{ route("case-studies.show", $case->id) }}" style="color: inherit;text-decoration: none">{{ $case->name }}</a>
                      </h2>
                      <p class="lead mb-4">{{ $case->job_title }} / {{ $case->person }}</p>
                    </div>
                    <div class="col-md-2"></div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#BestCases" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('Previous') }}</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#BestCases" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('Next') }}</span>
          </button>
        </div>
      </div>
    </div>
  @endif
  <div class="container">
    <h2 class="text-center mt-5">{{ __('Case Studies') }}</h2>
    @if(auth()->check() && auth()->user()->is_admin())
      <a href="{{ route("case-studies.create") }}" class="btn btn-primary">
        {{ __('add new') }} <i class="fa fa-plus"></i>
      </a>
    @endif
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-10">
        <div class="row my-5">
          @if(count($cases) > 0)
            @foreach ($cases as $case)
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="case p-3 border shadow">
                  <img src="{{ url("images/uploads/" . $case->image) }}" alt="{{ $case->name }}" style="width: 60px;max-height:60px;object-fit:cover;" class="img-fluid d-block rounded-circle mb-3 border">
                  <p class="mb-2 mt-1 m-2" style="line-height: 0.8">
                    <b class="m-0">{{ $case->person }}</b><br>
                    <small class="text-secondary">{{ $case->job_title }}</small>
                  </p>
                  <h5 class="title">{{ $case->name }}</h4>
                  <a class="text-decoration-none" href="{{ route("case-studies.show", $case->id) }}">{{ __('View Case Study') }} <i class="mx-2 fa fa-arrow-right"></i></a>
                  @if (auth()->check() && auth()->user()->is_admin())
                    <div class="row mt-3">
                      <div class="col-md-6">
                        <a href="{{ route("case-studies.edit", $case->id) }}" class="w-100 btn btn-success">{{ __('edit') }} <i class="fa fa-edit"></i></a>
                      </div>
                      <div class="col-md-6">
                        <a href="{{ route("case-studies.delete", $case->id) }}" class="w-100 btn btn-danger">{{ __('delete') }} <i class="fa fa-trash"></i></a>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
            @else
            <p class="alert alert-danger">{{ __('There Is No Cases To Show') }}</p>
            @endif
        </div>
      </div>
      <div class="col-md-1"></div>
    </div>
  </div>

@endsection
