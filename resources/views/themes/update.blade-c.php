@extends("layout.super-admin")

@section('styles')

<!-- Selected Font -->
<link rel="stylesheet" href="{{ $fonts[$theme->font_family]["url"] }}">

<style>
  * {
    box-sizing: border-box;
  }

  .quiz-options {
    height: calc(100vh - 242px);
  }

  .answer {
    color: #222;
  }

  .quiz-preview {
    font-family: {
        {
        $fonts[$theme->font_family]["name"]
      }
    }

    ;
  }

  .quiz-preview .quiz-content {
    height: calc(100vh - 145px);
  }
</style>
@endsection

@section("content")

<div class="quiz-preview" id="quizPreview" style="{{ $theme->main_text_color != null ? "color: $theme->main_text_color" : "" }}">
  <div class="quiz-bg-color" id="quiz-bg-color" style="{{ "background-color: $theme->background_color" }}"></div>
  <div class="quiz-bg-img" id="quiz-bg-img" style="{{ "background-image: url('" . url("images/uploads/$theme->image") . "')" }};{{"opacity: " . $theme->image_opacity / 100 }}"></div>
  <div class="container quiz-content pb-5">

    <div class="d-flex flex-column justify-content-start py-4 px-5 m-auto" style="height: fit-content">
      <span class="question" id="questionTitlePreview">{{ __('question title') }}</span>
      <span class="questionDesc mb-2" id="questionDescPreview">{{ __('description') }}</span>
      <div class="media-container d-none" id="img-preview"></div>
      <div class="answers my-4" id="answersContainerPreview">
        <div class="answer" style="color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
          <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
          <div class="answer-letter" style="border-color: {{ $theme->border_color }}">A</div>
          <p class="text m-0" id="Answer-1-preview">{{ __('Answer Text') }}</p>
        </div>
        <div class="answer hover" style="color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
          <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
          <div class="answer-letter" style="border-color: {{ $theme->border_color }}">A</div>
          <p class="text m-0" id="Answer-2-preview">{{ __('Answer Text') }}</p>
        </div>
      </div>
      <div class="answers img-answers mb-4" id="answersContainerPreview">
        <div class="answer img-answer card border-0" style="min-width: 200px;color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
          <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
          <img class="img card-img-top" src="http://placehold.it/300" width="200" height="200">
          <p class="text m-0" id="Answer-3-preview">{{ __('Answer Text') }}</p>
        </div>
        <div class="answer img-answer card border-0" style="min-width: 200px;color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
          <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
          <img class="img card-img-top" src="http://placehold.it/300" width="200" height="200">
          <p class="text m-0" id="Answer-4-preview">{{ __('Answer Text') }}</p>
        </div>
        <div class="answer img-answer hover card border-0" style="min-width: 200px;color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
          <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
          <img class="img card-img-top" src="http://placehold.it/300" width="200" height="200">
          <p class="text m-0" id="Answer-3-preview">{{ __('Answer Text') }}</p>
        </div>
      </div>
      <button class="btn d-inline-block ml-auto" style="background: {{ $theme->btn_color }};color: {{ $theme->btn_text_color }}" type="button" id="nextQuestionBtn">{{ __('Submit') }}</button>
      <div style="background: white; padding: 40px; color: black" id="resultCard" class="d-flex mt-3 flex-column justify-content-center rounded">
        <span class="question" id="resultTitlePreview">{{ __('result title preview') }}</span>
        <span class="questionDesc mb-2" id="resultDescPreview">{{ __('result description preview') }}</span>
        <a class="btn d-inline-block m-auto" style="background: {{ $theme->result_btn_color }};color: {{ $theme->result_btn_text_color }}" type="button" target="_blank" id="result-btn" href="">{{ __('Submit') }}</a>
      </div>
    </div>

  </div>

</div>

@endsection

@section('options')

