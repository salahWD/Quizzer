@extends("layout.super-admin")

@section("styles")
  <style>
    .theme.card {
      outline: 2px solid transparent;
      transition: 0.2s;
    }
    .quiz-preview .quiz-bg-img {
      position: absolute;
      width: 100%;
      height: 100%;
      inset: 0;
      background-position: center center;
      background-repeat: no-repeat;
      background-size: cover;
    }
    .theme.card:hover {
      outline-color: #7bbbff;
    }
    .link {
      color: inherit;
      text-decoration: none;
    }
    #quizPreview,
    .sidebar {
      height: calc(100dvh - 56px);
    }
    .quiz-options {
      height: calc(100dvh - 156px) !important;
    }
  </style>
@endsection


@section("content")

  <div class="quiz-preview" id="quizPreview" style="{{ $theme->main_text_color != null ? "color: $theme->main_text_color" : "" }}">
    <div class="quiz-bg-color" id="quiz-bg-color" style="{{ "background-color: $theme->background_color" }}"></div>
    <div class="quiz-bg-img" id="quiz-bg-img" style="{{ "background-image: url('" . url("images/uploads/$theme->image") . "')" }};{{"opacity: " . $theme->image_opacity / 100 }}"></div>
    <div class="container quiz-content pb-5">

      <div class="d-flex flex-column justify-content-start py-4 px-5 m-auto" style="height: fit-content">
        <span class="question" id="questionTitlePreview">english question</span>
        <span class="questionDesc mb-2" id="questionDescPreview">desc</span>
        <div class="media-container d-none" id="img-preview"></div>
        <div class="answers my-4" id="answersContainerPreview">
          <div class="answer" style="color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
            <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
            <div class="answer-letter" style="border-color: {{ $theme->border_color }}">A</div>
            <p class="text m-0" id="Answer-1-preview">Answer Text</p>
          </div>
          <div class="answer hover" style="color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
            <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
            <div class="answer-letter" style="border-color: {{ $theme->border_color }}">A</div>
            <p class="text m-0" id="Answer-2-preview">Answer Text</p>
          </div>
        </div>
        <div class="answers img-answers mb-4" id="answersContainerPreview">
          <div class="answer img-answer card border-0" style="min-width: 200px;color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
            <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
            <img class="img card-img-top" src="http://placehold.it/300" width="200" height="200">
            <p class="text m-0" id="Answer-3-preview">asd</p>
          </div>
          <div class="answer img-answer card border-0" style="min-width: 200px;color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
            <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
            <img class="img card-img-top" src="http://placehold.it/300" width="200" height="200">
            <p class="text m-0" id="Answer-4-preview">asd</p>
          </div>
          <div class="answer img-answer hover card border-0" style="min-width: 200px;color:{{ $theme->answer_text_color }};{{ $theme->answer_bg_color != null ? "background-color: $theme->answer_bg_color" : "" }}">
            <div class="highlight" style="{{ $theme->highlight_color != null ? "background-color: $theme->highlight_color" : "" }}"></div>
            <img class="img card-img-top" src="http://placehold.it/300" width="200" height="200">
            <p class="text m-0" id="Answer-3-preview">asd</p>
          </div>
        </div>
        <button class="btn d-inline-block" style="margin-left:auto;width:fit-content;background: {{ $theme->btn_color }};color: {{ $theme->btn_text_color }}" type="button" id="nextQuestionBtn">Submit</button>
        <div style="background: white; padding: 40px; color: black" id="resultCard" class="d-flex mt-3 flex-column justify-content-center rounded">
          <span class="question" id="resultTitlePreview">testtesttesttest</span>
          <span class="questionDesc mb-2" id="resultDescPreview">testtesttesttest</span>
          <a class="btn d-inline-block m-auto" style="background: {{ $theme->result_btn_color }};color: {{ $theme->result_btn_text_color }}" type="button" target="_blank" id="result-btn" href="">Submit</a>
        </div>
      </div>

    </div>

  </div>

@endsection

