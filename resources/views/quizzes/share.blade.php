@extends("layout.app-quiz-build")

@section('styles')
    <style>
      .embed-text {
        min-height: 100px !important;
        max-height: 160px !important;
        resize: vertical;
      }
    </style>
@endsection

@section("content")

<div class="quiz-content sharing-page">
  <div class="container px-5 py-4">
    <div class="mb-3">
      <div class="general-width">
        <div class="d-flex justify-content-between mb-2">
          <label class="form-label" for="url-input">Content URL:</label>
          <div data-target="quiz-url" class="copy-btn badge bg-primary py-2">copy</div>
        </div>
        <input type="url" id="quiz-url" readonly=true class="form-control" value="{{ route("show_quiz", $quiz->id) }}">
        <div class="mt-2 d-flex gap-1">
          <a class="btn btn-primary border-0 d-flex align-items-center justify-content-center" style="width: 80px;height: 50px;background-color: #3b5998;" href="http://www.facebook.com/sharer.php?u={{ route("show_quiz", $quiz->id) }}" role="button">
            <i class="fa fa-lg fa-facebook-f"></i>
          </a>
          <a class="btn btn-primary border-0 d-flex align-items-center justify-content-center" style="width: 80px;height: 50px;background-color: #55acee;" href="http://twitter.com/share?url={{ route("show_quiz", $quiz->id) }}&text={{ env('APP_NAME') }}&hashtags={{ env('APP_NAME') }}" role="button">
            <i class="fa fa-lg fa-twitter"></i>
          </a>
          <a class="btn btn-primary border-0 d-flex align-items-center justify-content-center" style="width: 80px;height: 50px;background-color: #0082ca;" href="http://www.linkedin.com/shareArticle?mini=true&url={{ route("show_quiz", $quiz->id) }}" role="button">
            <i class="fa fa-lg fa-linkedin"></i>
          </a>
          <a class="btn btn-primary border-0 d-flex align-items-center justify-content-center" style="width: 80px;height: 50px;background-color: #ac2bac;" href="mailto:?Subject={{ env('APP_NAME') }}&Body=I%20saw%20this%20and%20thought%20of%20you!%20 {{ route("show_quiz", $quiz->id) }}" role="button">
            <i class="fa fa-lg fa-instagram"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="container px-5 py-4">
    <div class="mb-3">
      <div class="general-width">
        <label class="form-label">Embed:</label>
        <p class="lead">Select the content embed size:</p>
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
          <button type="button" data-page="page-1" class="page-btn btn btn-outline-primary active">Custom</button>
          <button type="button" data-page="page-2" class="page-btn btn btn-outline-primary">Responsive</button>
          <button type="button" data-page="page-3" class="page-btn btn btn-outline-primary">Full Page</button>
        </div>
        <div id="page-1" class="page page-1 active my-2">
          <p class="m-0">Width:</p>
          <input id="val-1" type="number" class="mx-1 form-control" value="100">
          <select class="form-control" id="unit-1" style="width: fit-content">
            <option value="%">%</option>
            <option value="px">px</option>
          </select>
          <p style="margin: 0;margin-left: 20px">Height:</p>
          <input id="val-2" type="number" class="mx-1 form-control" value="700">
          <select class="form-control" id="unit-2" style="width: fit-content">
            <option value="px">px</option>
            <option value="%">%</option>
          </select>
        </div>
        <div id="page-2" class="page text-center page-2 my-2">
          This code will make it where your embedded content is fully responsive across all devices.
        </div>
        <div id="page-3" class="page text-center page-3 my-2">
          This code will make it where your embedded content takes up the entire page.
        </div>
        <div class="d-flex justify-content-between mb-2">
          <label class="form-label" for="url-input">Inline Embed Code:</label>
          <div data-target="embed-text" class="copy-btn badge bg-primary py-2">copy</div>
        </div>
        <textarea readonly=true class="form-control embed-text" id="embed-text"><div id='inline-embed-iframe' width="100px" height="700px" style='border-radius:5px; overflow:hidden;'><iframe src='{{ route("show_quiz", $quiz->id) }}' frameborder='0' style='float:left;'></iframe></div></textarea>
      </div>
    </div>
  </div>
  <hr>
  <div class="container px-5 py-4">
    <div class="mb-3">
      <div class="general-width">
        <h5>How to Promote Your Content</h5>
        <p class="lead">Discover the best ways to promote your content.</p>
        <iframe allowfullscreen="" frameborder="0" height="218" width="430" src="https://www.youtube.com/embed/7kAeADGQ1Pg"></iframe>
      </div>
    </div>
  </div>
