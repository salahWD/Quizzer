@extends("layout.app-quiz-build")

@section('styles')
    <style>
      .embed-text {
        min-height: 100px !important;
        max-height: 160px !important;
        resize: vertical;
      }
      pre {
        background: #333;
        color: #f3f3f3;
        border-radius: 5px;
      }
    </style>
@endsection

@section("content")

<div class="quiz-content sharing-page" id="share-page">
  <div class="container px-5 py-4">
    <div class="mb-3">
      <div class="general-width">
        <div class="d-flex justify-content-between mb-2">
          <label class="form-label" for="url-input">{{ __('Content URL') }}:</label>
          <div data-target="quiz-url" class="copy-btn badge bg-primary py-2">{{ __('copy') }}</div>
        </div>
        <input type="url" id="quiz-url" readonly=true class="form-control" value="{{ $quiz->website->custom_domain != null ? $quiz->website->custom_domain."/en/tq/".$quiz->id : route("show_quiz", $quiz->id) }}">
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
        <label class="form-label">{{ __('Embed') }}:</label>
        <p class="lead">{{ __('Select the content embed size') }}:</p>
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
          <button type="button" data-page="page-1" class="page-btn btn btn-outline-primary active">{{ __('Custom') }}</button>
          <button type="button" data-page="page-2" class="page-btn btn btn-outline-primary">{{ __('Responsive') }}</button>
          <button type="button" data-page="page-3" class="page-btn btn btn-outline-primary">{{ __('Full Page') }}</button>
        </div>
        <div id="page-1" class="page page-1 active my-2">
          <p class="m-0">{{ __('Width') }}:</p>
          <input id="val-1" type="number" class="mx-1 form-control" value="100">
          <select class="form-control" id="unit-1" style="width: fit-content">
            <option value="%">%</option>
            <option value="px">px</option>
          </select>
          <p style="margin: 0;margin-left: 20px">{{ __('Height') }}:</p>
          <input id="val-2" type="number" class="mx-1 form-control" value="700">
          <select class="form-control" id="unit-2" style="width: fit-content">
            <option value="px">px</option>
            <option value="%">%</option>
          </select>
        </div>
        <div id="page-2" class="page text-center page-2 my-2">
          {{ __('This code will make it where your embedded content is fully responsive across all devices') }}.
        </div>
        <div id="page-3" class="page text-center page-3 my-2">
          {{ __('This code will make it where your embedded content takes up the entire page') }}.
        </div>
        <div class="d-flex justify-content-between mb-2">
          <label class="form-label" for="url-input">{{ __('Inline Embed Code') }}:</label>
          <div data-target="embed-text" class="copy-btn badge bg-primary py-2">{{ __('copy') }}</div>
        </div>
        <textarea readonly=true class="form-control embed-text" id="embed-text"><div id='inline-embed-iframe' width="100px" height="700px" style='border-radius:5px; overflow:hidden;'><iframe src='{{ route("show_quiz", $quiz->id) }}' frameborder='0' style='float:left;'></iframe></div></textarea>
      </div>
    </div>
  </div>
  <hr>
  <div class="container px-5 py-4">
    <div class="mb-3">
      <div class="general-width">
        <h5>{{ __('How to Promote Your Content') }}</h5>
        <p class="lead">{{ __('Discover the best ways to promote your content') }}.</p>
        <iframe allowfullscreen="" frameborder="0" height="218" width="430" src="https://www.youtube.com/embed/VH92UJgM1WU?si=s1toVSnUhp4TavNM"></iframe>
      </div>
    </div>
  </div>
</div>