@section('options')

  <div class="sidebar">
    <header class="bg-light">
      <div class="row">
        <button class="col-6 btn py-2 page active" data-page="design_form">Design</button>
        <button class="col-6 btn py-2 page" data-page="theme_form">Themes</button>
      </div>
    </header>
    <div class="quiz-options bg-white">
      <form action="{{ route("delete_theme", $theme->id) }}" id="delete_form" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $theme->id }}">
      </form>
      <form action="{{ route("update_theme", $theme->id) }}" id="design_form" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $theme->id }}">
        <div class="content">
          <p class="lead">Customize the look & feel of your Content.</p>
          <hr>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="is_public" id="is_public" value="1" @if($theme->is_public == 1) checked @endif>
            <label class="form-check-label" for="is_public">allow to the public</label>
          </div>
          <hr>
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
            <label for="titleInput" class="mb-1">Font Family:</label>
            <select class="form-select" name="font_family" aria-label="Font Families Select">
              @foreach ($fonts as $font)
                <option {{$font["id"] == $theme["font_family"] ? "selected": ""}} value="{{ $font["id"] }}">{{$font["name"]}}</option>
              @endforeach
            </select>
          </div>
          <hr>
          <div class="row">
            <div class="mb-3 col-6">
              <label for="mainTextColor" class="form-label">Main Font Color:</label>
              <input
                  required type="color" name="main_text_color"
                  class="w-100 form-control form-control-color"
                  value="{{ old("main_text_color") ?? $theme["main_text_color"] }}"
                  id="mainTextColor" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="backgroundColor" class="form-label">Background Color:</label>
              <input required type="color" name="background_color" class="w-100 form-control form-control-color" id="backgroundColor" value="{{ old("background_color") ?? $theme["background_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="buttonColor" class="form-label">Button Color:</label>
              <input required type="color" name="btn_color" class="w-100 form-control form-control-color" id="buttonColor" value="{{ old("btn_color") ?? $theme["btn_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="buttonTextColor" class="form-label">Button Text Color:</label>
              <input required type="color" name="btn_text_color" class="w-100 form-control form-control-color" id="buttonTextColor" value="{{ old("btn_text_color") ?? $theme["btn_text_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="borderColor" class="form-label">Border Color:</label>
              <input required type="color" name="border_color" class="w-100 form-control form-control-color" id="borderColor" value="{{ old("border_color") ?? $theme["border_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="highlightColor" class="form-label">Highlight Color:</label>
              <input required type="color" name="highlight_color" class="w-100 form-control form-control-color" id="highlightColor" value="{{ old("highlight_color") ?? $theme["highlight_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="answerBgColor" class="form-label">Answer Background Color:</label>
              <input required type="color" name="answer_bg_color" class="w-100 form-control form-control-color" id="answerBgColor" value="{{ old("answer_bg_color") ?? $theme["answer_bg_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="answerFontColor" class="form-label">Answer Font Color:</label>
              <input required type="color" name="answer_text_color" class="w-100 form-control form-control-color" id="answerFontColor" value="{{ old("answer_text_color") ?? $theme["answer_text_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="resultBtnColor" class="form-label">Result Button Color:</label>
              <input required type="color" name="result_btn_color" class="w-100 form-control form-control-color" id="resultBtnColor" value="{{ old("result_btn_color") ?? $theme["result_btn_color"] }}" title="Choose a color">
            </div>
            <div class="mb-3 col-6">
              <label for="resultBtnTextColor" class="form-label">Result Button Text Color:</label>
              <input required type="color" name="result_btn_text_color" class="w-100 form-control form-control-color" id="resultBtnTextColor" value="{{ old("result_btn_text_color") ?? $theme["result_btn_text_color"] }}" title="Choose a color">
            </div>
            <hr>
            <div class="py-2 bg-white">
              <input type="file" name="image" id="imageInput" class="d-none" accept=".jpg,.png,.jpeg">
              <button type="button" onclick="imageInput.click();" class="btn btn-success">Upload Image</button>
              @if($theme->image != null)
                <button type="button" id="deleteImage" class="btn btn-danger"><i class="fa fa-trash"></i> {{ __("Delete Image") }}</button>
              @endif
              <span class="d-none">
                <span id="imageName"></span>
                <button type="button" id="removeImage" class="btn"><i class="fa fa-trash"></i></button>
              </span>
            </div>
            <div class="mb-4 bg-white">
              <label for="opacityRange" class="form-label">Opacity Range:</label>
              <div class="d-flex">
                <input required type="range" name="image_opacity" min=0 max=100 value="{{ old("image_opacity") ?? $theme["image_opacity"] ?? 100 }}" data-reflect="opacityValue" class="form-range p-3" id="opacityRange">
                <p>%<span id="opacityValue">{{ $theme->image_opacity ?? 100 }}</span></p>
              </div>
            </div>
          </div>
        </div>
      </form>
      <div class="content" id="theme_form">
        <div class="row">
          @foreach ($themes as $theme)
            <div class="col-6">
              <div class="card theme" style="background-color: {{ $theme->background_color }}">
                {{-- <div class="card-img-bg" style="background: {{ $theme->image ? "url('" . $theme->image . "')" : "" }};opacity: {{ $theme->image_opacity / 100 }}"></div> --}}
                <a class="link" href="{{ route("update_theme", $theme->id) }}">
                  <input type="radio" class="theme-input d-none" name="theme_id" value="{{ $theme->id }}">
                  <div>
                    <i class="icon fa fa-check"></i>
                  </div>
                  <div class="color-plate" style="background: {{ $theme->highlight_color }}"></div>
                  <h4
                      class="title question"
                      style="color:{{ $theme->main_text_color }}; font-fanily:{{ $theme->font_family }};"
                      >Question</h4>
                  <p
                      class="lead answer"
                      style="color: {{ $theme->main_text_color }}; font-family: {{ $theme->font_family }};"
                      >Answer</p>
                </a>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    <footer class="py-2 bg-light d-flex">
      <div class="col-5">
        <button type="submit" class="btn w-100 btn-primary ml-auto d-block" id="submit_btn" form="design_form">Save Theme</button>
      </div>
      <div class="col-2"></div>
      <div class="col-5">
        <button type="submit" class="btn w-100 btn-danger ml-auto d-block" form="delete_form">Delete Theme</button>
      </div>
    </footer>
  </div>