<div class="sidebar">
  <header class="bg-light">
    <div class="row">
      <button class="col-6 btn py-2 page active" data-page="design_form">{{ __('Design') }}</button>
      <button class="col-6 btn py-2 page" data-page="theme_form">{{ __('Themes') }}</button>
    </div>
  </header>
  <div class="quiz-options bg-white">
    <form action="{{ route("update_theme", compact("theme")) }}" id="theme_form" method="POST">
      @csrf
      <div class="content">
        <div class="row">
          @foreach ($themes as $theme)
          <div class="col-6">
            <div class="card theme" style="background: {{$theme["background_color"]}}">
              <input type="radio" class="theme-input d-none" name="theme_id" value="{{$theme["id"]}}">
              <div>
                <i class="icon fa fa-check"></i>
              </div>
              <p class="theme-name" style="color:#f2f2f2;">{{ $theme["name"] }}</p>
              <div class="color-plate" style="background: {{ $theme["highlight_color"] }}"></div>
              <h4 class="title question" style="color:{{$theme["text_color"]}}; font-family:{{ $fonts[$theme["font_family"]]["name"] }};">{{ __('Question') }}</h4>
              <p class="lead answer" style="color: {{ $theme["text_color"] }}; font-family: {{ $fonts[$theme["font_family"]]["name"] }};">{{ __('Answer') }}</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </form>
    <form action="{{ route("update_theme", compact("theme")) }}" id="design_form" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="content">
        @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="list-unstyled">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            <hr>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="mb-3">
          <label for="titleInput" class="mb-1">{{ __('Customize the look & feel of your Content') }}:</label>
          @if (auth()->user()->can_translate())
          <div class="p-0">
            <ul class="nav nav-tabs w-100">
              @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
              <li class="nav-link w-50 text-center option-link @if($localeCode == app()->getLocale()) active @endif" data-option="{{$localeCode}}-fonts">{{ $properties['native'] }}</li>
              @endforeach
            </ul>
            <div class="option-container py-3 px-2 bg-white border border-top-0 @if(app()->getLocale() != " en") d-none @endif" id="en-fonts">
              <select class="form-select" id="font_family_en" name="font_family_en" aria-label="Font Families Select">
                @foreach ($fonts as $font)
                <option data-url="{{ $font["url"] }}" data-id="{{ $font["id"] }}" data-font="{{ $font["name"] }}" {{$font["id"] == $theme["font_family"] ? "selected": ""}} value="{{ $font["id"] }}">{{$font["name"]}}</option>
                @endforeach
              </select>
            </div>
            <div class="option-container py-3 px-2 bg-white border border-top-0 @if(app()->getLocale() != " ar") d-none @endif" id="ar-fonts">
              <select class="form-select" id="font_family_ar" name="font_family_ar" aria-label="Font Families Select">
                @foreach ($fonts_ar as $font)
                <option data-url="{{ $font["url"] }}" data-id="{{ $font["id"] }}" data-font="{{ $font["name"] }}" {{$font["id"] == $theme["font_family_ar"] ? "selected": "" }} value="{{ $font["id"] }}">{{$font["name"]}}</option>
                @endforeach
              </select>
            </div>
          </div>
          @else
          <select class="form-select" id="font_family" name="font_family_en" aria-label="Font Families Select">
            @foreach ($fonts as $font)
            <option data-url="{{ $font["url"] }}" data-id="{{ $font["id"] }}" data-font="{{ $font["name"] }}" {{$font["id"] == $theme["font_family"] ? "selected": ""}} value="{{ $font["id"] }}">{{$font["name"]}}</option>
            @endforeach
          </select>
          @endif
        </div>
        <hr>
        <div class="row">
          <div class="mb-3 col-6">
            <label for="mainTextColor" class="form-label">{{ __('Main Font Color') }}:</label>
            <input required type="color" name="main_text_color" class="w-100 form-control form-control-color" id="mainTextColor" value="{{ $theme->main_text_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="backgroundColor" class="form-label">{{ __('Background Color') }}:</label>
            <input required type="color" name="background_color" class="w-100 form-control form-control-color" id="backgroundColor" value="{{ $theme->background_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="buttonColor" class="form-label">{{ __('Button Color') }}:</label>
            <input required type="color" name="btn_color" class="w-100 form-control form-control-color" id="buttonColor" value="{{ $theme->btn_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="buttonTextColor" class="form-label">{{ __('Button Text Color') }}:</label>
            <input required type="color" name="btn_text_color" class="w-100 form-control form-control-color" id="buttonTextColor" value="{{ $theme->btn_text_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="borderColor" class="form-label">{{ __('Border Color') }}:</label>
            <input required type="color" name="border_color" class="w-100 form-control form-control-color" id="borderColor" value="{{ $theme->border_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="highlightColor" class="form-label">{{ __('Highlight Color') }}:</label>
            <input required type="color" name="highlight_color" class="w-100 form-control form-control-color" id="highlightColor" value="{{ $theme->highlight_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="answerBgColor" class="form-label">{{ __('Answer Background Color') }}:</label>
            <input required type="color" name="answer_bg_color" class="w-100 form-control form-control-color" id="answerBgColor" value="{{ $theme->answer_bg_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="answerFontColor" class="form-label">{{ __('Answer Font Color') }}:</label>
            <input required type="color" name="answer_text_color" class="w-100 form-control form-control-color" id="answerFontColor" value="{{ $theme->answer_text_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="resultBtnColor" class="form-label">{{ __('Result Button Color') }}:</label>
            <input required type="color" name="result_btn_color" class="w-100 form-control form-control-color" id="resultBtnColor" value="{{ $theme->result_btn_color }}" title="Choose a color">
          </div>
          <div class="mb-3 col-6">
            <label for="resultBtnTextColor" class="form-label">{{ __('Result Button Text Color') }}:</label>
            <input required type="color" name="result_btn_text_color" class="w-100 form-control form-control-color" id="resultBtnTextColor" value="{{ $theme->result_btn_text_color }}" title="Choose a color">
          </div>
          <hr>
          <div class="py-2 bg-white">
            <input type="file" name="image" id="imageInput" class="d-none" accept=".jpg,.png,.jpeg">
            <button type="button" onclick="imageInput.click();" class="btn btn-success">{{ __('Upload Image') }}</button>
            @if($theme->image != null)
            <button type="button" id="deleteImage" class="btn btn-danger"><i class="fa fa-trash"></i> {{ __("Delete Image") }}</button>
            @endif
            <span class="d-none">
              <span id="imageName"></span>
              <button type="button" id="removeImage" class="btn"><i class="fa fa-trash"></i></button>
            </span>
          </div>
          <div class="mb-4 bg-white">
            <label for="opacityRange" class="form-label">{{ __('Opacity Range') }}:</label>
            <div class="d-flex">
              <input required type="range" name="image_opacity" min=0 max=100 value="{{ $theme->image_opacity ?? 100 }}" data-reflect="opacityValue" class="form-range p-3" id="opacityRange">
              <p>%<span id="opacityValue">{{ $theme->image_opacity ?? 100 }}</span></p>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <footer class="py-2 bg-light">
    <button type="submit" class="btn btn-primary mx-4 ml-auto d-block" id="submit_btn" form="design_form">{{ __('Save Design') }}</button>
  </footer>