<div class="quiz-content sharing-page" id="popup-page">
  <div class="container px-5 py-4">
    <div class="mb-3">

      <div style="max-width: 650px">
        <h5>{{ __('Popup Notification') }}</h5>
        <p class="lead">{{ __('Note: you have to include jQuery in your site for it to work') }}.</p>

        <form action="{{ route("share_popup_images", $quiz->id) }}" method="POST" enctype="multipart/form-data">
          <div class="d-flex gap-4">
            @csrf
            <div class="box-item">
              <p class="mb-1" style="min-width: 50px">{{ __('Image') }}:</p>
              <input id="popup-img-input" type="file" class="d-none" name="image">
              <img
                  style="width: 150px;object-fit:cover;display:block;background: #f8f9fa;"
                  src="{{ $quiz->popup_img != null ? url("images/uploads/" . $quiz->popup_img) : "http://via.placeholder.com/400" }}"
                  class="img-fluid" alt="" id="img-img-preview">
              <button type="button" id="img-img-btn" class="btn btn-primary d-block m-auto" style="transform:translatey(-100%)" onclick="$('#popup-img-input').click()">
                <i class="fa fa-camera"></i>
              </button>
            </div>
            <div class="box-item">
              <p class="mb-1" style="min-width: 50px">{{ __('Thumbnail') }}:</p>
              <input id="popup-thumb-input" type="file" class="d-none" name="thumb">
              <img
                  style="width: 150px;object-fit:cover;display:block;background: #f8f9fa;"
                  src="{{ $quiz->popup_thumb != null ? url("images/uploads/" . $quiz->popup_thumb) : "http://via.placeholder.com/100" }}"
                  class="img-fluid" alt="" id="thumb-img-preview">
              <button type="button" id="thumb-img-btn" class="btn btn-primary d-block m-auto" style="transform:translatey(-100%)" onclick="$('#popup-thumb-input').click()">
                <i class="fa fa-camera"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary mb-4">{{ __('Save') }} <i class="fa fa-save"></i></button>
        </form>
        <div class="my-2">
          <p class="m-0" style="min-width: 50px">{{ __('Title') }}:</p>
          <input id="popup-title-input" type="text" class="mx-1 form-control" value="{{ $quiz->name }}">
        </div>
        <div class="my-2">
          <p class="m-0" style="min-width: 50px">{{ __('CTA') }}:</p>
          <input id="popup-cta-input" type="text" class="mx-1 form-control" value="Let's Go">
        </div>
      </div>

      <button type="button" id="popupcode-copy" class="btn btn-primary">{{ __('Copy Code Snippet') }}</button>

      <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="popupcode-notification" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              {{ __("Snippet Copied Successfully") }}.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>


      <div class="mt-4 preview" style="position: relative;min-height: 250px;max-width: 650px">

        <style>
          .floating-chat {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: absolute;
            bottom: 30px;
            right: 30px;
            width: 80px;
            height: 80px;
            transform: translateY(70px);
            transition: all 250ms ease-out;
            border-radius: 50%;
            opacity: 0;
            background: -moz-linear-gradient(-45deg, #183850 0, #183850 25%, #192C46 50%, #22254C 75%, #22254C 100%);
            background: -webkit-linear-gradient(-45deg, #183850 0, #183850 25%, #192C46 50%, #22254C 75%, #22254C 100%);
            background-repeat: no-repeat;
            background-attachment: fixed;
          }
          .floating-chat.enter:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
            opacity: 1;
          }
          .floating-chat.enter {
            transform: translateY(0);
            opacity: 0.6;
            box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.12), 0px 1px 2px rgba(0, 0, 0, 0.14);
          }
          .floating-chat .image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            transition: 0.4s ease-out;
            border-radius: 50%;
            overflow: hidden;
            display: none;
          }
          .floating-chat.expand {
            width: 250px;
            max-height: 500px;
            min-height: 80px;
            height: 220px;
            border-radius: 5px;
            cursor: auto;
            opacity: 1;
          }
          .floating-chat :focus {
            outline: 0;
            box-shadow: 0 0 3pt 2pt rgba(14, 200, 121, 0.3);
          }
          .floating-chat .chat {
            display: flex;
            flex-direction: column;
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            border-radius: 50%;
            transition: all 250ms ease-out;
            margin: auto;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
          }
          .floating-chat .chat.enter {
            opacity: 1;
            border-radius: 0;
            margin: 0;
            width: auto;
            height: auto;
          }
          .floating-chat .chat .header {
            flex-shrink: 0;
            padding-bottom: 10px;
            display: flex;
            background: transparent;
            position: relative;
          }
          .floating-chat .chat .header button {
            border: 0;
            text-transform: uppercase;
            cursor: pointer;
            flex-shrink: 0;
            position: absolute;
            right: -20px;
            top: -20px;
            background: white;
            color: black;
            border-radius: 50%;
            aspect-ratio: 1;
          }
          .floating-chat .chat .header .image {
            width: 100%;
            max-height: 90px;
            object-fit: cover;
            object-position: center center;
            margin: 0;
            display: block;
            border-radius: 0;
            padding: 0;
          }
          .floating-chat .chat .body {
            padding: 0 12px 12px;
            font-size: 16px;
            line-height: 21px;
            height: 100%;
            display: flex;
            flex-direction: column;
          }
          .floating-chat .chat .body .btn {
            margin: 0;
            display: block;
            background: white;
            padding: 8px;
            text-align: center;
            margin-top: auto;
            width: 100%;
            text-decoration: none;
            color: black;
          }
        </style>
        <div class="floating-chat enter">
          <img id="thumb-prev" src="{{ $quiz->popup_thumb != null ? url("images/uploads/" . $quiz->popup_thumb) : "http://via.placeholder.com/100" }}" class="image">
          <div class="chat">
              <div class="header">
                <img id="img-prev" src="{{ $quiz->popup_img != null ? url("images/uploads/" . $quiz->popup_img) : "http://via.placeholder.com/400" }}" class="image">
                <button>
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
              </div>
              <div class="body">
                <div id="title-prev">{{ $quiz->name }}</div>
                <a href="#" class="btn" id="cta-prev">Let's Go</a>
              </div>
          </div>
        </div>
      </div>


    </div>
  </div>