@endsection


@section('scripts')
  <script>

    $("#mainTextColor").on("input", function () {
      $("#quizPreview").css({"color": $(this).val()});
    });
    $("#backgroundColor").on("input", function () {
      $("#quiz-bg-color").css({"background-color": $(this).val()});
    });
    $("#buttonColor").on("input", function () {
      $("#nextQuestionBtn").css({"background-color": $(this).val()});
    });
    $("#buttonTextColor").on("input", function () {
      $("#nextQuestionBtn").css({"color": $(this).val()});
    });
    $("#borderColor").on("input", function () {
      $(".answers .answer, .answers .answer-letter").css({"border-color": $(this).val()});
    });
    $("#highlightColor").on("input", function () {
      $(".answers .highlight").css({"background-color": $(this).val()});
    });
    $("#answerBgColor").on("input", function () {
      $(".answers .answer").css({"background-color": $(this).val()});
    });
    $("#answerFontColor").on("input", function () {
      $(".answers .answer, .answers .answer .text").css({"color": $(this).val()});
    });
    $("#resultBtnColor").on("input", function () {
      $("#result-btn").css({"background-color": $(this).val()});
    });
    $("#resultBtnTextColor").on("input", function () {
      $("#result-btn").css({"color": $(this).val()});
    });

    $("#deleteImage").click(function () {
      $("#imageInput").val("");
      $("#imageName").text("").parent().addClass("d-none");
      $("#deleteImage").addClass("d-none");
      $.ajax({
        type: "POST", // For jQuery < 1.9
        method: "POST",
        url: `{{ route("delete_image", $theme->id) }}`,
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
          action: "remove",
        },
        success: function (res) {
          $("#quiz-bg-img").remove();
          console.log("Image Uploaded (:");
        },
        error: function (res) {
          console.error("something went wrong :(");
        },
      });
    });

    // upload and preview image logic
    $("#imageInput").on("change", function () {
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
    $("#removeImage").click(function () {
      $("#imageInput").val("");
      $("#quiz-bg-img").css("background-image", $("#quiz-bg-img").data("img"));
      $("#imageName").text("");
      $("#imageName").parent().addClass("d-none");
      $("#deleteImage").removeClass("d-none");
    });

    // show live demo for opacity range value
    $('#opacityRange').on("input", function () {
      $("#" + $(this).data("reflect")).text($(this).val());
      $("#quiz-bg-img").css({
        "opacity": $(this).val() / 100,
      });
    });

    // sidebar pages active link
    let trig = true;
    $(".sidebar .page").each(function () {

      if ($(this).hasClass("active") && trig) {
        $("#" + $(this).data("page")).show();
        $("#submit_btn").attr("form", $(this).data("page"));
        trig = false;
      }else {
        $("#" + $(this).data("page")).hide();
      }
    });
    $(".sidebar .page").click(function () {
      $("#" + $(this).siblings().data("page")).hide();
      $(this).siblings().removeClass("active");
      $(this).addClass("active");
      $("#" + $(this).data("page")).show();
      $("#submit_btn").attr("form", $(this).data("page"));
    });

  </script>
@endsection
