@extends('layout.master')

@section('styles')
  @vite(["resources/sass/case-study.scss"])
@endsection

@section('content')
  <div class="py-4" style="color:white;margin-top:-24px;background-image:url('{{ url("images/features-bg.png") }}')">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-2"></div>
        <div class="col-md-5">
          <h3 class="job-title mb-4">{{ $casestudy->job_title }}</h3>
          <h2 class="case-name">{{ $casestudy->name }}</h2>
          <p class="cta-1 mt-5">
            {{ __('Do you want to increase your lead capture, just like they did ?') }} {{ $casestudy->name }}
          </p>
          <a class="btn text-primary bg-white rounded-pill shadow btn-lg"
              href="{{ route("pricing") }}">{{ __('Grap Your 14 Free Day') }}</a>
        </div>
        <div class="col-md-3">
          <div class="box">
            <img
                src="{{ url("images/uploads/" . $casestudy->image) }}"
                alt="{{ $casestudy->name }} Case Study Image"
                class="img-fluid box-img shadow">
            <button type="button" class="btn btn-lg video-btn" data-bs-toggle="modal" data-src="{{ $casestudy->video }}" data-bs-target="#video-modal">
              {{ __('Watch Case Study Video') }}
            </button>
          </div>
        </div>
        <div class="col-md-2"></div>
      </div>
    </div>
  </div>

  {{-- Video Modal --}}
  <div class="modal fade" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-body">

          <button type="button" class="btn-close p-0" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item" src="" id="video" allowscriptaccess="always" allow="autoplay"></iframe>
          </div>

        </div>

      </div>
    </div>
  </div>

  <section class="story py-120">
    <div class="container">
      <div class="text-center">
        <h2 class="title">{{ __('THEIR STORY') }}</h2>
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <p class="lead" style="color: black"><b>{{ $casestudy->story_title }}</b></p>
            <p class="desc">{{ $casestudy->story }}</p>
          </div>
          <div class="col-md-2"></div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light py-100">
    <div class="container">
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <img src="{{ url("images/icon-case.png") }}" class="img-fluid rounded-start" alt="...">
                </div>
                <div class="col-md-9">
                  <h4 class="card-title">{{ __('THEIR GOAL') }}</h4>
                  <p class="card-text lead text-primary"><b>{{ $casestudy->goal_title }}</b></p>
                  <p class="desc">{{ $casestudy->goal }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2"></div>
      </div>
    </div>
  </section>

  <section class="bg-light py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div class="bg-light card border-0">
            <div class="card-body">
              <div class="row">
                <div class="col-md-9">
                  <h4 class="card-title">{{ __('THEIR SOLUTION') }}</h4>
                  <p class="card-text lead text-primary"><b>{{ $casestudy->quiz != null ? $casestudy->quiz->name : config('app.name') }}</b></p>
                  <p class="desc">{{ $casestudy->solution }}</p>
                </div>
                <div class="col-md-3">
                  <img
                    src="{{ url("images/uploads/" . $casestudy->solution_img) }}"
                    class="img-fluid box-img shadow-sm rounded-start"
                    alt="{{ $casestudy->name }} Case Study Solution">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2"></div>
      </div>
    </div>
  </section>

  <section class="keys py-100">
    <div class="container">
      <div class="text-center">
        <h2 class="title mb-5">{{ __('KEYS TO SUCCESS') }}</h2>
        <br>
      </div>
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div class="shadow-sm card border-0">
            <div class="card-body py-4">
              <div class="row">
                <div class="col-md-6">
                  <img
                      src="{{ url("images/uploads/" . $casestudy->key_1_img) }}"
                      class="box-img shadow-sm img-fluid rounded-start"
                      alt="{{ $casestudy->key_1_title }} Case Study Solution">
                </div>
                <div class="col-md-6">
                  <h4 class="card-title">{{ $casestudy->key_1_title }}</h4>
                  <p class="card-text lead">{{ $casestudy->key_1_desc }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-2"></div>
      </div>
      @if ($casestudy->key_2_title != null && $casestudy->key_2_img != null)
        <div class="row mt-5">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <div class="card bg-transparent border-0">
              <div class="card-body py-4">
                <div class="row">
                  <div class="col-md-6">
                    <img
                        src="{{ url("images/uploads/" . $casestudy->key_2_img) }}"
                        class="box-img shadow-sm img-fluid rounded-start"
                        alt="{{ $casestudy->key_2_title }} Case Study Solution">
                  </div>
                  <div class="col-md-6">
                    <h4 class="card-title">{{ $casestudy->key_2_title }}</h4>
                    <p class="card-text lead">{{ $casestudy->key_2_desc }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-2"></div>
        </div>
      @endif
      @if ($casestudy->key_3_title != null && $casestudy->key_3_img != null)
        <div class="row mt-5">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <div class="shadow-sm card border-0">
              <div class="card-body py-4">
                <div class="row">
                  <div class="col-md-6">
                    <img
                        src="{{ url("images/uploads/" . $casestudy->key_3_img) }}"
                        class="box-img shadow-sm img-fluid rounded-start"
                        alt="{{ $casestudy->key_3_title }} Case Study Solution">
                  </div>
                  <div class="col-md-6">
                    <h4 class="card-title">{{ $casestudy->key_3_title }}</h4>
                    <p class="card-text lead">{{ $casestudy->key_3_desc }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-2"></div>
        </div>
      @endif
      <div class="row mt-5">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div class="card shadow border-0">
            <div class="card-body py-5 text-center">
              <h4 class="card-title mb-4">{{ __('THEIR RESULTS') }}</h4>
              <p class="card-text desc">{{ $casestudy->result_desc }}</p>
              <h3 class="card-text title text-primary"><b>{{ $casestudy->result_title }}</b></h3>
            </div>
          </div>
        </div>
        <div class="col-md-2"></div>
      </div>
    </div>
  </section>

  <section class="py-100">
    <div class="container">
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
          <b class="text-center h4 d-block mb-4">{{ __('More Case Studies') }}</b>
          <div class="row my-5">
            @if ($more_cases != null && count($more_cases) > 0)
              @foreach ($more_cases as $case)
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                  <div class="case p-3 border shadow">
                    <img src="{{ url("images/uploads/" . $case->image) }}" alt="{{ $case->name }}" style="width: 60px;max-height:60px;object-fit:cover;" class="img-fluid d-block rounded-circle mb-3 border">
                    <p class="mb-2 mt-1 m-2" style="line-height: 0.8">
                      <b class="m-0">{{ $case->person }}</b><br>
                      <small class="text-secondary">{{ $case->job_title }}</small>
                    </p>
                    <h5 class="title">{{ $case->name }}</h4>
                    <a class="text-decoration-none" href="{{ route("case-studies.show", $case->id) }}">{{ __('View Case Study') }} <i class="mx-2 fa fa-arrow-right"></i></a>
                  </div>
                </div>
              @endforeach
            @else
              <div class="alert alert-warning">
                <h5>{{ __('No Case Studies To Show') }}</h5>
              </div>
            @endif
          </div>
        </div>
        <div class="col-md-1"></div>
      </div>
    </div>
  </section>

@endsection


@section('scripts')
  @vite(["resources/js/case-study.js"])
@endsection