</div>

@endsection

@section('options')

  <div class="sidebar">

    <div class="container">

      <ul class="nav config-options sharing-pages flex-column">
        <li class="nav-item">
          <a class="nav-link active" data-page="share-page">
            {{ __('Share URL & Embed') }}
            <p class="lead mb-0">{{ __('Share URL or grab embed code') }}</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" data-page="popup-page">
            {{ __('Popup Code') }}
            <p class="lead mb-0">{{ __('Popup Sharing') }}</p>
          </a>
        </li>
      </ul>

    </div>

  </div>

@endsection


@section('scripts')
  <script>
    function activePage(id) {
      $(".sharing-pages").find(".nav-link[data-page=\"" + id + "\"]").addClass("active").parent().siblings(".nav-item").find(".nav-link").removeClass("active");
      $('.sharing-page').hide();
      $("#" + id).fadeIn();
    }
    $(".sharing-pages").find(".nav-link[data-page]").each(function () {
      $(this).click(function () {
        activePage($(this).data("page"));
      });
    });
    // activePage("share-page");
    const urlParams = new URLSearchParams(window.location.search);
    let queryStringPage = urlParams.get('page');
    if (queryStringPage != null) {
      if ($("#" + queryStringPage + ".sharing-page").length > 0) {
        activePage(queryStringPage);
      }else {
        activePage("share-page");
      }
    }else {
      activePage("share-page");
    }

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
        $(this).text("{{ __('copied') }}");
        setTimeout(() => {
          $(this).text("{{ __('copy') }}");
        }, 1500);
      });
    });

    $("#popupcode-copy").click(function () {
      let title = $("#popup-title-input").val();
      let cta = $("#popup-cta-input").val();
      // let thumb = $("#popup-img-input").val();
      let thumb = '{{ $quiz->popup_thumb != null ? url("images/uploads/" . $quiz->popup_thumb) : "http://via.placeholder.com/100" }}';
      // let img = $("#popup-img-input").val();
      let img = '{{ $quiz->popup_img != null ? url("images/uploads/" . $quiz->popup_img) : "http://via.placeholder.com/400" }}';
      let text =`
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
          @import 'https://fonts.googleapis.com/css?family=Noto+Sans';
          .floating-chat {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 80px;
            height: 80px;
            transform: translateY(70px);
            transition: all 250ms ease-out;
            border-radius: 50%;
            opacity: 0;
            background: -moz-linear-gradient(-45deg, #183850 0, #183850 25%, #192C46 50%, #22254C 75%, #22254C 100%);
            background: -webkit-linear-gradient(-45deg, #183850 0, #183850 25%, #192C46 50%, #22254C 75%, #22254C 100%);
            background-repeat: no-repeat;
            background-attachment: fixed;
          }
          @media (max-width: 768px) {
            .floating-chat {
              width: 15%;
            }
          }
          .floating-chat.enter:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
            opacity: 1;
          }
          .floating-chat.enter {
            transform: translateY(0);
            opacity: 0.6;
            box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.12), 0px 1px 2px rgba(0, 0, 0, 0.14);
          }
          .floating-chat .image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            transition: 0.4s ease-out;
            border-radius: 50%;
            overflow: hidden;
            display: none;
          }
          .floating-chat.expand {
            width: 250px;
            max-height: 500px;
            min-height: 80px;
            height: 220px;
            border-radius: 5px;
            cursor: auto;
            opacity: 1;
          }
          .floating-chat :focus {
            outline: 0;
            box-shadow: 0 0 3pt 2pt rgba(14, 200, 121, 0.3);
          }
          .floating-chat .chat {
            display: flex;
            flex-direction: column;
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            border-radius: 50%;
            transition: all 250ms ease-out;
            margin: auto;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
          }
          .floating-chat .chat.enter {
            opacity: 1;
            border-radius: 0;
            margin: 0;
            width: auto;
            height: auto;
          }
          .floating-chat .chat .header {
            flex-shrink: 0;
            padding-bottom: 10px;
            display: flex;
            background: transparent;
            position: relative;
          }
          .floating-chat .chat .header button {
            border: 0;
            text-transform: uppercase;
            cursor: pointer;
            flex-shrink: 0;
            position: absolute;
            right: -20px;
            top: -20px;
            background: white;
            color: black;
            border-radius: 50%;
            aspect-ratio: 1;
          }
          .floating-chat .chat .header .image {
            width: 100%;
            max-height: 90px;
            object-fit: cover;
            object-position: center center;
            margin: 0;
            display: block;
            border-radius: 0;
            padding: 0;
          }
          .floating-chat .chat .body {
            padding: 0 12px 12px;
            font-size: 16px;
            line-height: 21px;
            height: 100%;
            display: flex;
            flex-direction: column;
          }
          .floating-chat .chat .body .btn {
            margin: 0;
            display: block;
            background: white;
            padding: 8px;
            text-align: center;
            margin-top: auto;
            width: 100%;
            text-decoration: none;
            color: black;
          }
        </style>
        <div class="floating-chat enter">
          <img src="${thumb}" class="image">
          <div class="chat">
              <div class="header">
                <img src="${img}" class="image">
                <button>
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
              </div>
              <div class="body">
                <div>${title}</div>
                <a href="{{ route("show_quiz", $quiz->id) }}" class="btn">${cta}</a>
              </div>
          </div>
        </div>
        <` + `script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"><` + `/script>
        <` + `script` + `>
          var element=$(".floating-chat");function openElement(){element.find(">.image").hide(),element.addClass("expand"),element.find(".chat").addClass("enter"),element.off("click",openElement),element.find(".header button").click(closeElement)}function closeElement(){element.find(".chat").removeClass("enter").hide(),element.removeClass("expand"),element.find(".header button").off("click",closeElement),setTimeout(function(){element.find(">.image").show(),element.find(".chat").removeClass("enter").show(),element.click(openElement)},250)}setTimeout(function(){openElement()},1e3),element.click(openElement);
        </` + `script>`;
      var $temp = $("<textarea>");
      $("body").append($temp);
      $temp.val(text).select();
      document.execCommand("copy");
      $temp.remove();
    });

    const toastTrigger = document.getElementById('popupcode-copy')
    const toastLiveExample = document.getElementById('popupcode-notification')

    if (toastTrigger) {
      const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
      toastTrigger.addEventListener('click', () => {
        toastBootstrap.show()
        window.setTimeout(function () {
          toastBootstrap.hide()
        }, 1000)
      })
    }

    $(window).ready(function () {

      let img   = $("#popup-img-input");
      let thumb = $("#popup-thumb-input");
      let title = $("#popup-title-input");
      let cta   = $("#popup-cta-input");

      img.on("change", function(evt) {
        const [file] = img[0].files
        if (file) {
          $("#img-prev")[0].src = URL.createObjectURL(file);
          $("#img-img-preview")[0].src = URL.createObjectURL(file);
        }
      });
      thumb.on("change", function(evt) {
        const [file] = thumb[0].files
        if (file) {
          $("#thumb-prev")[0].src = URL.createObjectURL(file);
          $("#thumb-img-preview")[0].src = URL.createObjectURL(file);
        }
      });

      title.on("input", function () {
        $("#title-prev").html(title.val());
      });
      cta.on("input", function () {
        $("#cta-prev").html(cta.val());
      });

      var element = $('.floating-chat');

      setTimeout(function() {
        openElement();
      }, 1000);

      element.click(openElement);

      function openElement() {
        element.find('>.image').hide()
        element.addClass('expand');
        element.find('.chat').addClass('enter');
        element.off('click', openElement);
        element.find('.header button').click(closeElement);
      }

      function closeElement() {
        element.find('.chat').removeClass('enter').hide();
        element.removeClass('expand');
        element.find('.header button').off('click', closeElement);
        setTimeout(function() {
          element.find('>.image').show()
          element.find('.chat').removeClass('enter').show()
          element.click(openElement);
        }, 250);
      }

    });

  </script>
@endsection