</div>

@endsection

@section('options')


<div class="sidebar">

  <div class="container">

    <ul class="nav config-options flex-column">
      <li class="nav-item">
        <a class="nav-link active" data-page="general">
          Share URL & Embed
          <p class="lead mb-0">Share URL or grab embed code</p>
        </a>
      </li>
    </ul>

  </div>

</div>

@endsection


@section('scripts')
  <script>

    $(".page-btn").each(function () {
      $(this).click(function () {

        $(this).addClass("active").siblings().removeClass("active");
        $(`#${$(this).data('page')}`).addClass("active").siblings().removeClass("active");

        if ($(this).data('page') == "page-1") {

          let textClass = "";
          let textWidth = "width:100%;";
          let textWidthAttr = `width="${$("#val-1").val()}${$("#unit-1").val()}"`;
          let textHeightAttr = `height="${$("#val-2").val()}${$("#unit-2").val()}"`;

          $("#embed-text").text(
            `<div id='inline-embed-iframe' ${textWidthAttr} ${textHeightAttr} class='${textClass}' style='border-radius:5px; overflow:hidden;'><iframe src='{{ route("show_quiz", $quiz->id) }}' frameborder='0' style='float:left; ${textWidth}'></iframe></div>`
          );

        }else if ($(this).data('page') == "page-2") {

          let textClass = "inline-embed-responsive";
          let textWidth = "width:100%;";
          let textWidthAttr = "";
          let textHeightAttr = "";

          $("#embed-text").text(
            `<div id='inline-embed-iframe' ${textWidthAttr} ${textHeightAttr} class='${textClass}' style='border-radius:5px; overflow:hidden;'><iframe src='{{ route("show_quiz", $quiz->id) }}' frameborder='0' style='float:left; ${textWidth}'></iframe></div>`
          );

        }else if ($(this).data('page') == "page-3") {

          let textClass = "inline-embed-full";
          let textWidth = "width:100%;";
          let textWidthAttr = "";
          let textHeightAttr = "";

          $("#embed-text").text(
            `<div id='inline-embed-iframe' ${textWidthAttr} ${textHeightAttr} class='${textClass}' style='border-radius:5px; overflow:hidden;'><iframe src='{{ route("show_quiz", $quiz->id) }}' frameborder='0' style='float:left; ${textWidth}'></iframe></div>`
          );

        }

      });
    });

    $("#val-1, #val-2, #unit-1, #unit-2").on("input", function () {

        let textClass = "";
        let textWidth = "width:100%;";
        let textWidthAttr = `width="${$("#val-1").val()}${$("#unit-1").val()}"`;
        let textHeightAttr = `height="${$("#val-2").val()}${$("#unit-2").val()}"`;

        $("#embed-text").text(
          `<div id='inline-embed-iframe' ${textWidthAttr} ${textHeightAttr} class='${textClass}' style='border-radius:5px; overflow:hidden;'><iframe src='{{ route("show_quiz", $quiz->id) }}' frameborder='0' style='float:left; ${textWidth}'></iframe></div>`
        );
    });

    $(".copy-btn").each(function () {
      $(this).click(function () {
        $(`#${$(this).data("target")}`).select();
        document.execCommand("copy");
        $(this).text("copied");
        setTimeout(() => {
          $(this).text("copy");
        }, 1500);
      });
    });

  </script>
@endsection
