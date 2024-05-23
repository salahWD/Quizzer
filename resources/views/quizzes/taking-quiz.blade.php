@php
  $questionsClasses = [
    "text-question",
    "image-question",
    "form",
    "text",
    "media",
  ];
@endphp
<!DOCTYPE html>
  <!-- This Website Created By Salah -->
  <!-- Github: https://github.com/SalahWD -->
  <!-- Instagram: https://instagram.com/salah_backhach -->
  <!-- Work Email: salahbakhash@gmail.com -->
  <!-- whatsapp: (may not work in the future) +905527188570 -->
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="{{ $quiz->meta_description }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $quiz->meta_title ?? __(env('APP_NAME')) }}</title>

  <!-- Include stylesheet -->
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

  <!-- Selected Font -->
  @if ($lang == "ar" && !empty($quiz->font_family_ar))
    <link rel="stylesheet" href="{{ $fonts[$quiz->font_family_ar]["url"] }}">
  @else
    <link rel="stylesheet" href="{{ $fonts[$quiz->font_family]["url"] }}">
  @endif

  <style>
    * {
      direction: {{ app()->getLocale() == "ar" ? "rtl" : "ltr"}};
      box-sizing: border-box;
      font-family: "{{ $fonts[$quiz->font_family]["name"] }}";
      @if (app()->getLocale() == "ar" && !empty($quiz->font_family_ar))
      font-family: "{{ $fonts[$quiz->font_family_ar]["name"] }}";
      @else
      font-family: "{{ $fonts[$quiz->font_family]["name"] }}";
      @endif
    }
    @php list($r, $g, $b) = sscanf($quiz->main_text_color, "#%02x%02x%02x"); @endphp
    ::placeholder {
      color: rgba({{ $r }},{{ $g }},{{ $b }}, 0.5);
    }
    .ql-toolbar.ql-snow {
      display: none
    }
  </style>

  {{-- fontAwesome 5.5 --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.5.0/css/all.min.css" integrity="sha512-QfDd74mlg8afgSqm3Vq2Q65e9b3xMhJB4GZ9OcHDVy1hZ6pqBJPWWnMsKDXM7NINoKqJANNGBuVRIpIJ5dogfA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  {{-- bootstrap 5.0.2 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  @vite(["resources/sass/quiz.scss"])

  @php $ga_has_integrations = false; @endphp
  @if($quiz->website != null && $quiz->website->has_integration("googleAds"))

    @php $tags = $quiz->google_tags; @endphp
    @if ($tags != null && !empty($tags))
      {!! preg_replace('/<!--(.|\s)*?-->/', '', $quiz->website->get_integration("googleAds")->key) !!}
      @php $ga_has_integrations = true; @endphp
    @endif

  @endif

  @php $tm_is_used = false @endphp
  @if($quiz->website != null && $quiz->website->has_integration("tagManager"))

    @php $quiz_tm = $quiz->integrations("tagManager"); @endphp
    @if ($quiz_tm != null && $quiz_tm->pivot != null && $quiz_tm->pivot->value != null && $quiz_tm->pivot->value == 1)
      @php $tm_is_used = true @endphp
      {!! preg_replace('/<!--(.|\s)*?-->/', '', $quiz->website->get_integration("tagManager")->key) !!}
    @endif

  @endif

  @php $fb_has_integrations = false; @endphp
  @if($quiz->website != null && $quiz->website->has_integration("facebook"))
    @php
      $fb_integ = $quiz->website->get_integration("facebook")->key;
      $fb = $quiz->integrations('facebook');
    @endphp
    @if(!empty($fb_integ) && $fb != null && $fb->pivot != null && $fb->pivot->value != null)
      <!-- Meta Pixel Code -->
      {!! preg_replace('/<!--(.|\s)*?-->/', '', $fb_integ) !!}
      @php $fb_has_integrations = true; @endphp

    @endif
  @endif

  @php $tk_has_integrations = false; @endphp
  @if($quiz->website != null && $quiz->website->has_integration("tiktok"))

    @php
      $tk_integ = $quiz->website->get_integration("tiktok")->key;
      $tk = $quiz->integrations('tiktok');
    @endphp
    @if(!empty($tk_integ) && $tk != null && $tk->pivot != null && !empty($tk->pivot->value))
      <!-- Tiktok Code -->
      {!! preg_replace('/<!--(.|\s)*?-->/', '', $tk_integ) !!}
      @php $tk_has_integrations = true; @endphp
    @endif
  @endif

  @php $tw_has_integrations = false; @endphp
  @if($quiz->website != null && $quiz->website->has_integration("twitter"))
    @php
      $tw_integ = $quiz->website->get_integration("twitter")->key;
      $tw = $quiz->integrations('twitter');
    @endphp
    @if(!empty($tw_integ) && $tw != null && $tw->pivot != null && !empty($tw->pivot->value))
      <!-- Twitter Code -->
      {!! preg_replace('/<!--(.|\s)*?-->/', '', $tw_integ) !!}
      @if ($quiz->integrations("twitter")->pivot != null && !empty($quiz->integrations("twitter")->pivot->value))
        <script>
          twq("event", "{{$quiz->integrations("twitter")->pivot->value}}");
        </script>
      @endif
      @php $tw_has_integrations = true; @endphp
    @endif
  @endif

  @php $sc_has_integrations = false; @endphp
  @if($quiz->website != null && $quiz->website->has_integration("snapchat"))

    @php
      $sc_integ = $quiz->website->get_integration("snapchat")->key;
      $quiz_sc = $quiz->integrations("snapchat");
    @endphp
    @if(!empty($sc_integ) && $quiz_sc != null && $quiz_sc->pivot != null && $quiz_sc->pivot->value != null)
      <!-- Snapchat Code -->
      {!! preg_replace('/<!--(.|\s)*?-->/', '', $sc_integ) !!}

      @php $sc_has_integrations = true; @endphp
    @endif
  @endif

  {{-- Quill Core --}}
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
</head>
<body>

  @if($quiz->website != null && $quiz->website->has_integration("tagManager") && $tm_is_used)
    {!! preg_replace('/<!--(.|\s)*?-->/', '', $quiz->website->get_integration("tagManager")->value) !!}
  @endif

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

      @php $intro_exists = false @endphp
      @if($quiz->intro_title != null && $quiz->intro_btn != null)
        @php $intro_exists = true @endphp
        <div class="intro item active" id="quiz-intro">
          <div class="content text-center">
            <h1
                style=" color: {{$quiz->main_text_color}};"
                class="title">{{ $quiz->intro_title }}</h1>
            <p
                style=" color: {{$quiz->main_text_color}};"
                class="lead desc">{{ $quiz->intro_description }}</p>

            @if($quiz->intro_image != null)
              @if(File::exists(public_path("images/uploads/$quiz->intro_image")))
                <img src="{{ url("images/uploads/$quiz->intro_image") }}" class="img-fluid question-img d-block m-auto">
              @elseif(filter_var($quiz->intro_image, FILTER_VALIDATE_URL) == TRUE)
                <div class="embed-responsive embed-responsive-16by9">
                  <iframe class="d-block mx-auto my-2 yt-aspect embed-responsive-item" src="{{ $quiz->intro_image }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
              @endif
            @endif
            @if($quiz->policy_link != null && $quiz->is_shown_policy == 1)
              <div class="mt-3" id="acceptPolicy">
                <div>
                  <input type="checkbox" class="form-check-input" id="policyElement" style="scale:1.2">
                  <label for="policyElement"> {{ $quiz->policy_label }} <a href="{{ $quiz->policy_link }}">{{ __('Read More.') }}</a></label>
                </div>
              </div>
            @endif
            <button
                style="
                    color: {{$quiz->btn_text_color}};
                    background-color: {{ $quiz->btn_color }};"
                class="btn nex-btn"
                id="intro-btn"
                data-target="{{ $questions->first() != null ? "question-".$questions->first()->id : "no-result"; }}">{{ $quiz->intro_btn }}</button>
          </div>
        </div>
      @endif

      @foreach($questions as $i => $question)
        <div
            class="question {{ $questionsClasses[$question->type - 1] }} item no-answer-yet @if(!$intro_exists && $i == 0) active @endif"
            @if($quiz->website != null && isset($fb_has_integrations) && $fb_has_integrations == true)
              @if($question->has_fb_integrations())
                data-fb="{{ $question->fb_integrations->event }}"
              @endif
            @endif
            @if($quiz->website != null && isset($tw_has_integrations) && $tw_has_integrations == true && $question->has_tw_integrations())
              data-tw="{{ $question->tw_event->event }}"
            @endif
            @if($quiz->website != null && isset($tk_has_integrations) && $tk_has_integrations == true)
              @if($question->has_tk_integrations())
                data-tk="{{ $question->tk_event->event }}"
              @endif
            @endif
            {{-- @if($quiz->website != null && isset($sc_has_integrations) && $sc_has_integrations == true)
              @if($question->type == 3)
                data-sc="SIGN_UP"
              @endif
            @endif --}}
            id="question-{{ $question->id }}"
            data-id="{{ $question->id }}"
            data-type="{{ $question->type }}"
            data-order="{{ $question->order }}"
            style="order: {{ $question->order }}">
          <?php $letter = 'A'; ?>
          <div class="content">
            <h1
                class="title question-title"
                style=" color: {{$quiz->main_text_color}};"
                >{{ $question->title }}</h1>
            <p
                class="lead question-desc"
                style=" color: {{$quiz->main_text_color}};"
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

              <?php //$langsAnswers = $lang . "_answers"; ?>
              <div class="answers @if($question->type == 2) img-answers @endif my-4" id="answers-holder-{{$i}}" data-multi={{ $question->multi_select }}>

                @foreach ($question->answers as $x => $answer)

                  <div
                      @if($question->multi_select != 1)
                        @if($i >= count($questions) - 1) data-target="end"
                        @else data-target="question-{{ $questions[$i + 1]->id }}" @endif
                      @endif
                      @if($answer->fb_event != null)
                        data-fb="{{ $answer->fb_event->event ?? ""}}"
                      @endif
                      @if($answer->tk_event != null)
                        data-tk="{{ $answer->tk_event->event ?? ""}}"
                      @endif
                      @if($answer->sc_event != null)
                        data-sc="{{ $answer->sc_event->event ?? ""}}"
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
                  <p style=" color: {{$quiz->answer_text_color}};"
                    class="text m-0">{{ $answer->text }}</p>
                </div>

                @endforeach
              </div>
            @endif

            @if($question->type == 3)

              <form class="form disabled-form" id="fields-holder-{{$i}}">
                @foreach ($question->fields as $x => $field)

                  <div class="input-box">

                    @if($field->type != 13)
                      <label class="form-label" style="color: {{$quiz->main_text_color}}" for="form-{{$i}}-input-{{$x}}">{{ $field->label }} @if($field->is_required == 1) * @endif</label>
                    @endif

                    @if(in_array($field->type, [1, 2, 3, 4, 6, 9, 10, 11, 12]))
                      <input
                          {{ $field->is_required == 1 ? "required" : "" }}
                          class="view-input"
                          type="{{ $field->type_name() }}"
                          data-type="{{$field->type}}" data-id="{{ $field->id }}"
                          id="form-{{$i}}-input-{{$x}}"
                          placeholder="{{ $field->placeholder != 'undefined' ? $field->placeholder : __("write here") }}"
                          style="color: {{$quiz->main_text_color}};border-color: {{$quiz->border_color}};">
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
                          placeholder="{{ $field->placeholder != 'undefined' ? $field->placeholder : __("write here") }}"
                          style="color: {{$quiz->main_text_color}};border-color: {{$quiz->border_color}};"
                          ></textarea>
                    @elseif($field->type == 7)
                      @foreach($field->options as $y => $option)
                        @if($field->is_multiple_chooseing == 1)
                          <div class="holder">
                            <label class="form-label" for="form-{{$i}}-input-{{$x}}-option-{{$y}}">{{ $option->value }}</label>
                            <input
                              {{ $field->is_required == 1 ? "required" : "" }}
                              name="field[{{$field->id}}][]"
                              value="{{ $option->value }}"
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
                              value="{{ $option->value }}"
                              class="input-option d-none"
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
                        style="color: {{$quiz->main_text_color}};border-color: {{$quiz->border_color}};">
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
                @if($question->show_policy == 1)
                  <div class="mb-3" id="acceptPolicy">
                    <div>
                      <input type="checkbox" class="form-privacy form-check-input" id="policyElementLabel{{$question->id}}" required>
                      <label for="policyElementLabel{{$question->id}}">{{ $quiz->policy_label }} <a href="{{ $quiz->policy_link }}">Read More.</a></label>
                    </div>
                  </div>
                @endif
                <div class="btns-row">
                  <button
                    class="btn nex-btn mt-0"
                    style="
                          color: {{$quiz->btn_text_color}};
                          background-color: {{ $quiz->btn_color }};"
                    @if($i >= count($questions) - 1) data-target="end"
                    @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ $question->button_label ?? __("Submit") }}</button>
                  @if($question->is_skippable != null && $question->is_skippable == 1)
                    <button
                      class="btn skip"
                      style="
                            color: {{$quiz->btn_color}};
                            background-color: transparent;"
                      @if($i >= count($questions) - 1) data-target="end"
                      @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ __("Skip") }}</button>
                  @endif
                  <div class="error-box d-inline-block" style="float: {{ app()->getLocale() == "ar" ? "left" : "right" }}"></div>
                </div>
              @else
                <button
                  class="btn nex-btn"
                  style="
                        color: {{$quiz->btn_text_color}};
                        background-color: {{ $quiz->btn_color }};"
                  @if($i >= count($questions) - 1) data-target="end"
                  @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ $question->button_label ?? __("Submit") }}</button>
              @endif


            @endif

          </div>
        </div>
      @endforeach

      @foreach($results as $i => $result)

        @if($quiz->type == 1){{-- scoring --}}

          @if($result->type == 1)

            <div
                class="result item {{ $result[$result->type - 5 - 1] }} py-lg-4 px-lg-5 py-3 px-2 m-auto"
                @if($quiz->website != null && isset($fb_has_integrations) && $fb_has_integrations == true && $result->fb_event)
                  data-fb="{{ $result->fb_event }}"
                @endif
                @if($quiz->website != null && isset($tk_has_integrations) && $tk_has_integrations == true && $result->has_tk_integrations())
                  data-tk="{{ $question->tk_event }}"
                @endif
                @if($quiz->website != null && isset($tw_has_integrations) && $tw_has_integrations == true && $result->has_tw_integrations())
                  data-tw="{{ $question->tw_event }}"
                @endif
                {{-- @if($quiz->website != null && isset($sc_has_integrations) && $sc_has_integrations == true)
                  data-sc="VIEW_CONTENT"
                @endif --}}
                id="result-{{ $result->id }}"
                data-min-score="{{ $result->min_score }}"
                data-max-score="{{ $result->max_score }}"
                data-type="{{ $result->type }}"
                data-id="{{ $result->id }}">
              <div class="content">

                @if($result->show_social == 1)
                  <ul class="result-social-panel results-shares" style="color: white;">
                    <h2 class="title" style=" color: {{$quiz->main_text_color}};">
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

                <div class="body d-flex flex-column justify-content-center rounded">
                  <span class="title">{{ $result->title }}</span>
                  <span class="desc mb-2">
                    @php $descElements = json_decode($result->description); @endphp
                    @if(json_last_error() === JSON_ERROR_NONE && $descElements->ops != null && count($descElements->ops) > 0)
                      <div class="preview ql-editor"></div>
                      <div class="editor" data-result='{!! $result->description !!}' style="display: none"></div>
                    @endif
                  </span>
                  @if($result->show_button == 1)
                    <a
                        class="btn d-inline-block m-auto"
                        style="background: {{$quiz->result_btn_color}};color: {{$quiz->result_btn_text_color}};"
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
                @if($quiz->website != null && isset($fb_has_integrations) && $fb_has_integrations == true && $result->fb_event)
                  data-fb="{{ $result->fb_event }}"
                @endif
                @if($quiz->website != null && isset($tk_has_integrations) && $tk_has_integrations == true && $result->has_tk_integrations())
                  data-tk="{{ $question->tk_event }}"
                @endif
                @if($quiz->website != null && isset($tw_has_integrations) && $tw_has_integrations == true && $result->has_tw_integrations())
                  data-tw="{{ $question->tw_event }}"
                @endif
                {{-- @if($quiz->website != null && isset($sc_has_integrations) && $sc_has_integrations == true)
                  data-sc="VIEW_CONTENT"
                @endif --}}
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
                @if($quiz->website != null && isset($fb_has_integrations) && $fb_has_integrations == true && $result->fb_event)
                  data-fb="{{ $result->fb_event }}"
                @endif
                @if($quiz->website != null && isset($tk_has_integrations) && $tk_has_integrations == true && $result->has_tk_integrations())
                  data-tk="{{ $question->tk_event }}"
                @endif
                @if($quiz->website != null && isset($tw_has_integrations) && $tw_has_integrations == true && $result->has_tw_integrations())
                  data-tw="{{ $question->tw_event }}"
                @endif
                {{-- @if($quiz->website != null && isset($sc_has_integrations) && $sc_has_integrations == true)
                  data-sc="VIEW_CONTENT"
                @endif --}}
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

                <div class="body d-flex flex-column justify-content-center rounded">
                  <span class="title">{{ $result->title }}</span>
                  <span class="desc mb-2">
                    @php $descElements = json_decode($result->description); @endphp
                    @if(json_last_error() === JSON_ERROR_NONE && $descElements->ops != null && count($descElements->ops) > 0)

                      <div class="preview ql-editor"></div>
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
                @if($quiz->website != null && isset($fb_has_integrations) && $fb_has_integrations == true && $result->fb_event)
                  data-fb="{{ $result->fb_event }}"
                @endif
                @if($quiz->website != null && isset($tk_has_integrations) && $tk_has_integrations == true && $result->has_tk_integrations())
                  data-tk="{{ $question->tk_event }}"
                @endif
                @if($quiz->website != null && isset($tw_has_integrations) && $tw_has_integrations == true && $result->has_tw_integrations())
                  data-tw="{{ $question->tw_event }}"
                @endif
                {{-- @if($quiz->website != null && isset($sc_has_integrations) && $sc_has_integrations == true)
                  data-sc="VIEW_CONTENT"
                @endif --}}
                id="result-{{ $result->id }}"
                data-type="{{ $result->type }}"
                data-id="{{ $result->id }}">
              <div class="content">

                @if($result->show_social == 1)
                  <ul class="result-social-panel results-shares" style="color: white;">
                    <h2 class="title" style=" color: {{$quiz->main_text_color}};">
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

                <div class="body d-flex flex-column justify-content-center rounded">
                  <span class="title">{{ $result->title }}</span>
                  <span class="desc mb-2">
                    @php $descElements = json_decode($result->description); @endphp
                    @if(json_last_error() === JSON_ERROR_NONE && $descElements->ops != null && count($descElements->ops) > 0)
                      <div class="preview ql-editor"></div>
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
                @if($quiz->website != null && isset($fb_has_integrations) && $fb_has_integrations == true && $result->fb_event)
                  data-fb="{{ $result->fb_event }}"
                @endif
                @if($quiz->website != null && isset($tk_has_integrations) && $tk_has_integrations == true && $result->has_tk_integrations())
                  data-tk="{{ $question->tk_event }}"
                @endif
                @if($quiz->website != null && isset($tw_has_integrations) && $tw_has_integrations == true && $result->has_tw_integrations())
                  data-tw="{{ $question->tw_event }}"
                @endif
                {{-- @if($quiz->website != null && isset($sc_has_integrations) && $sc_has_integrations == true)
                  data-sc="VIEW_CONTENT"
                @endif --}}
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
                @if($quiz->website != null && isset($fb_has_integrations) && $fb_has_integrations == true && $result->fb_event)
                  data-fb="{{ $result->fb_event }}"
                @endif
                @if($quiz->website != null && isset($tk_has_integrations) && $tk_has_integrations == true && $result->has_tk_integrations())
                  data-tk="{{ $question->tk_event }}"
                @endif
                @if($quiz->website != null && isset($tw_has_integrations) && $tw_has_integrations == true && $result->has_tw_integrations())
                  data-tw="{{ $question->tw_event }}"
                @endif
                {{-- @if($quiz->website != null && isset($sc_has_integrations) && $sc_has_integrations == true)
                  data-sc="VIEW_CONTENT"
                @endif --}}
                id="result-{{ $result->id }}"
                data-type="{{ $result->type }}"
                data-id="{{ $result->id }}">
                <div class="content">

                  @if($result->show_score == 1)
                    <div class="score-panel rounded" id="score-panel">
                      <span>{{ $result->score_message }}</span>:<span id="result-{{ $result->id }}-score">50</span>
                    </div>
                  @endif

                  <div class="body d-flex flex-column justify-content-center rounded">
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
          <div class="body d-flex flex-column justify-content-center rounded">
            <span class="title">No Result ):</span>
            <span class="desc mb-2">these answers has no result</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="watermark website-logo-container @if($lang == "en") flex-row-reverse @endif">
    @if($quiz->website != null && $quiz->website->show_watermark)
      <img class="website-logo" src="{{ url("images/logo-2.png") }}" alt="logo">
    @endif
    @if($quiz->can_go_back == 1)
      <div class="steps-btns @if($lang != "en") flex-row-reverse @endif" id="steps-btns">
        <button id="back-ques-btn" style="color:{{ $quiz->btn_text_color }};background:{{ $quiz->btn_color }};border-color:{{ $quiz->border_color }};" class="btn border border-secondary opacity-50"><i class="fa fa-solid fa-arrow-left"></i></button>
        <button id="forward-ques-btn" style="color:{{ $quiz->btn_text_color }};background:{{ $quiz->btn_color }};border-color:{{ $quiz->border_color }};" class="btn border border-secondary opacity-50"><i class="fa fa-solid fa-arrow-right"></i></button>
      </div>
    @endif
  </div>

  <script>
    const FbInteg = '{!! htmlspecialchars($quiz->integrations("facebook") != null && $fb_has_integrations ? $quiz->integrations("facebook")->pivot->value : '') ?? false !!}';
    const TkInteg = '{!! htmlspecialchars($quiz->integrations("tiktok") != null && $tk_has_integrations ? $quiz->integrations("tiktok")->pivot->value : '') ?? false !!}';
    const TwInteg = '{!! htmlspecialchars($quiz->integrations("twitter") != null && $tw_has_integrations ? $quiz->integrations("twitter")->pivot->value : '') ?? false !!}';
    const ScInteg = {!! $sc_has_integrations == true ? "true" : "false" !!};
    const GAIteg = {!! json_encode($ga_has_integrations) !!};
    const QuizType = {{ $quiz->type }};
    const FormRoute = "{{ route('form_entry', $quiz->id) }}";
    const AnswersRoute = "{{ route('answering', $quiz->id) }}";
    const addViewRoute = "{{ url('ajax/add-view/') }}";
    const addViewResultRoute = "{{ url('ajax/add-view-result/') }}";
    const isTemplate = {{ $quiz->is_template }};
    const apiRoute = "{{ url('ajax/') }}";
    const __ = {
      "required fields are not filled": "{{ __('required fields are not filled') }}",
    };

    let forms = document.querySelectorAll(".disabled-form");
    forms.forEach(e => {
      e.addEventListener("submit", function (e) {
        e.preventDefault();
      })
    });

    @if(isset($submission_code) && $submission_code != null)
      window.submission = {{ $submission_code }};
    @endif

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

  @if($quiz->website != null && $quiz->website->has_integration("tagManager") && $quiz->google_tags != null)
    <script>
      function start_remarketing_ga() {
        {!! $quiz->google_tags->start_marketing !!}
      }
      function finish_remarketing_ga() {
        {!! $quiz->google_tags->finish_marketing !!}
      }
      function finish_convertion_ga() {
        {!! $quiz->google_tags->finish_conversion !!}
      }
      let funcs = [];
      @php
        $integs_ga_items = $quiz->itemgoogles();
      @endphp
      @if(!empty($integs_ga_items) && count($integs_ga_items) > 0)
        @foreach($integs_ga_items as $item)
          function integ_ga_item_{{ $item->item_id }}_{{ $item->item_type }}() {
            {!! $item->marketing_tag !!}
            {!! $item->conversion_tag !!}
          }
          funcs.push("integ_ga_item_{{ $item->item_id }}_{{ $item->item_type }}");
        @endforeach
      @endif
      start_remarketing_ga();
    </script>
  @endif

</body>
</html>

