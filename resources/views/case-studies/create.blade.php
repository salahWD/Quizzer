@extends('layout.master')

@section('styles')
  <style>
    body {
      background: #f5f5f5;
    }
    .rounded-lg {
      border-radius: 1rem;
    }
    .nav-pills .nav-link {
      color: #555;
    }
    .nav-pills .nav-link.active {
      color: #fff;
    }
    .img-preview-holder {
      max-width: fit-content;
      margin: auto;
      position: relative;
      max-width: 180px;
      min-width: 140px;
      max-height: 180px;
      min-height: 140px;
      overflow: hidden;
    }
    .upload-img-btn {
      position: absolute;
      bottom: 15px;
      left: 50%;
      transform: translateX(-50%);
    }
    .img-preview-holder img {
      display: block;
      margin: auto;
      object-fit: cover;
      object-position: center center;
      width: 100%;
      height: 100%;
    }
  </style>
@endsection

@section('content')
  <div class="container py-5">

    <div class="row">
      <div class="col-lg-7 mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-5">

          <div class="tab-content">

            <div id="nav-tab-card" class="tab-pane fade show active">
              <h3 class="text-center mb-4title">{{ __('Create New Case Study') }}</h3>
              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <hr>

              <form action="{{ isset($casestudy) && $casestudy != null ? route("case-studies.update", $casestudy->id) : route("case-studies.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="img-preview-holder">
                  <button type="button" onclick="cstd_image.click()" class="btn btn-primary upload-img-btn">
                    <i class="fa fa-upload"></i>
                  </button>
                  <input type='file' name="image" id="cstd_image" class="d-none" onchange="readURL(this, 'cstd_image_preview');" />
                  <img id="cstd_image_preview" class="img-fluid mw-100" src="@if(isset($casestudy) && $casestudy != null) {{ url("images/uploads/" . $casestudy->image) }} @else http://placehold.it/180 @endif" alt="your image" />
                </div>
                <div class="form-group mt-3">
                  <label for="title">{{ __('Title') }}</label>
                  <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->name : old('name') }}" type="text" id="title" name="name" placeholder="Case Study Title" required class="form-control">
                </div>
                @if ($quizzes != null && count($quizzes) > 0)
                  <div class="form-group mt-3">
                    <label for="quiz">{{ __('Related Quiz') }}</label>
                    <select name="quiz" id="quiz" class="form-control">
                      <option>-- {{ __('choose a quiz') }} --</option>
                      @foreach ($quizzes as $quiz)
                        <option @if (isset($casestudy) && $casestudy != null && $casestudy->quiz_id != null && $casestudy->quiz_id == $quiz->id) selected @endif value="{{ $quiz->id }}">{{ $quiz->name }}</option>
                      @endforeach
                    </select>
                  </div>
                @endif
                <div class="form-group mt-3">
                  <label>{{ __('Person / Job title') }}</label>
                  <div class="row">
                    <div class="col-md-6">
                      <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->person ?? old('person') : old('person') }}" type="text" name="person" placeholder="Case Study Owner" required class="form-control">
                    </div>
                    <div class="col-md-6">
                      <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->job_title ?? old('job') : old('job') }}" type="text" name="job" placeholder="Job Title" required class="form-control">
                    </div>
                  </div>
                </div>
                <div class="form-group mt-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" @if((old("is_best") != null && old("is_best") == 1) || (isset($casestudy) && $casestudy->is_best == 1)) checked @endif id="is_best" name="is_best">
                    <label class="form-check-label" for="is_best">
                      {{ __('appears in slider') }}
                    </label>
                  </div>
                </div>
                <div class="form-group mt-3">
                  <label for="desc">{{ __('Description') }}</label>
                  <textarea class="form-control" id="desc" name="desc" placeholder="Description" required>{{ isset($casestudy) && $casestudy != null ? $casestudy->desc ?? old('desc') : old('desc') }}</textarea>
                </div>
                <div class="form-group mt-3">
                  <label for="video">{{ __('Video URL') }}</label>
                  <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->video ?? old('video') : old('video') }}" type="url" id="video" name="video" placeholder="Video (YT)" required class="form-control">
                </div>
                <div class="form-group mt-3">
                  <label for="story-title">{{ __('Their Story Title') }}</label>
                  <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->story_title ?? old('story-title') : old('story-title') }}" id="story-title" name="story-title" placeholder="Their Story Title" required class="form-control">
                </div>
                <div class="form-group mt-3">
                  <label for="story">{{ __('Their Story') }}</label>
                  <textarea class="form-control" id="srory" name="story" placeholder="Write Their Story" required>{{ isset($casestudy) && $casestudy != null ? $casestudy->story ?? old('story') : old('story') }}</textarea>
                </div>
                <div class="form-group mt-3">
                  <label for="goal-title">{{ __('Their Goal Title') }}</label>
                  <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->goal_title ?? old('goal-title') : old('goal-title') }}" id="goal-title" name="goal-title" placeholder="Their Goal Title" required class="form-control">
                </div>
                <div class="form-group mt-3">
                  <label for="goal">{{ __('Their Goal') }}</label>
                  <textarea class="form-control" id="goal" name="goal" placeholder="Write Their Goal" required>{{ isset($casestudy) && $casestudy != null ? $casestudy->goal ?? old('goal') : old('goal') }}</textarea>
                </div>
                <div class="form-group mt-3">
                  <label for="solution">{{ __('Their Solution') }}</label>
                  <textarea class="form-control" id="solution" name="solution" placeholder="Write Their Solution" required>{{ isset($casestudy) && $casestudy != null ? $casestudy->solution ?? old('solution') : old('solution') }}</textarea>
                </div>
                <div class="form-group mt-3">
                  <div class="img-preview-holder mb-1">
                    <img id="key1img_preview" src="{{ isset($casestudy) && $casestudy != null ? url("images/uploads/" . $casestudy->key_1_img) : "http://via.placeholder.com/180" }}" class="img-fluid d-block m-auto" style="max-width:180px">
                  </div>
                  <div class="input-group">
                    <label class="rounded-0 input-group-text" for="key-1-title">{{ __('Key 1') }}</label>
                    <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->key_1_title ?? old('keys-1-title') : old('keys-1-title') }}" type="text" class="form-control" id="key-1-title" name="keys-1-title" placeholder="Title">
                    <button type="button" onclick="key1img.click()" class="btn btn-primary">{{ __('image') }} <i class="fa fa-upload"></i></button>
                    <input type="file" class="d-none" id="key1img" name="keys-1-img" onchange="readURL(this, 'key1img_preview');">
                  </div>
                  <textarea style="border-top: 0;border-top-left-radius:0;border-top-right-radius:0" class="form-control" name="keys-1-desc" placeholder="{{ __('Write First key To Success') }}">{{ isset($casestudy) && $casestudy != null ? $casestudy->key_1_desc ?? old('keys-1-desc') : old('keys-1-desc') }}</textarea>
                </div>
                <div class="form-group mt-3">
                  <div class="img-preview-holder mb-1">
                    <img id="key2img_preview" src="{{ isset($casestudy) && $casestudy != null ? url("images/uploads/" . $casestudy->key_1_img) : "http://via.placeholder.com/180" }}" class="img-fluid d-block m-auto" style="max-width:180px">
                  </div>
                  <div class="input-group">
                    <label class="rounded-0 input-group-text" for="key-2-title">{{ __('Key 2') }}</label>
                    <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->key_2_title ?? old('keys-2-title') : old('keys-2-title') }}" class="form-control" id="key-2-title" name="keys-2-title" placeholder="Title">
                    <button type="button" onclick="key2img.click()" class="btn btn-primary">{{ __('image') }} <i class="fa fa-upload"></i></button>
                    <input type="file" class="d-none" id="key2img" name="keys-2-img" onchange="readURL(this, 'key2img_preview');">
                  </div>
                  <textarea style="border-top: 0;border-top-left-radius:0;border-top-right-radius:0" class="form-control" name="keys-2-desc" placeholder="{{ __('Write First key To Success') }}">{{ isset($casestudy) && $casestudy != null ? $casestudy->key_2_desc ?? old('keys-2-desc') : old('keys-2-desc') }}</textarea>
                </div>
                <div class="form-group mt-3">
                  <div class="img-preview-holder mb-1">
                    <img id="key3img_preview" src="{{ isset($casestudy) && $casestudy != null ? url("images/uploads/" . $casestudy->key_1_img) : "http://via.placeholder.com/180" }}" class="img-fluid d-block m-auto" style="max-width:180px">
                  </div>
                  <div class="input-group">
                    <label class="rounded-0 input-group-text" for="key-3-title">{{ __('Key 3') }}</label>
                    <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->key_3_title ?? old('keys-3-title') : old('keys-3-title') }}" type="text" class="form-control" id="key-3-title" name="keys-3-title" placeholder="Title">
                    <button type="button" onclick="key3img.click()" class="btn btn-primary">{{ __('image') }} <i class="fa fa-upload"></i></button>
                    <input type="file" class="d-none" id="key3img" name="keys-3-img" onchange="readURL(this, 'key3img_preview');">
                  </div>
                  <textarea style="border-top: 0;border-top-left-radius:0;border-top-right-radius:0" class="form-control" name="keys-3-desc" placeholder="{{ __('Write First key To Success') }}">{{ isset($casestudy) && $casestudy != null ? $casestudy->key_3_title ?? old('keys-3-title') : old('keys-3-title') }}</textarea>
                </div>
                <div class="form-group mt-3">
                  <label for="result-title">{{ __('Result Title') }}</label>
                  <input value="{{ isset($casestudy) && $casestudy != null ? $casestudy->result_title ?? old('result-title') : old('result-title') }}" id="result-title" name="result-title" placeholder="Case Study Result Title" required class="form-control">
                </div>
                <div class="form-group mt-3">
                  <label for="result-desc">{{ __('Result Description') }}</label>
                  <textarea class="form-control" id="result-desc" name="result-desc" placeholder="Case Study Result Description" required>{{ isset($casestudy) && $casestudy != null ? $casestudy->result_desc ?? old('result-desc') : old('result-desc') }}</textarea>
                </div>
                <button class="btn mt-3 btn-primary d-block shadow">{{ __('Save') }} <i class="mx-2 fa fa-save"></i></button>
              </form>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

@section("scripts")
  <script>
    function readURL(input, id) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(id).setAttribute('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endsection