</div>

@endsection


@section('scripts')
<script src="http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script>
<script>
  $(".nav-link").each(function() {
    $(this).click(function() {
      $(this).addClass("active").siblings().removeClass("active");
      $("#" + $(this).data("option")).removeClass("d-none").siblings(".option-container").addClass("d-none");
    });
  });

  if ({
      {
        auth() - > user() - > can_translate()
      }
    }) {

    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
    $("#font_family_{{$localeCode}}").on("input", function() {
      WebFont.load({
        google: {
          families: [$(this).find("option[data-id='" + $(this).val() + "']").data("font")]
        }
      });
      $("#quizPreview").css({
        "font-family": $(this).find("option[data-id='" + $(this).val() + "']").data("font") + ", 'tahoma'"
      });
    });
    @endforeach

  } else {
    $("#font_family").on("input", function() {
      WebFont.load({
        google: {
          families: [$(this).find("option[data-id='" + $(this).val() + "']").data("font")]
        }
      });
      $("#quizPreview").css({
        "font-family": $(this).find("option[data-id='" + $(this).val() + "']").data("font") + ", 'tahoma'"
      });
    });
  }

  $("#mainTextColor").on("input", function() {
    $("#quizPreview").css({
      "color": $(this).val()
    });
  });
  $("#backgroundColor").on("input", function() {
    $("#quiz-bg-color").css({
      "background-color": $(this).val()
    });
  });
  $("#buttonColor").on("input", function() {
    $("#nextQuestionBtn").css({
      "background-color": $(this).val()
    });
  });
  $("#buttonTextColor").on("input", function() {
    $("#nextQuestionBtn").css({
      "color": $(this).val()
    });
  });
  $("#borderColor").on("input", function() {
    $(".answers .answer, .answers .answer-letter").css({
      "border-color": $(this).val()
    });
  });
  $("#highlightColor").on("input", function() {
    $(".answers .highlight").css({
      "background-color": $(this).val()
    });
  });
  $("#answerBgColor").on("input", function() {
    $(".answers .answer").css({
      "background-color": $(this).val()
    });
  });
  $("#answerFontColor").on("input", function() {
    $(".answers .answer, .answers .answer .text").css({
      "color": $(this).val()
    });
  });
  $("#resultBtnColor").on("input", function() {
    $("#result-btn").css({
      "background-color": $(this).val()
    });
  });
  $("#resultBtnTextColor").on("input", function() {
    $("#result-btn").css({
      "color": $(this).val()
    });
  });


  $("#deleteImage").click(function() {
    $("#imageInput").val("");
    $("#imageName").text("").parent().addClass("d-none");
    $("#deleteImage").addClass("d-none");
    $.ajax({
      type: "POST", // For jQuery < 1.9
      method: "POST",
      url: `{{ route("delete_design_image", $theme->id) }}`,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      data: {
        action: "remove",
      },
      success: function(res) {
        $("#quiz-bg-img").remove();
        console.log("Image Uploaded (:");
      },
      error: function(res) {
        console.error("something went wrong :(");
      },
    });
  });

  // upload and preview image logic
  $("#imageInput").on("change", function() {
    const file = $(this).get(0).files[0];
    if (file) {
      let url = URL.createObjectURL(file);
      $("#quiz-bg-img").data("img", $("#quiz-bg-img").css("background-image"));
      $("#quiz-bg-img").css("background-image", `url(${url})`);
    }
    $("#imageName").text(file.name.slice(-23));
    $("#imageName").parent().removeClass("d-none");
    $("#deleteImage").addClass("d-none");
  });
  $("#removeImage").click(function() {
    $("#imageInput").val("");
    $("#quiz-bg-img").css("background-image", $("#quiz-bg-img").data("img"));
    $("#imageName").text("");
    $("#imageName").parent().addClass("d-none");
    $("#deleteImage").removeClass("d-none");
  });

  // show live demo for opacity range value
  $('#opacityRange').on("input", function() {
    $("#" + $(this).data("reflect")).text($(this).val());
    $("#quiz-bg-img").css({
      "opacity": $(this).val() / 100,
    });
  });

  // sidebar pages active link
  let trig = true;
  $(".sidebar .page").each(function() {

    if ($(this).hasClass("active") && trig) {
      $("#" + $(this).data("page")).show();
      $("#submit_btn").attr("form", $(this).data("page"));
      trig = false;
    } else {
      $("#" + $(this).data("page")).hide();
    }
  });
  $(".sidebar .page").click(function() {
    $("#" + $(this).siblings().data("page")).hide();
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
    $("#" + $(this).data("page")).show();
    $("#submit_btn").attr("form", $(this).data("page"));
  });

  // themes selection logic
  $(".content .theme").click(function() {
    $(this).parent().siblings().find(".theme").removeClass("active");
    $(this).find(".theme-input").prop("checked", true);
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
    } else {
      $(this).addClass("active");
    }
  });
</script>
@endsection
