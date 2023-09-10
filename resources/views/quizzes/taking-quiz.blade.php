<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="{{ $quiz->meta_description }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $quiz->meta_title ?? env('APP_NAME') }}</title>
  <!-- Include stylesheet -->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <style>
    .ql-toolbar.ql-snow {
      display: none
    }
  </style>

  {{-- fontAwesome 5.5 --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.5.0/css/all.min.css" integrity="sha512-QfDd74mlg8afgSqm3Vq2Q65e9b3xMhJB4GZ9OcHDVy1hZ6pqBJPWWnMsKDXM7NINoKqJANNGBuVRIpIJ5dogfA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  {{-- bootstrap 5.0.2 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  @vite(["resources/sass/quiz.scss"])

  @if($quiz->website != null && $quiz->website->has_integration("facebook"))
    <!-- Meta Pixel Code -->
    {!! preg_replace('/<!--(.|\s)*?-->/', '', $quiz->website->get_integration("facebook")->key) !!}
    <!-- End Meta Pixel Code -->
  @endif

  {{-- Quill Core --}}
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
</head>
<body>
  <script>
    fbq('track', 'ViewContent', {quiz: "{{ $quiz->name }}" });
  </script>

  <?php

    $has_integrations = $quiz->website->has_integration("facebook");

    $questionsClasses = [
      "text-question",
      "image-question",
      "form",
      "text",
      "media",
    ];

  ?>

  <div class="quiz-bg-color" style="background-color: {{$quiz->background_color}}"></div>

  @if($quiz->image != null)
    <div class="quiz-bg-img" style="background-image: url('{{ url("images/uploads/$quiz->image") }}');opacity: {{$quiz->image_opacity / 100}}"></div>
  @endif

  @if($quiz->show_logo == 1 && $quiz->website->logo_image != null)
    <div class="website-logo-container">
      <img class="website-logo" src="{{ url("images/uploads/" . $quiz->website->logo_image) }}" alt="logo">
    </div>
  @endif

  <div class="quiz-content">

    <div class="questions" id="questions-holder">

      @if($quiz->intro_title != null && $quiz->intro_btn != null)
        <div class="intro item active">
          <div class="content text-center">
            <h1
                style="font-family: {{$quiz->font_family}}; color: {{$quiz->main_text_color}};"
                class="title">{{ $quiz->intro_title }}</h1>
            <p
                style="font-family: {{$quiz->font_family}}; color: {{$quiz->main_text_color}};"
                class="lead desc">{{ $quiz->intro_description }}</p>

            @if($quiz->intro_image != null)
              @if(File::exists(public_path("images/uploads/$quiz->intro_image")))
                <img src="{{ url("images/uploads/$quiz->intro_image") }}" class="img-fluid question-img">
              @elseif(filter_var($quiz->intro_image, FILTER_VALIDATE_URL) == TRUE)
                <div class="embed-responsive embed-responsive-16by9">
                  <iframe class="d-block mx-auto my-2 embed-responsive-item" src="{{ $quiz->intro_image }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
              @endif
            @endif
            @if($quiz->policy_link != null && $quiz->is_shown_policy == 1)
              <div class="mt-3" id="acceptPolicy">
                <div>
                  <input type="checkbox" class="form-check-input" id="policyElement" style="scale:1.2">
                  <label for="policyElement"> {{ $quiz->policy_label }} <a href="{{ $quiz->policy_link }}">Read More.</a></label>
                </div>
              </div>
            @endif
            <button
                style="font-family: {{$quiz->font_family}};
                    color: {{$quiz->btn_text_color}};
                    background-color: {{ $quiz->btn_color }};"
                class="btn nex-btn"
                id="intro-btn"
                data-target="question-{{ $questions[0]->id }}">{{ $quiz->intro_btn }}</button>
          </div>
        </div>
      @endif

      @foreach($questions as $i => $question)
        <div
            class="question {{ $questionsClasses[$question->type - 1] }} item @if($quiz->intro_title == null || $quiz->intro_btn == null) active @endif"
            @if($quiz->website != null && $has_integrations == true)
              @if($question->has_integrations("facebook") != null)
                data-fb="question-{{ $question->get_integration("facebook") }}"
              @endif
            @endif
            id="question-{{ $question->id }}"
            data-id="{{ $question->id }}"
            data-type="{{ $question->type }}"
            data-order="{{ $question->order }}"
            style="order: {{ $question->order }}">
          <?php $letter = 'A'; ?>
          <div class="content">
            <h1
                class="title question-title"
                style="font-family: {{$quiz->font_family}}; color: {{$quiz->main_text_color}};"
                >{{ $question->title }}</h1>
            <p
                class="lead question-desc"
                style="font-family: {{$quiz->font_family}}; color: {{$quiz->main_text_color}};"
                >{{ $question->description }}</p>

            @if($question->image != null)
              <img src="{{ url("images/uploads/$question->image") }}" class="img-fluid question-img">
            @endif

            @if($question->video != null)
              <div class="embed-responsive embed-responsive-16by9">
                <iframe class="yt-aspect embed-responsive-item" src="{{ $question->video }}"></iframe>
              </div>
            @endif

            @if(in_array($question->type, [1, 2]))

              <?php $langsAnswers = $lang . "_answers"; ?>
              <div class="answers @if($question->type == 2) img-answers @endif my-4" id="answers-holder-{{$i}}" data-multi={{ $question->multi_select }}>

                @foreach ($question->$langsAnswers() as $x => $answer)

                  <div
                      @if($question->multi_select != 1)
                        @if($i >= count($questions) - 1) data-target="end"
                        @else data-target="question-{{ $questions[$i + 1]->id }}" @endif
                      @endif
                      @if($quiz->type == 1){{-- scoring --}}
                        data-score="{{ $answer->score ?? 0}}"
                      @endif
                      data-id="{{ $answer->id }}"
                      id="answer-{{ $answer->id }}"
                      class="answer @if ($question->type == 2) answer card border-0 @else border @endif"
                      style="background: {{$quiz->answer_bg_color}};border-color: {{$quiz->border_color}} !important;">
                  <div class="highlight" style="background: {{$quiz->highlight_color}};"></div>
                  @if ($question->type == 2)
                    <img class="img card-img-top" src="{{ url("images/uploads/$answer->image") }}" width="200" height="200">
                  @else
                    <div class="answer-letter">{{ $letter++ }}</div>
                  @endif
                  <p style="font-family: {{$quiz->font_family}}; color: {{$quiz->answer_text_color}};"
                    class="text m-0">{{ $answer->text }}</p>
                </div>

                @endforeach
              </div>
            @endif

            @if($question->type == 3)

              <form class="form" id="fields-holder-{{$i}}">
                @foreach ($question->fields as $x => $field)

                  <div class="input-box">

                    @if($field->type != 13)
                      <label class="form-label" for="form-{{$i}}-input-{{$x}}">{{ $field->label }} @if($field->is_required == 1) * @endif</label>
                    @endif

                    @if(in_array($field->type, [1, 2, 3, 4, 6, 9, 10, 11, 12]))
                      <input
                          {{ $field->is_required == 1 ? "required" : "" }}
                          class="view-input"
                          type="{{ $field->type_name() }}"
                          data-type="{{$field->type}}" data-id="{{ $field->id }}"
                          id="form-{{$i}}-input-{{$x}}"
                          placeholder="{{ $field->placeholder }}"
                          style="font-family: {{$quiz->font_family}};color: {{$quiz->main_text_color}};border-color: {{$quiz->border_color}};">
                    @elseif($field->type == 13)
                      <input
                          class="view-input"
                          id="form-{{$i}}-input-{{$x}}"
                          data-type="{{$field->type}}" data-id="{{ $field->id }}"
                          type="{{ $field->type_name() }}"
                          value="{{ $field->hidden_value }}">
                    @elseif($field->type == 5)
                      <textarea
                          {{ $field->is_required == 1 ? "required" : "" }}
                          class="view-input"
                          data-type="{{$field->type}}" data-id="{{ $field->id }}"
                          id="form-{{$i}}-input-{{$x}}"
                          placeholder="{{ $field->placeholder }}"
                          style="font-family: {{$quiz->font_family}};color: {{$quiz->main_text_color}};border-color: {{$quiz->border_color}};"
                          ></textarea>
                    @elseif($field->type == 7)
                      @foreach($field->options as $y => $option)
                        @if($field->is_multiple_chooseing == 1)
                          <div class="holder">
                            <label class="form-label" for="form-{{$i}}-input-{{$x}}-option-{{$y}}">{{ $option->value }}</label>
                            <input
                              {{ $field->is_required == 1 ? "required" : "" }}
                              name="field[{{$field->id}}][]"
                              class="input-option"
                              data-type="{{$field->type}}" data-id="{{ $field->id }}"
                              id="form-{{$i}}-input-{{$x}}-option-{{$y}}"
                              type="checkbox"
                              style="border-color: {{$quiz->border_color}}">
                          </div>
                        @else
                          <div class="holder">
                            <label class="form-label" for="form-{{$i}}-input-{{$x}}-option-{{$y}}">{{ $option->value }}</label>
                            <input
                              {{ $field->is_required == 1 ? "required" : "" }}
                              name="field[{{$field->id}}]"
                              class="input-option"
                              data-type="{{$field->type}}" data-id="{{ $field->id }}"
                              id="form-{{$i}}-input-{{$x}}-option-{{$y}}"
                              type="radio">
                            <span onclick="document.getElementById('form-{{$i}}-input-{{$x}}-option-{{$y}}').click()" class="checkmark" style="border-color: {{$quiz->border_color}}"></span>
                          </div>
                        @endif
                      @endforeach
                    @elseif($field->type == 8)
                      <select class="view-input" data-type="{{$field->type}}" data-id="{{ $field->id }}" id="form-{{$i}}-input-{{$x}}"
                        {{ $field->is_required == 1 ? "required" : "" }}
                        style="font-family: {{$quiz->font_family}};color: {{$quiz->main_text_color}};border-color: {{$quiz->border_color}};">
                          @foreach($field->options as $option)
                            <option value="{{ $option->id }}">{{ $option->value }}</option>
                          @endforeach
                      </select>
                    @endif

                  </div>

                @endforeach
              </form>
            @endif

            @if(!in_array($question->type, [1, 2]) || $question->multi_select == 1)

              @if($question->type == 3)
                <div class="btns-row">
                  <button
                    class="btn nex-btn mt-0"
                    style="font-family: {{$quiz->font_family}};
                          color: {{$quiz->btn_text_color}};
                          background-color: {{ $quiz->btn_color }};"
                    @if($i >= count($questions) - 1) data-target="end"
                    @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ $question->button_label ?? "Submit" }}</button>
                  @if($question->is_skippable != null && $question->is_skippable == 1)
                    <button
                      class="btn skip"
                      style="font-family: {{$quiz->font_family}};
                            color: {{$quiz->btn_color}};
                            background-color: transparent;"
                      @if($i >= count($questions) - 1) data-target="end"
                      @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ __("Skip") }}</button>
                  @endif
                  <div class="error-box d-inline-block" style="float: right"></div>
                </div>
              @else
                <button
                  class="btn nex-btn"
                  style="font-family: {{$quiz->font_family}};
                        color: {{$quiz->btn_text_color}};
                        background-color: {{ $quiz->btn_color }};"
                  @if($i >= count($questions) - 1) data-target="end"
                  @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ $question->button_label ?? "Submit" }}</button>
              @endif


            @endif

          </div>
        </div>
      @endforeach

      @foreach($results as $i => $result)

        @if($quiz->type == 1){{-- scoring --}}

          @if($result->type == 1)

            <div
                class="result item {{ $result[$result->type - 5 - 1] }} py-4 px-5 m-auto"
                id="result-{{ $result->id }}"
                data-min-score="{{ $result->min_score }}"
                data-max-score="{{ $result->max_score }}"
                data-type="{{ $result->type }}"
                data-id="{{ $result->id }}">
              <div class="content">

                @if($result->show_social == 1)
                  <ul class="result-social-panel results-shares" style="color: white;">
                    <h2 class="title" style="font-family: {{$quiz->font_family}}; color: {{$quiz->main_text_color}};">
                        Share on:
                    </h2>
                    <li style="background-color: #016fdf;">
                      <a href="http://www.facebook.com/sharer.php?u={{ route("show_quiz", $quiz->id) }}" class="facebook"><i aria-hidden="true" class="fa fa-facebook" title="Facebook"></i></a>
                    </li>
                    <li style="background-color: #58bdf2;">
                      <a href="http://twitter.com/share?url={{ route("show_quiz", $quiz->id) }}&text={{ env('APP_NAME') }}&hashtags={{ env('APP_NAME') }}" class="twitter"><i aria-hidden="true" class="fa fa-twitter" title="Twitter"></i></a>
                    </li>
                    <li style="background-color: #283e4a;">
                      <a href="http://www.linkedin.com/shareArticle?mini=true&url={{ route("show_quiz", $quiz->id) }}" class="linkedin"><i aria-hidden="true" class="fa fa-linkedin" title="Linkedin"></i></a>
                    </li>
                    <li style="background-color: #db4437;">
                      <a href="mailto:?Subject={{ env('APP_NAME') }}&Body=I%20saw%20this%20and%20thought%20of%20you!%20 {{ route("show_quiz", $quiz->id) }}" class="Email"><i aria-hidden="true" class="fa fa-envelope" title="Email"></i></a>
                    </li>
                  </ul>
                @endif

                @if($result->show_score == 1)
                  <div class="score-panel rounded" id="score-panel">
                    <span>{{ $result->score_message }}</span>:<span id="result-{{ $result->id }}-score">50</span>
                  </div>
                @endif

                <div style="background: white; padding: 40px" class="d-flex flex-column justify-content-center rounded">
                  <span class="title">{{ $result->title }}</span>
                  <span class="desc mb-2">
                    @php $descElements = json_decode($result->description); @endphp
                    @if(json_last_error() === JSON_ERROR_NONE && $descElements->ops != null && count($descElements->ops) > 0)
                      <div class="preview"></div>
                      <div class="editor" data-result='{!! $result->description !!}' style="display: none"></div>
                    @endif
                  </span>
                  @if($result->show_button == 1)
                    <a
                        class="btn d-inline-block m-auto"
                        style="font-family: {{$quiz->font_family}};background: {{$quiz->result_btn_color}};color: {{$quiz->result_btn_text_color}};"
                        type="button"
                        target="_blank"
                        href="{{ $result->result_link }}">{{ $result->button_label }}</a>
                  @endif
                </div>
              </div>
            </div>

          @elseif($result->type == 2)

            <div
                class="result item {{ $result[$result->type - 5 - 1] }} py-4 px-5 m-auto"
                id="result-{{ $result->id }}"
                data-min-score="{{ $result->min_score }}"
                data-max-score="{{ $result->max_score }}"
                data-type="{{ $result->type }}"
                data-url="{{ $result->result_link }}"
                data-utm="{{ $result->send_UTM }}"
                data-send-data="{{ $result->send_data }}"
                data-id="{{ $result->id }}">
            </div>

          @elseif($result->type == 3)

            <div
                class="result item {{ $result[$result->type - 5 - 1] }} py-4 px-5 m-auto"
                id="result-{{ $result->id }}"
                data-min-score="{{ $result->min_score }}"
                data-max-score="{{ $result->max_score }}"
                data-type="{{ $result->type }}"
                data-id="{{ $result->id }}">
              <div class="content">

                @if($result->show_score == 1)
                  <div class="score-panel rounded" id="score-panel">
                    <span>{{ $result->score_message }}</span>:<span id="result-{{ $result->id }}-score">50</span>
                  </div>
                @endif

                <div style="background: white; padding: 40px" class="d-flex flex-column justify-content-center rounded">
                  <span class="title">{{ $result->title }}</span>
                  <span class="desc mb-2">
                    @php $descElements = json_decode($result->description); @endphp
                    @if(json_last_error() === JSON_ERROR_NONE && $descElements->ops != null && count($descElements->ops) > 0)

                      <div class="preview"></div>
                      <div class="editor" data-result='{!! $result->description !!}' style="display: none"></div>
                      <script>
                        var editors = document.querySelectorAll(".editor");
                        editors.forEach(editor => {
                          let preview = document.querySelector(".preview");
                          var quill = new Quill(editor);
                          quill.setContents(JSON.parse(editor.dataset.result));
                          preview.innerHTML = quill.root.innerHTML;
                        });
                      </script>

                    @endif
                  </span>
                  <div id="calendly-box">
                    <div class="calendly-inline-widget" style="position: relative;min-width:320px;" data-processed="true">
                      <div class="calendly-spinner">
                        <div class="calendly-bounce1"></div>
                        <div class="calendly-bounce2"></div>
                        <div class="calendly-bounce3"></div>
                      </div>
                      {{--

                      "https://api.calendly.com/users/me"

                      Authorization: `Bearer $quiz->website->get_integration("calendly")->key`,

                      ${link}/${val}?hide_gdpr_banner=1&background_color=${bgColor}&text_color=${color}&primary_color=${primaryColor} --}}
                      <iframe style="width: 100%;min-height: 960px;" src="{{ $result->result_link }}"></iframe>

                    </div>
                    <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async=""></script>
                  </div>
                </div>

              </div>
            </div>

          @endif

        @else{{-- outcome --}}

          @if($result->type == 1)

            <div
                class="result item {{ $result[$result->type - 5 - 1] }} py-4 px-5 m-auto"
                id="result-{{ $result->id }}"
                data-type="{{ $result->type }}"
                data-id="{{ $result->id }}">
              <div class="content">

                @if($result->show_social == 1)
                  <ul class="result-social-panel results-shares" style="color: white;">
                    <h2 class="title" style="font-family: {{$quiz->font_family}}; color: {{$quiz->main_text_color}};">
                        Share on:
                    </h2>
                    <li style="background-color: #016fdf;">
                      <a href="http://www.facebook.com/sharer.php?u={{ route("show_quiz", $quiz->id) }}" class="facebook"><i aria-hidden="true" class="fa fa-facebook" title="Facebook"></i></a>
                    </li>
                    <li style="background-color: #58bdf2;">
                      <a href="http://twitter.com/share?url={{ route("show_quiz", $quiz->id) }}&text={{ env('APP_NAME') }}&hashtags={{ env('APP_NAME') }}" class="twitter"><i aria-hidden="true" class="fa fa-twitter" title="Twitter"></i></a>
                    </li>
                    <li style="background-color: #283e4a;">
                      <a href="http://www.linkedin.com/shareArticle?mini=true&url={{ route("show_quiz", $quiz->id) }}" class="linkedin"><i aria-hidden="true" class="fa fa-linkedin" title="Linkedin"></i></a>
                    </li>
                    <li style="background-color: #db4437;">
                      <a href="mailto:?Subject={{ env('APP_NAME') }}&Body=I%20saw%20this%20and%20thought%20of%20you!%20 {{ route("show_quiz", $quiz->id) }}" class="Email"><i aria-hidden="true" class="fa fa-envelope" title="Email"></i></a>
                    </li>
                  </ul>
                @endif

                <div style="background: white; padding: 40px" class="d-flex flex-column justify-content-center rounded">
                  <span class="title">{{ $result->title }}</span>
                  <span class="desc mb-2">
                    @php $descElements = json_decode($result->description); @endphp
                    @if(json_last_error() === JSON_ERROR_NONE && $descElements->ops != null && count($descElements->ops) > 0)
                      <div class="preview"></div>
                      <div class="editor" data-result='{!! $result->description !!}' style="display: none"></div>
                    @endif
                  </span>
                  @if( $result->show_button == 1 )
                    <a
                        class="btn d-inline-block m-auto"
                        style="font-family: {{ $quiz->font_family }};background: {{ $quiz->result_btn_color }};color: {{ $quiz->result_btn_text_color }};"
                        type="button"
                        target="_blank"
                        href="{{ $result->result_link }}">{{ $result->button_label }}</a>
                  @endif
                </div>
              </div>
            </div>

          @elseif($result->type == 2)

            <div
                class="result item {{ $result[$result->type - 5 - 1] }} py-4 px-5 m-auto"
                id="result-{{ $result->id }}"
                data-type="{{ $result->type }}"
                data-url="{{ $result->result_link }}"
                data-utm="{{ $result->send_UTM }}"
                data-send-data="{{ $result->send_data }}"
                data-id="{{ $result->id }}">
            </div>

          @elseif($result->type == 3)

            <div
                class="result item {{ $result[$result->type - 5 - 1] }} py-4 px-5 m-auto"
                id="result-{{ $result->id }}"
                data-type="{{ $result->type }}"
                data-id="{{ $result->id }}">
                <div class="content">

                  @if($result->show_score == 1)
                    <div class="score-panel rounded" id="score-panel">
                      <span>{{ $result->score_message }}</span>:<span id="result-{{ $result->id }}-score">50</span>
                    </div>
                  @endif

                  <div style="background: white; padding: 40px" class="d-flex flex-column justify-content-center rounded">
                    <span class="title">{{ $result->title }}</span>
                    <span class="desc mb-2">{{ $result->description }}</span>
                    <div id="calendly-box">
                      <div class="calendly-inline-widget" style="position: relative;min-width:320px;" data-processed="true">
                        <div class="calendly-spinner">
                          <div class="calendly-bounce1"></div>
                          <div class="calendly-bounce2"></div>
                          <div class="calendly-bounce3"></div>
                        </div>
                        {{--

                        "https://api.calendly.com/users/me"

                        Authorization: `Bearer $quiz->website->get_integration("calendly")->key`,

                        ${link}/${val}?hide_gdpr_banner=1&background_color=${bgColor}&text_color=${color}&primary_color=${primaryColor} --}}
                        <iframe style="width: 100%;min-height: 960px;" src="{{ $result->result_link }}"></iframe>

                      </div>
                      <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async=""></script>
                    </div>
                  </div>

                </div>
            </div>

          @endif

        @endif

      @endforeach

      <div class="question item" id="no-result" data-type="1">
        <div class="content">
          <div style="background: white; padding: 40px" class="d-flex flex-column justify-content-center rounded">
            <span class="title">No Result ):</span>
            <span class="desc mb-2">these answers has no result</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  @if($quiz->website->show_watermark)
    <div class="watermark website-logo-container">
      <img class="website-logo" src="{{ url("images/logo.png") }}" alt="logo">
    </div>
  @endif

  <script>
    const QuizType = {{ $quiz->type }};
    const FormRoute = "{{ route('form_entry', $quiz->id) }}";
    const AnswersRoute = "{{ route('answering', $quiz->id) }}";
    const addViewRoute = "{{ url('ajax/add-view/') }}";

    var editors = document.querySelectorAll(".editor");
    editors.forEach(editor => {
      let preview = editor.parentElement.querySelector(".preview");
      var quill = new Quill(editor);
      quill.setContents(JSON.parse(editor.dataset.result));
      preview.innerHTML = quill.root.innerHTML;
    });

  </script>

  {{-- FontAwesome 4 --}}
  <script src="https://use.fontawesome.com/7eb399af8f.js"></script>

  {{-- Jquery 3 --}}
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

  {{-- bootstrap bandle --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  @vite(['resources/js/quiz.js'])

</body>
</html>

