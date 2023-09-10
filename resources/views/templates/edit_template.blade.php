@extends("layout.template-master")

<?php $translation = $quiz->translate(app()->getLocale())?>

@section("content")

  <div class="container quiz-content pb-5">

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
      <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
      </symbol>
      <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
      </symbol>
      <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
      </symbol>
    </svg>

    <div id="alerts"></div>

    <div class="py-4">
      <div class="title">
        <h3 id="quizTitleShow" class="quiz-title d-inline-block">{{ $quiz->name }}</h3>
        <button class="btn py-1 px-2 text-muted" data-bs-toggle="modal" data-bs-target="#TitleEditModal">
          <i class="fa fa-edit"></i>
        </button>
      </div>
      <span class="badge quiz-type-{{$quiz->type}}">{{ $quiz->type() }}</span>
    </div>

    <!-- quiz title Modal -->
    <div class="modal fade" id="TitleEditModal" tabindex="-1" aria-labelledby="exampleModalLabel">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Name Your Quiz</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <label class="mb-1" for="quizTitle">Set quiz name:</label>
            <input type="text" name="title" value="{{ $quiz->name }}" id="quizTitle" class="form-control" placeholder="Quiz Title">
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="editNameSubmit">Save changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="alert alert-warning p-4" style="border-style: dashed" id="introInfo" data-removable="introRemovable">

      <div id="introRemovable" class="{{ null !== $translation->intro_title && !empty($translation->intro_title) && null !== $translation->intro_btn && !empty($translation->intro_btn) ? 'd-none': ''}}">
        <h4 class="title text-dark">Introduction Info</h4>
        <p class="desc lead text-dark">fill the fields on the top of the option bar.</p>
      </div>

      <div class="container">
        <div class="text-center {{ null == $translation->intro_title || empty($translation->intro_title) || null == ($translation->intro_btn) || empty($translation->intro_btn) ? 'd-none': ''}}">
          <h3 class="text-dark title mb-3" id="introTitle">{{ $translation->intro_title }}</h3>
          <p class="text-dark lead mb-2"
              id="introDesc">{{ $translation->intro_description }}</p>
          <button type="button"
              class="btn btn-primary py-2 px-3 mt-4 {{ null == ($translation->intro_btn) || empty($translation->intro_btn) ? 'd-none': ''}}"
              id="introButton">{{ $translation->intro_btn }}</button>
        </div>
      </div>

    </div>

    <!-- Question Modal -->
    <div class="modal fade" id="questionsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
          <div class="modal-header px-4">
            <h5 class="modal-title">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0">
            <div class="row m-0">
              <div class="col-4 pr-0 bg-light">
                <div class="p-3" id="modalSidebar">

                </div>
              </div>
              <div class="col-8 px-0">
                <div class="quiz-preview m-0 p-0">

                  <div
                      style="{{ "background-color: $quiz->background_color" }};"
                      class="quiz-bg-img"
                      id="quiz-bg-img"></div>
                  <div
                      style="{{ $quiz->image ? "background-image: url(" . url("images/uploads/$quiz->image") . ")": "" }};
                          {{ "opacity: " . $quiz->image_opacity / 100 }};
                          "
                      class="quiz-bg-color"
                      id="quiz-bg-color"></div>
                  <div
                      class="p-0 py-0 quiz-content"
                      style="color: {{ $quiz->main_text_color }}; font-family: {{ $fonts[$quiz->font_family]["name"] }};"
                      data-font-family="{{ $fonts[$quiz->font_family]["name"] }}"
                      data-answer-bg-color="{{ $quiz->answer_bg_color }}"
                      data-highlight="{{ $quiz->highlight_color }}"
                      data-answer-text-color="{{ $quiz->answer_text_color }}"
                      data-btn-text-color="{{ $quiz->btn_text_color }}"
                      data-btn-color="{{ $quiz->btn_color }}"
                      data-result-btn-text-color="{{ $quiz->result_btn_text_color }}"
                      data-result-btn-color="{{ $quiz->result_btn_color }}"
                      id="modalPreview">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeQuestionModal" data-bs-dismiss="modal">Close</button>
            <button type="button" id="submit_modal" class="btn btn-primary">Save Question</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mapping Questinos Modal -->
    <div class="modal fade" id="mapping-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header px-4">
            <h5 class="modal-title">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="container">
              <div class="mb-3 pt-4">
                <p class="lead m-0 pb-3 border-bottom">Drag the answer to the result in order to map them.</p>
              </div>
              <div class="row m-0">
                <div class="col-6">
                  <div class="split">
                    <h2 class="title">test</h2>
                    <p class="lead">Answers:</p>
                    <div class="answers" id="mapping-modal-answers">

                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="split">
                    <h2 class="title">Results:</h2>
                    <p class="lead"><span id="results-counter">{{ $results->count() }}</span> results have been created</p>
                    <div class="results">
                      @foreach ($results as $result)
                        <div class="result card p-3 bg-light" style="min-height: 111px" data-id="{{ $result->id }}">
                          <h3 class="title">{{ $result->getOriginal(app()->getLocale() . "_title") }}</h3>
                          <p class="lead">Connected Answers:</p>
                          <div class="connected-answers" id="connected-answers-{{$result->id}}">
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeMappingModal" data-bs-dismiss="modal">Close</button>
            <button type="button" id="submit_mapping_modal" class="btn btn-primary">save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Branching Modal -->
    <div class="modal fade" id="branching-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header px-4">
            <h5 class="modal-title">Branching title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="split">
              <div class="container" id="conditions-container">

              </div>
              <div class="container">
                <button id="add-new-condition" class="mt-3 btn py-4 w-100 text-center border border-primary text-primary" style="border-style: dashed;">Add Condition</button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="closeBranchingModal" data-bs-dismiss="modal">Close</button>
            <button type="button" id="submit_branching_modal" class="btn btn-primary">save</button>
          </div>
        </div>
      </div>
    </div>

    <div id="contentItemsContainer" class="alert alert-primary list-group questions mb-3 p-4" style="border-style: dashed" id="contentItemsContainer" data-removable="contentRemovableInfo">

      <div id="contentRemovableInfo" {{ !empty($questions) && count($questions) > 0 ? "class=d-none": "" }}>
        <h4 class="title text-dark">Content Elements</h4>
        <p class="lead text-dark">Drag & Drop content elements here.</p>
      </div>

    </div>

    <div class="alert alert-danger p-4 results" style="border-style: dashed" id="resultItemsContainer" data-removable="resultRemovableInfo">

      <div id="resultRemovableInfo" {{ !empty($results) && count($results) > 0 ? "class=d-none": "" }}>
        <h4 class="title text-dark">Results Screen</h4>
        <p class="lead text-dark">Drag & Drop results elements here.</p>
      </div>

    </div>


  </div>{{-- container end --}}

@endsection

@section('options')

  <div class="quiz-options">
    @if(Auth::user()->is_sub() || Auth::user()->is_admin())
      <form action="{{ $formRoute }}" method="POST">
        @csrf

        <div class="mb-4">
          <label for="templateDesc" class="form-label">Set Template description:</label>
          <textarea
              required
              type="text"
              name="template_desc"
              id="templateDesc"
              class="form-control
              @if (count($errors) > 0)
                {{ count($errors->get("template_desc")) > 0 ? "is-invalid": "is-valid";}}
              @endif
              "
              value="{{ old("template_desc") ?? $quiz->template_desc ?? "" }}"
              placeholder="{{ __("Template Description (line or tow)") }}">{{ $quiz->template_desc }}</textarea>
          @if (count($errors->get("template_desc")) > 0)
            <div class="invalid-feedback">
              @foreach ($errors->get("template_desc") as $err)
                {{ $err }}
              @endforeach
            </div>
          @endif
        </div>
        <hr>
        <h3 class="title">Introduction Info:</h3>
        <p class="lead">this information will be shown at the starting page of the quiz.</p>
        <div class="p-0">
          <ul class="nav nav-tabs w-100">
            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            {{-- {{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }} --}}
            <li class="nav-link w-50 text-center option-link @if($localeCode == app()->getLocale()) active @endif" data-option="{{$localeCode}}-intro-page" id="{{$localeCode}}-intro-link">{{ $properties['native'] }}</li>
            @endforeach
          </ul>
          @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <div class="option-container py-3 px-2 bg-white border border-top-0 @if(app()->getLocale() != $localeCode) d-none @endif" id="{{$localeCode}}-intro-page">
              <div class="form-floating mb-3">
                <input
                    type="text"
                    class="form-control @error($localeCode . '_intro_title') is-invalid @enderror"
                    name="{{$localeCode}}_intro_title"
                    id="{{$localeCode}}_titleInput"
                    value="{{ old($localeCode . '_intro_title') ?? $quiz->translate($localeCode, true)->intro_title }}"
                    placeholder="Enter A Title">
                @if (count($errors->get($localeCode . "_intro_title")) > 0)
                  <div class="invalid-feedback">
                    @foreach ($errors->get($localeCode . "_intro_title") as $err)
                      {{ $err }}
                    @endforeach
                  </div>
                @endif
                <label for="{{$localeCode}}_titleInput">intro title *({{$localeCode}})</label>
              </div>
              <div class="form-floating mb-3">
                <textarea
                    class="form-control min-height @error($localeCode . '_intro_description') is-invalid @enderror"
                    name="{{$localeCode}}_intro_description"
                    id="{{$localeCode}}_descInput"
                    placeholder="Enter a Description"
                >{{ old($localeCode . '_intro_description') ?? $quiz->translate($localeCode, true)->intro_description }}</textarea>
                @if (count($errors->get($localeCode . "_intro_description")) > 0)
                  <div class="invalid-feedback">
                    @foreach ($errors->get($localeCode . "_intro_description") as $err)
                      {{ $err }}
                    @endforeach
                  </div>
                @endif
                <label for="{{$localeCode}}_descInput">intro description</label>
              </div>
              <div class="form-floating mb-3">
                <input
                    type="text"
                    class="form-control @error($localeCode . '_intro_btn') is-invalid @enderror"
                    name="{{$localeCode}}_intro_btn"
                    id="{{$localeCode}}_buttonInput"
                    value="{{ old($localeCode . '_intro_btn') ?? $quiz->translate($localeCode, true)->intro_btn }}"
                    placeholder="Button Text" />
                @if (count($errors->get($localeCode . "_intro_btn")) > 0)
                  <div class="invalid-feedback">
                    @foreach ($errors->get($localeCode . "_intro_btn") as $err)
                      {{ $err }}
                    @endforeach
                  </div>
                @endif
                <label for="{{$localeCode}}_buttonInput">Button Text *</label>
              </div>
            </div>
          @endforeach
        </div>

        @if ($errors->any())
          <div class="alert alert-danger">
            @foreach ($errors->all() as $err)
              <p>{{ $err }}</p>
            @endforeach
          </div>
        @endif

        <div class="my-3">
          <button class="btn btn-success">Submit <i class="fa fa-send"></i></button>
        </div>

      </form>
    @else
      <form action="{{ $formRoute }}" method="POST">
        @csrf
        <h3 class="title">Introduction Info:</h3>
        <p class="lead">this information will be shown at the starting page of the quiz.</p>
        <div class="form-floating mb-3">
          <input
              type="text"
              class="form-control @error('en_intro_title') is-invalid @enderror"
              name="en_intro_title"
              id="en_titleInput"
              value="{{ old('en_intro_title') ?? $translation->intro_title }}"
              placeholder="Enter A Title">
          @if (count($errors->get("en_intro_title")) > 0)
            <div class="invalid-feedback">
              @foreach ($errors->get("en_intro_title") as $err)
                {{ $err }}
              @endforeach
            </div>
          @endif
          <label for="en_titleInput">intro title *</label>
        </div>
        <div class="form-floating mb-3">
          <textarea
              class="form-control min-height @error('en_intro_description') is-invalid @enderror"
              name="en_intro_description"
              id="en_descInput"
              placeholder="Enter a Description"
          >{{ old('en_intro_description') ?? $translation->intro_description }}</textarea>
          @if (count($errors->get("en_intro_description")) > 0)
            <div class="invalid-feedback">
              @foreach ($errors->get("en_intro_description") as $err)
                {{ $err }}
              @endforeach
            </div>
          @endif
          <label for="en_descInput">intro description</label>
        </div>
        <div class="form-floating mb-3">
          <input
              type="text"
              class="form-control @error('en_intro_btn') is-invalid @enderror"
              name="en_intro_btn"
              id="en_buttonInput"
              value="{{ old('en_intro_btn') ?? $translation->intro_btn }}"
              placeholder="Button Text">
          @if (count($errors->get("en_intro_btn")) > 0)
            <div class="invalid-feedback">
              @foreach ($errors->get("en_intro_btn") as $err)
                {{ $err }}
              @endforeach
            </div>
          @endif
          <label for="en_buttonText">Button Text *</label>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger">
            @foreach ($errors->all() as $err)
              <p>{{ $err }}</p>
            @endforeach
          </div>
        @endif

        <div class="my-3">
          <button class="btn btn-success">Submit <i class="fa fa-send"></i></button>
        </div>

      </form>
    @endif
    <hr>
    <div class="container">
      <div class="pt-4 pb-2 px-2">
        <h5>Content Builder</h5>
        <p class="lead">Drag & drop content builder elements to create your content.</p>
      </div>
    </div>
    <hr>
    <div class="container">
      <b class="lead">Content Elements:</b>
      <div class="content-items row items mt-3 mb-4">
        @foreach ($questions_types as $quest)
          <div class="col-6 px-1 mb-2">
            <div class="item card flex-row draggable-question" draggable="true" data-item="{{ $quest["id"] }}" data-name="{{ $quest["name"] }}">
              <div class="icon-bg py-1 px-3 d-flex align-items-center justify-content-center">
                <i class="fa {{ $quest["icon"] }}"></i>
              </div>
              <div class="card-body py-1 px-2">
                <h6 class="card-title">{{ $quest["name"] }}</h6>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <b class="lead">Results Screen:</b>
      <div class="results-items row items mt-3 mb-4">
        @foreach ($results_types as $result)
          <div class="col-6 px-1 mb-2">
            <div class="item card flex-row draggable-result" draggable="true" data-item="{{ $result["id"] }}" data-name="{{ $result["name"] }}">
              <div class="icon-bg py-1 px-3 d-flex align-items-center justify-content-center">
                <i class="fa {{ $result["icon"] }}"></i>
              </div>
              <div class="card-body py-1 px-2">
                <h6 class="card-title">{{ $result["name"] }}</h6>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

@endsection


@section('scripts')
  <script>
    var edit_quiz_name_route  = "{{ route("edit_quiz_name", compact("quiz")) }}";
    var questions_ajax_route  = "{{ url(app()->getLocale() . "/ajax/questions") }}";
    var question_ajax_route   = "{{ url(app()->getLocale() . "/ajax/question") }}";
    var uploades_route        = "{{ url("/images/uploads") }}";
    var public_route          = "{{ url("") }}";
    var edit_result           = "{{ route("add_result", compact("quiz")) }}";
    var get_result            = "{{ url(app()->getLocale() . "/ajax/results/") }}";
    const actionCoolDownTime = 1500;
    const quizType = JSON.parse('{!! json_encode($quiz->type) !!}');// 1 => scoring, 2 => outcome
    var questions_types = JSON.parse('{!! json_encode($questions_types) !!}');
    var results_types = JSON.parse('{!! json_encode($results_types) !!}');
    const errTable = {
      "result_title": "#resultTitleInput",
      "result_link": "#resultButtonLink",
      "button_label": "#resultButtonLabel",
      "max_score": "#maxScoreInput",
      "min_score": "#minScoreInput",
      "en_score_message": "#scoreDisplayMsg",
      "show_button": "#showBtn",
    };
    const answersStyles = [
      ["A", "#016fdf"],
      ["B", "#63d581"],
      ["C", "#2ad5c7"],
      ["D", "#877fed"],
      ["E", "#b064db"],
      ["F", "#dd61aa"],
      ["G", "#f05242"],
      ["H", "#50cbe5"],
      ["I", "#537af8"],
      ["J", "#d789ed"],
      ["K", "#5c7d9f"],
      ["L", "#f6bd50"],
      ["M", "#d75965"],
      ["N", "#5a53af"],
      ["O", "#d55899"],
      ["P", "#5b95d0"],
      ["Q", "#87b65d"],
      ["R", "#f08f65"],
      ["S", "#88b3e8"],
      ["T", "#bb2b76"],
      ["U", "#2bb0ee"],
      ["V", "#79aa75"],
      ["W", "#be8b75"],
      ["X", "#d697d2"],
      ["Y", "#4fb0a1"],
      ["Z", "#bed05b"],
    ];
  </script>
  <script type="text/javascript" src="{{ URL::asset("js/sortable.js") }}"></script>
  <script type="text/javascript" src="{{ URL::asset("js/dashboard.js") }}"></script>
  <script>

    $(".nav-link").each(function () {
      $(this).click(function () {
        $(this).addClass("active").siblings().removeClass("active");
        $("#" + $(this).data("option")).removeClass("d-none").siblings(".option-container").addClass("d-none");
      });
    });

    $("#editNameSubmit").click(function () {
      editQuizName($("#quizTitle").val());
    });

    linkIntroInputs();// link intro data with it's demo views
    dragAndDropQuestionsHandler();// handling drag and drop question

    questionsReorderConfig();// questions reorder config


    $('#questionsModal').on("hide.bs.modal", function (e) {
      resetModal();
    });

    $('#questionsModal').on("show.bs.modal", function (e) {

      let modalParams = JSON.parse(e.relatedTarget ?? e);

      let modalTitle  = $(this).find('.modal-title');
      modalTitle.text(modalParams.itemName);

      if (modalParams.itemType == 1) {
        if (modalParams != null && modalParams.isEdit) {
          textQuestionModal("modalSidebar", "modalPreview", modalParams.data);
          updateQuestion(modalParams.data, quizType);
        }else {
          textQuestionModal("modalSidebar", "modalPreview");
          createNewQuestion(modalParams.itemType, quizType);
        }

      }else if (modalParams.itemType == 2) {
        if (modalParams != null && modalParams.isEdit) {
          imageQuestionModal("modalSidebar", "modalPreview", modalParams.data);
          updateQuestion(modalParams.data, quizType);
        }else {
          imageQuestionModal("modalSidebar", "modalPreview");
          createNewQuestion(modalParams.itemType, quizType);
        }
      }else if (modalParams.itemType == 3) {
        if (modalParams != null && modalParams.isEdit) {
          formModal("modalSidebar", "modalPreview", modalParams.data);
          updateQuestion(modalParams.data, quizType);
        }else {
          formModal("modalSidebar", "modalPreview");
          createNewQuestion(modalParams.itemType, quizType);
        }
      }else if (modalParams.itemType == 4) {
        if (modalParams != null && modalParams.isEdit) {
          textModal("modalSidebar", "modalPreview", modalParams.data);
          updateQuestion(modalParams.data, quizType);
        }else {
          textModal("modalSidebar", "modalPreview");
          createNewQuestion(modalParams.itemType, quizType);
        }
      }else if (modalParams.itemType == 5) {
        if (modalParams != null && modalParams.isEdit) {
          mediaModal("modalSidebar", "modalPreview", modalParams.data);
          updateQuestion(modalParams.data, quizType);
        }else {
          mediaModal("modalSidebar", "modalPreview");
          createNewQuestion(modalParams.itemType, quizType);
        }
      }else if (modalParams.itemType == 6) {
        if (modalParams != null && modalParams.isEdit) {
          resultModal("modalSidebar", "modalPreview", modalParams.data);
          modalParams.data["type"] += 5;
          updateQuestion(modalParams.data, quizType);
        }else {
          resultModal("modalSidebar", "modalPreview");
          createNewQuestion(modalParams.itemType, quizType);
        }
      }else if (modalParams.itemType == 7) {
        if (modalParams != null && modalParams.isEdit) {
          redirectModal("modalSidebar", modalParams.data);
          modalParams.data["type"] += 5;
          updateQuestion(modalParams.data, quizType);
        }else {
          redirectModal("modalSidebar");
          createNewQuestion(modalParams.itemType, quizType);
        }
      }else {
        resetModal();
      }
    });



    $('#mapping-modal').on("hide.bs.modal", function (e) {
      resetMappingModal();
    });

    $('#mapping-modal').on("show.bs.modal", function (e) {

      resetMappingModal();

      let modalParams = JSON.parse(e.relatedTarget);

      let modalTitle  = $(this).find('.modal-title');
      modalTitle.text(modalParams.itemName);

      modalParams.data.forEach(answer => {
        if (answer.image == null) {
          createMappingAnswer(answer.id, answersStyles[answer.order-1][0], answer.text, answersStyles[answer.order-1][1]);
        }else {
          createMappingAnswer(answer.id, null, answer.text, answer.image);
        }
        if (answer.results != null) {
          answer.results.forEach(result => {

            if (answer.image == null) {
              $(`#connected-answers-${result}`).append(
                $(`<div data-answer="${answer.id}" class="answer rounded" style="background-color: ${answersStyles[answer.order - 1][1]}">
                    <span class="letter">${answersStyles[answer.order - 1][0]}</span>
                    <i class="icon fa fa-trash"></i>
                  </div>`).click(function() {
                    $(this).fadeOut(150, function() {
                      this.remove();
                    });
                  })
              );
            }else {
              $(`#connected-answers-${result}`).append(
                $(`<div data-answer="${answer.id}" class="answer rounded" style="background-image: url('${uploades_route}/${answer.image}')">
                    <span class="letter"></span>
                    <i class="icon fa fa-trash"></i>
                  </div>`).click(function() {
                    $(this).fadeOut(150, function() {
                      this.remove();
                    });
                  })
              );
            }
          });
        }
      });

      let resultElements = document.querySelectorAll("#mapping-modal-answers .answer");

      resultElements.forEach(answer => {

        $(answer).on("dragstart", function (e) {
          let info = {
            "result_id": e.target.dataset.id,
          };
          e.originalEvent.dataTransfer.setData("text", JSON.stringify(info));
        });
      });

      $("#submit_mapping_modal").click(function () {
        let info = [];
        $("#mapping-modal .results .result").each(function (i) {
          let result = {};
          result.answers = [];
          result.id = $(this).data("id");
          $(this).find(".connected-answers .answer").each(function (y) {
            result.answers.push($(this).data("answer"));
          });
          info.push(result);
        });

        $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: `${question_ajax_route}/${modalParams.question_id}/mapping`,
            data: {
              results: info,
            },
            success: function(res) {
              resetMappingModal();
              $('#mapping-modal').modal("hide");
              let alert = makeSuccessAlert("Answers Mapped Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              console.error(res);
            }
          });

      });
    });



    $('#branching-modal').on("hide.bs.modal", function (e) {
      resetBranchingModal();
    });

    $('#branching-modal').on("show.bs.modal", function (e) {

      resetBranchingModal();
      let conditionCount = 0;
      var modalParams = JSON.parse(e.relatedTarget);

      let modalTitle  = $(this).find('.modal-title');
      modalTitle.text(modalParams.itemName);

      if (modalParams.data.conditions.length > 0) {
        modalParams.data.conditions.forEach((condition, i) => {
          createCondition(condition.id, conditionCount, modalParams.data.question_type, modalParams.data.answers, modalParams.data.targets, condition.question_id, condition);
          conditionCount++;
        });
      }

      $("#add-new-condition").click(function () {
        if ([1, 2].includes(modalParams.data.question_type)) {
          createCondition(null, conditionCount, modalParams.data.question_type, modalParams.data.answers, modalParams.data.targets, modalParams.question_id, null);
        }else {
          if ($("#conditions-container .condition").length == 0) {
            createCondition(null, conditionCount, modalParams.data.question_type, modalParams.data.answers, modalParams.data.targets, modalParams.question_id, null);
          }
        }
        conditionCount++;
      });

      $("#submit_branching_modal").click(function () {

        let info = [];

        $("#conditions-container .condition").each(function (i, val) {

          if ($(this).data("delete") != 1) {

            let answers = [];

            $(this).find(".branching").each(function () {
              answers.push($(this).val());
            });

            answers = answers.filter((item, index) => answers.indexOf(item) === index);

            info.push({
              "id":           $(this).data("id"),
              "answers":      answers,
              "question_id":  $(this).data("question"),
              "is_on":        $(`#${$(this).attr("id")}-is-on`).prop("checked"),
              "any_or":       $(`#${$(this).attr("id")}-any-or`).val(),
              "target_id":    $(`#${$(this).attr("id")}-target`).val(),
              "target_type":  $(`#${$(this).attr("id")}-target`).data("type"),
            });

          }else {
            if ($(this).data("id") != null) {
              info.push({
                "id": $(this).data("id"),
                "delete": 1,
              });
            }
          }
        });

        $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: `${question_ajax_route}/${modalParams.question_id}/conditioning`,
            data: {
              conditions: info,
            },
            success: function(res) {
              resetBranchingModal();
              $('#branching-modal').modal("hide");
              let alert = makeSuccessAlert("Branched Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              let alert = makeErrorAlert("Branching Field ):");
              setTimeout(() => {
                alert.close();
              }, 2000);
            }
        });

      });

    });

    @foreach ($questions as $question)
      createQuestion({{$question->order}}, "{{$question->getOriginal(app()->getLocale() . "_title")}}", {{$question->id}}, {{$question->type}}, "contentItemsContainer");
    @endforeach

    @foreach ($results as $result)
      createResult("{{$result->getOriginal(app()->getLocale() . "_title")}}", {{$result->id}}, {{$result->type + count($questions_types)}}, "resultItemsContainer", {{$result->min_score ?? "null"}}, {{$result->max_score ?? "null"}});
    @endforeach

    function linkIntroInputs() {

      let introTitle = $('#introTitle');
      let removable = $('#introRemovable');
      let introDesc = $('#introDesc');
      let introBtn = $('#introButton');

      $('#{{app()->getLocale()}}_titleInput').on('input', function () {
        var newText = $(this).val();
        introTitle.text(newText);
        if (newText.length > 0) {
          introTitle.parent().removeClass("d-none");
          introTitle.removeClass("d-none");
          removable.addClass("d-none");
          introDesc.removeClass("d-none");
        }else {
          introTitle.addClass("d-none");
          if (!introBtn.text()) {
            removable.removeClass("d-none");
            introDesc.addClass("d-none");
          }
        }
      });
      $('#{{app()->getLocale()}}_descInput').on('input', function () {
        var newText = $(this).val();
        introDesc.text(newText);
        if (newText.length > 0 && (!introBtn.hasClass("d-none") || !introTitle.hasClass("d-none"))) {
          introDesc.removeClass("d-none");
        }
      });
      $('#{{app()->getLocale()}}_buttonInput').on('input', function () {
        var newText = $(this).val();

        introBtn.text(newText);
        if (newText.length > 0) {
          introBtn.parent().removeClass("d-none");
          introBtn.removeClass("d-none");
          removable.addClass("d-none");
          introDesc.removeClass("d-none");
        }else {
          introBtn.addClass("d-none");
          if (!introTitle.text()) {
            removable.removeClass("d-none");
            introDesc.addClass("d-none");
          }
        }
      });
    }

    function dragAndDropQuestionsHandler() {
      let quiestionsContainer = $("#contentItemsContainer");
      let contentRemovableInfo = $("#contentRemovableInfo");
      let draggableQuestions = $(".draggable-question");
      let resultsContainer = $("#resultItemsContainer");
      let resultRemovableInfo = $("#resultRemovableInfo");
      let draggableResults = $(".draggable-result");
      let resultElements = document.querySelectorAll("#mapping-modal .results .result");

      quiestionsContainer.on("dragover", function (e) {
        e.preventDefault();
      });
      draggableQuestions.on("dragstart", function (e) {
        $(resultsContainer).addClass("opacity-50");
        let info = {
          "type": "question",
          "itemType": e.target.dataset.item,
          "itemName": e.target.dataset.name,
        };
        e.originalEvent.dataTransfer.setData("text", JSON.stringify(info));
      });
      draggableQuestions.on("dragend", function (e) {
        $(resultsContainer).removeClass("opacity-50");
      });
      quiestionsContainer.on("drop", function (e) {

        e.preventDefault();
        var data = e.originalEvent.dataTransfer.getData("text");// item => info object
        if (isJson(data)) {
          data = JSON.parse(data);
          if (data.type == "question") {
            if (data.itemType != null) {
              $('#questionsModal').modal('show', JSON.stringify(data));
            }
          }
        }

      });

      resultsContainer.on("dragover", function (e) {
        e.preventDefault();
      });
      draggableResults.on("dragstart", function (e) {
        $(quiestionsContainer).addClass("opacity-50");
        let info = {
          "type": "result",
          "itemType": e.target.dataset.item,
          "itemName": e.target.dataset.name,
        };
        e.originalEvent.dataTransfer.setData("text", JSON.stringify(info));
      });
      draggableResults.on("dragend", function (e) {
        $(quiestionsContainer).removeClass("opacity-50");
      });
      resultsContainer.on("drop", function (e) {

        e.preventDefault();
        var data = e.originalEvent.dataTransfer.getData("text");// item => info object
        if (isJson(data)) {
          data = JSON.parse(data);
          if (data.type == "result") {
            if (data.itemType != null) {
              $('#questionsModal').modal('show', JSON.stringify(data));
            }
          }
        }

      });

      resultElements.forEach(result => {
        $(result).on("dragover", function (e) {
          e.preventDefault();
        });
        $(result).on("drop", function (e) {
          e.preventDefault();
          var data = JSON.parse(e.originalEvent.dataTransfer.getData("text"));

          let answer = $(`#answer-${data.result_id}`);
          if (!$(result).find(".connected-answers").find(`.answer[data-answer="${answer.data("id")}"]`).length > 0) {
            if (answer.find(".letter").text() == "") {
              $(result).find(".connected-answers").append(
                $(`<div data-answer="${answer.data("id")}" class="answer rounded" style="background-image: ${answer.find(".letter").css("background-image").replace(/\"/g, "")}">
                    <i class="icon fa fa-trash"></i>
                  </div>`).click(function() {
                    $(this).fadeOut(150, function() {
                      this.remove();
                    });
                  })
              );
            }else {
              $(result).find(".connected-answers").append(
                $(`<div data-answer="${answer.data("id")}" class="answer rounded" style="background-color: ${answer.find(".letter").css("background-color")}">
                    <span class="letter">${answer.find(".letter").text()}</span>
                    <i class="icon fa fa-trash"></i>
                  </div>`).click(function() {
                    $(this).fadeOut(150, function() {
                      this.remove();
                    });
                  })
              );
            }
          }

        });
      });

    }

    function questionsReorderConfig() {
      var questionsReorderContainer = document.getElementById('contentItemsContainer');

      new Sortable(questionsReorderContainer, {
        animation: 150,
        handle: '.handler-questions',
        ghostClass: 'border-primary',
      });

      $("#contentItemsContainer").on("drop", function (e) {

        let val = e.originalEvent.dataTransfer.getData("text");
        if (!isJson(val)) {
          let orders = [];
          document.querySelectorAll("#contentItemsContainer .question").forEach(function (order, i) {
            order.dataset.order = i + 1;
            order.querySelector(".order b").innerText = order.dataset.order;
            orders.push({"id":order.dataset.id, "order":order.dataset.order});
          });
          reorderQuestions(orders);
        }
      });
    }

    function createNewQuestion(type, quizType=1) {
      $("#submit_modal").click(function () {

        if (type == 1) {

          var formData = new FormData();
          $("#answersContainer .answer-val").each(function (el) {
            formData.append(`en_answers[${el}][text]`, $(this).val());
            if (quizType == 1) {
              formData.append(`en_answers[${el}][score]`, $("#" + $(this).attr("id") + "-score-input").val());
            }
            formData.append(`en_answers[${el}][order]`, $(this).parent().data("order"));
          });

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", type);
          let imageValue = $("#imageInput").prop("files")[0];
          if (imageValue != undefined) {
            formData.append("image", imageValue);
          }
          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("multi_select", document.getElementById("multiSelect").checked == true ? 1 : 0);
          formData.append("order", $("#contentItemsContainer .question").length + 1);

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: "{{ route("add_question", compact("quiz")) }}",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              createQuestion($("#contentItemsContainer .question").length + 1, $("#EnQuestionTitleInput").val(), res, type, "contentItemsContainer");
              $("#contentRemovableInfo").addClass("d-none");
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Added Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              if (res.responseJSON.errors["question_title"] != null && res.responseJSON.errors["question_title"][0] != null) {
                $("#EnQuestionTitleInput").addClass("is-invalid");
                $("<div class='invalid-feedback'>" + res.responseJSON.errors["question_title"][0] + "</div>").insertAfter("#EnQuestionTitleInput");
              }
              let i = 0;
              for (const key in res.responseJSON.errors) {
                if (key == `answers.${i}.text`) {
                  let selector = `#Answer-${formData.get(`answers[${i}][order]`)}`;
                  $(selector).addClass("is-invalid");
                }
                i++;
              }
            }
          });
        }else if (type == 2) {

          var formData = new FormData();
          $("#answersContainer .answer-val").each(function (el) {
            formData.append(`en_answers[${el}][text]`, $(this).val());
            if (quizType == 1) {
              formData.append(`en_answers[${el}][score]`, $("#" + $(this).attr("id") + "-score-input").val());
            }
            formData.append(`en_answers[${el}][order]`, $(this).parent().data("order"));
            let imageValue = $(`#${$(this).attr('id')}-file`).prop("files")[0];
            if (imageValue != undefined && imageValue != "undefined") {
              formData.append(`en_answers[${el}][image]`, imageValue);
            }
          });

          let imageValue = $("#imageInput").prop("files")[0];
          if (imageValue != undefined && imageValue != "undefined") {
            formData.append("image", imageValue);
          }

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", type);
          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("multi_select", document.getElementById("multiSelect").checked == true ? 1 : 0);
          formData.append("order", $("#contentItemsContainer .question").length + 1);

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: "{{ route("add_question", compact("quiz")) }}",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              createQuestion($("#contentItemsContainer .question").length + 1, $("#EnQuestionTitleInput").val(), res, type, "contentItemsContainer");
              $("#contentRemovableInfo").addClass("d-none");
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Added Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              if (res.responseJSON.errors["question_title"] != null && res.responseJSON.errors["question_title"][0] != null) {
                $("#EnQuestionTitleInput").addClass("is-invalid");
                $("<div class='invalid-feedback'>" + res.responseJSON.errors["question_title"][0] + "</div>").insertAfter("#EnQuestionTitleInput");
              }
              let i = 0;
              for (const key in res.responseJSON.errors) {
                if (key == `answers.${i}.text`) {
                  let selector = `#Answer-${formData.get(`answers[${i}][order]`)}`;
                  $(selector).addClass("is-invalid");
                }
                i++;
              }
            }
          });

        }else if (type == 3) {

          var formData = new FormData();

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", type);

          let media_type = $("#modalSidebar .nav-tabs .option-link.active").data("option");
          formData.append("media_type", media_type);

          if (media_type == "image") {
            let imageValue = $("#imageInput").prop("files")[0];
            if (imageValue != null) {
              formData.append("image", imageValue);
            }
          }else if (media_type == "video") {
            formData.append("video", $("#videoUrlInput").val());
          }

          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("show_policy", $("#showPrivacyCheckbox").prop("checked"));
          formData.append("is_skippable", $("#isSkippableCheckbox").prop("checked"));
          formData.append("en_button_label", $("#EnbuttonLabelInput").val());
          formData.append("order", $("#contentItemsContainer .question").length + 1);

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: "{{ route("add_question", compact("quiz")) }}",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              createQuestion($("#contentItemsContainer .question").length + 1, $("#EnQuestionTitleInput").val(), res, type, "contentItemsContainer");
              $("#contentRemovableInfo").addClass("d-none");
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Added Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              if (res.responseJSON.errors["question_title"] != null && res.responseJSON.errors["question_title"][0] != null) {
                $("#EnQuestionTitleInput").addClass("is-invalid");
                $("<div class='invalid-feedback'>" + res.responseJSON.errors["question_title"][0] + "</div>").insertAfter("#EnQuestionTitleInput");
              }
              if (res.responseJSON.errors["button_label"] != null && res.responseJSON.errors["button_label"][0] != null) {
                $("#QuestionButtonLabel").addClass("is-invalid");
                $("<div class='invalid-feedback'>" + res.responseJSON.errors["button_label"][0] + "</div>").insertAfter("#QuestionButtonLabel");
              }
            }
          });
        }else if (type == 4) {

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "{{ route("add_question", compact("quiz")) }}",
            data: {
              "type_id": type,
              "en_question_title": $("#EnQuestionTitleInput").val(),
              "en_question_desc": $("#EnquestionDescInpiut").val(),
              "en_button_label": $("#EnQuestionButtonLabel").val(),
              "order": $("#contentItemsContainer .question").length + 1,
            },
            success: function(res) {
              createQuestion($("#contentItemsContainer .question").length + 1, $("#EnQuestionTitleInput").val(), res, type, "contentItemsContainer");
              $("#contentRemovableInfo").addClass("d-none");
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Added Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              if (res.responseJSON.errors["question_title"] != null && res.responseJSON.errors["question_title"][0] != null) {
                $("#EnQuestionTitleInput").addClass("is-invalid");
                $("<div class='invalid-feedback'>" + res.responseJSON.errors["question_title"][0] + "</div>").insertAfter("#EnQuestionTitleInput");
              }
              if (res.responseJSON.errors["button_label"] != null && res.responseJSON.errors["button_label"][0] != null) {
                $("#EnQuestionButtonLabel").addClass("is-invalid");
                $("<div class='invalid-feedback'>" + res.responseJSON.errors["button_label"][0] + "</div>").insertAfter("#EnQuestionButtonLabel");
              }
            }
          });

        }else if (type == 5) {

          var formData = new FormData();

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", type);

          let media_type = $("#modalSidebar .nav-tabs .option-link.active").data("option");
          formData.append("media_type", media_type);

          if (media_type == "image") {
            let imageValue = $("#imageInput").prop("files")[0];
            formData.append("image", imageValue);
          }else if (media_type == "video") {
            formData.append("video", $("#videoUrlInput").val());
          }

          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("en_button_label", $("#EnQuestionButtonLabel").val());
          formData.append("order", $("#contentItemsContainer .question").length + 1);

          if (formData.get("image") != null || formData.get("video") != null) {
            $.ajax({
              type: 'POST', // For jQuery < 1.9
              method: "POST",
              url: "{{ route("add_question", compact("quiz")) }}",
              data: formData,
              cache: false,
              processData: false,
              contentType: false,
              success: function(res) {
                createQuestion($("#contentItemsContainer .question").length + 1, $("#EnQuestionTitleInput").val(), res, type, "contentItemsContainer");
                $("#contentRemovableInfo").addClass("d-none");
                $(`#questionsModal`).modal("hide");
                let alert = makeSuccessAlert("Question Added Successfully");
                setTimeout(() => {
                  alert.close();
                }, 2000);
              },
              error: function(res) {
                if (res.responseJSON.errors["question_title"] != null && res.responseJSON.errors["question_title"][0] != null) {
                  $("#EnQuestionTitleInput").addClass("is-invalid");
                  $("<div class='invalid-feedback'>" + res.responseJSON.errors["question_title"][0] + "</div>").insertAfter("#EnQuestionTitleInput");
                }
                if (res.responseJSON.errors["button_label"] != null && res.responseJSON.errors["button_label"][0] != null) {
                  $("#EnQuestionButtonLabel").addClass("is-invalid");
                  $("<div class='invalid-feedback'>" + res.responseJSON.errors["button_label"][0] + "</div>").insertAfter("#EnQuestionButtonLabel");
                }
              }
            });
          }else {
            // TODO => make an error visualaization
            console.error("no Image Or Video");
          }
        }else if (type == 6) {

          var formData = new FormData();

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", type - 5);

          formData.append("en_result_title", $("#resultTitleInput").val());
          formData.append("en_result_desc", $("#resultDescInpiut").val());
          formData.append("show_button", $("#showBtn").prop("checked"));
          if (formData.get("show_button")) {
            formData.append("en_button_label", $("#resultButtonLabel").val());
            formData.append("result_link", $("#resultButtonLink").val());
          }
          formData.append("show_social", $("#social-sharing").prop("checked"));

          if (quizType == 1) {
            formData.append("min_score", $("#minScoreInput").val());
            formData.append("max_score", $("#maxScoreInput").val());
            formData.append("show_score", $("#show-score").prop("checked"));
            formData.append("en_score_message", $("#scoreDisplayMsg").val());
          }

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: "{{ route("add_result", compact("quiz")) }}",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              createResult($("#resultTitleInput").val(), res, type, "resultItemsContainer", formData.get("min_score"), formData.get("max_score"));
              $("#resultRemovableInfo").addClass("d-none");
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Result Added Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              for (const key in errTable) {
                $(errTable[key]).removeClass("is-invalid");
                $(errTable[key]).addClass("is-valid");
              }
              for (let err in res.responseJSON.errors) {
                if (res.responseJSON.errors[err] != null && res.responseJSON.errors[err][0] != null) {
                  $(errTable[err]).addClass("is-invalid");
                  if ($(errTable[err]).next(".invalid-feedback").length > 0) {
                    $(errTable[err]).siblings(".invalid-feedback").text(res.responseJSON.errors[err][0]);
                  }else {
                    $("<div class='invalid-feedback'>" + res.responseJSON.errors[err][0] + "</div>").insertAfter(errTable[err]);
                  }
                }
              }
            }
          });
        }else if (type == 7) {

          var formData = new FormData();

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", type - 5);

          formData.append("en_result_title", $("#resultTitleInput").val());
          formData.append("result_link", $("#resultButtonLink").val());
          if (quizType == 1) {
            formData.append("min_score", $("#minScoreInput").val());
            formData.append("max_score", $("#maxScoreInput").val());
          }
          formData.append("send_data", $("#sendFormData").prop("checked"));
          formData.append("send_utm", $("#send-utm").prop("checked"));

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: "{{ route("add_result", compact("quiz")) }}",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              createResult($("#resultTitleInput").val(), res, type, "resultItemsContainer", formData.get("min_score"), formData.get("max_score"));
              $("#resultRemovableInfo").addClass("d-none");
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Result Added Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              for (const key in errTable) {
                $(errTable[key]).removeClass("is-invalid");
                $(errTable[key]).addClass("is-valid");
              }
              for (let err in res.responseJSON.errors) {
                if (res.responseJSON.errors[err] != null && res.responseJSON.errors[err][0] != null) {
                  $(errTable[err]).addClass("is-invalid");
                  if ($(errTable[err]).next(".invalid-feedback").length > 0) {
                    $(errTable[err]).siblings(".invalid-feedback").text(res.responseJSON.errors[err][0]);
                  }else {
                    $("<div class='invalid-feedback'>" + res.responseJSON.errors[err][0] + "</div>").insertAfter(errTable[err]);
                  }
                }
              }
            }
          });
        }

      });
    }

    function updateQuestion(question, quizType) {

      $("#submit_modal").click(function () {
        if (question.type == 1) {

          var formData = new FormData();
          $("#answersContainer .answer-val").each(function (el) {
            formData.append(`en_answers[${el}][id]`, $(this).data("id"));
            formData.append(`en_answers[${el}][text]`, $(this).val());
            if (quizType == 1) {
              formData.append(`en_answers[${el}][score]`, $("#" + $(this).attr("id") + "-score-input").val());
            }
            formData.append(`en_answers[${el}][order]`, $(this).parent().data("order"));
            if ($(this).data("action") != undefined) {
              formData.append(`en_answers[${el}][action]`, $(this).data("action"));
            }
          });

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", question.type);
          let imageValue = $("#imageInput").prop("files")[0];
          if (imageValue != undefined) {
            formData.append("image", imageValue);
          }
          formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
          formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("multi_select", document.getElementById("multiSelect").checked == true ? 1 : 0);

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: `${question_ajax_route}/${question.id}`,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              $(`#question-${question.id}-title`).text($("#EnQuestionTitleInput").val());
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Updated Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              let alert = makeErrorAlert("Can't Update This Question");
              setTimeout(() => {
                alert.close();
              }, 2000);
              for (let error in res.responseJSON.errors) {
                console.error(error);
              }
            }
          });
        }else if (question.type == 2) {

          var formData = new FormData();
          $("#answersContainer .answer-val").each(function (el) {
            formData.append(`en_answers[${el}][id]`, $(this).data("id"));
            formData.append(`en_answers[${el}][text]`, $(this).val());
            if (quizType == 1) {
              formData.append(`en_answers[${el}][score]`, $("#" + $(this).attr("id") + "-score-input").val());
            }
            formData.append(`en_answers[${el}][order]`, $(this).parent().data("order"));
            if ($(this).data("action") != undefined) {
              formData.append(`en_answers[${el}][action]`, $(this).data("action"));
            }
            formData.append(`en_answers[${el}][image]`, $(`#${$(this).attr('id')}-file`).prop("files")[0]) ?? null;
          });

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", question.type);

          let imageValue = $("#imageInput").prop("files")[0];
          if (imageValue != undefined) {
            formData.append("image", imageValue);
          }
          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("multi_select", document.getElementById("multiSelect").checked == true ? 1 : 0);

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: `${question_ajax_route}/${question.id}`,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              $(`#question-${question.id}-title`).text($("#EnQuestionTitleInput").val());
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Updated Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              let alert = makeErrorAlert("Can't Update This Question");
              setTimeout(() => {
                alert.close();
              }, 2000);
              for (let error in res.responseJSON.errors) {
                console.error(error);
              }
            }
          });
        }else if (question.type == 3) {

          var formData = new FormData();

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", question.type);

          let media_type = $("#modalSidebar .nav-tabs .option-link.active").data("option");
          formData.append("media_type", media_type);

          if (media_type == "image") {
            let imageValue = $("#imageInput").prop("files")[0];
            if (imageValue != undefined) {
              formData.append("image", imageValue);
            }
          }else if (media_type == "video") {
            formData.append("video", $("#videoUrlInput").val());
          }

          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("show_policy", $("#showPrivacyCheckbox").prop("checked"));
          formData.append("is_skippable", $("#isSkippableCheckbox").prop("checked"));
          formData.append("en_button_label", $("#EnbuttonLabelInput").val());

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: `${question_ajax_route}/${question.id}`,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              $(`#question-${question.id}-title`).text($("#EnQuestionTitleInput").val());
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Updated Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              let alert = makeErrorAlert("Can't Update This Question");
              setTimeout(() => {
                alert.close();
              }, 2000);
              for (let error in res.responseJSON.errors) {
                console.error(error);
              }
            }
          });

        }else if (question.type == 4) {

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: `${question_ajax_route}/${question.id}`,
            data: {
              "type_id": question.type,
              "en_question_title": $("#EnQuestionTitleInput").val(),
              "en_question_desc": $("#EnquestionDescInpiut").val(),
              "en_button_label": $("#EnQuestionButtonLabel").val(),
            },
            success: function(res) {
              $(`#question-${question.id}-title`).text($("#EnQuestionTitleInput").val());
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Updated Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              if (res.responseJSON.errors["question_title"] != null && res.responseJSON.errors["question_title"][0] != null) {
                $("#EnQuestionTitleInput").addClass("is-invalid");
                if ($("#EnQuestionTitleInput").siblings(".invalid-feedback").length) {
                  $("#EnQuestionTitleInput").siblings(".invalid-feedback").text(res.responseJSON.errors["question_title"][0]);
                }else {
                  $("<div class='invalid-feedback'>" + res.responseJSON.errors["question_title"][0] + "</div>").insertAfter("#EnQuestionTitleInput");
                }
              }
              if (res.responseJSON.errors["button_label"] != null && res.responseJSON.errors["button_label"][0] != null) {
                $("#EnQuestionButtonLabel").addClass("is-invalid");
                $("<div class='invalid-feedback'>" + res.responseJSON.errors["button_label"][0] + "</div>").insertAfter("#EnQuestionButtonLabel");
              }
            }
          });

        }else if (question.type == 5) {

          var formData = new FormData();

          formData.append("_token","{{ csrf_token() }}");
          formData.append("type_id", question.type);

          let media_type = $("#modalSidebar .nav-tabs .option-link.active").data("option");
          formData.append("media_type", media_type);

          if (media_type == "image") {
            let imageValue = $("#imageInput").prop("files")[0];
            if (imageValue != undefined) {
              formData.append("image", imageValue);
            }
          }else if (media_type == "video") {
            formData.append("video", $("#videoUrlInput").val());
          }

          formData.append("en_question_title", $("#EnQuestionTitleInput").val());
          formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
          formData.append("en_button_label", $("#EnQuestionButtonLabel").val());

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: `${question_ajax_route}/${question.id}`,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(res) {
              $(`#question-${question.id}-title`).text($("#EnQuestionTitleInput").val());
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Question Updated Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              let alert = makeErrorAlert("Can't Update This Question");
              setTimeout(() => {
                alert.close();
              }, 2000);
              for (let error in res.responseJSON.errors) {
                console.error(error);
              }
            }
          });

        }else if (question.type == 6) {

          var data = {
            "type_id": question.type - 5,
            "en_result_title": $("#resultTitleInput").val(),
            "en_result_desc": $("#resultDescInpiut").val(),
            "show_social": $("#social-sharing").prop("checked"),
            "show_button": $("#showBtn").prop("checked"),
          };

          if (quizType == 1) {
            data["min_score"] = $("#minScoreInput").val();
            data["max_score"] = $("#maxScoreInput").val();
            data["show_score"] = $("#show-score").prop("checked");
            data["en_score_message"] = $("#scoreDisplayMsg").val();
          }

          if ($("#showBtn").prop("checked")) {
            data["en_button_label"] = $("#resultButtonLabel").val();
            data["result_link"] = $("#resultButtonLink").val();
          }

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: `${get_result}/${question.id}`,
            headers: {
              "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
            },
            data: data,
            success: function(res) {
              $(`#result-${question.id}-title`).text($("#resultTitleInput").val());
              let minScore = $("#minScoreInput").val();
              let maxScore = $("#maxScoreInput").val();
              $(`#result-${question.id} .score-title`).html(
                `Score: <b id="result-${question.id}-max-score">${minScore}</b> to <b id="result-${question.id}-max-score">${maxScore}</b>`
              );
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Result Updated Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              for (const key in errTable) {
                $(errTable[key]).removeClass("is-invalid");
                $(errTable[key]).addClass("is-valid");
              }
              for (let err in res.responseJSON.errors) {
                if (res.responseJSON.errors[err] != null && res.responseJSON.errors[err][0] != null) {
                  $(errTable[err]).addClass("is-invalid");
                  if ($(errTable[err]).next(".invalid-feedback").length > 0) {
                    $(errTable[err]).siblings(".invalid-feedback").text(res.responseJSON.errors[err][0]);
                  }else {
                    $("<div class='invalid-feedback'>" + res.responseJSON.errors[err][0] + "</div>").insertAfter(errTable[err]);
                  }
                }
              }
            }
          });

        }else if (question.type == 7) {

          var data = {
            "type_id": question.type - 5,
            "en_result_title": $("#resultTitleInput").val(),
            "result_link": $("#resultButtonLink").val(),
            "send_data": $("#sendFormData").prop("checked"),
            "send_utm": $("#send-utm").prop("checked"),
          };

          if (quizType == 1) {
            data["min_score"] = $("#minScoreInput").val();
            data["max_score"] = $("#maxScoreInput").val();
          }

          $.ajax({
            type: 'POST', // For jQuery < 1.9
            method: "POST",
            url: `${get_result}/${question.id}`,
            headers: {
              "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
            },
            data: data,
            success: function(res) {
              $(`#result-${question.id}-title`).text($("#resultTitleInput").val());
              $(`#result-${question.id}-min-score`).text(data["min_score"]);
              $(`#result-${question.id}-max-score`).text(data["max_score"]);
              $(`#questionsModal`).modal("hide");
              let alert = makeSuccessAlert("Result Updated Successfully");
              setTimeout(() => {
                alert.close();
              }, 2000);
            },
            error: function(res) {
              for (const key in errTable) {
                $(errTable[key]).removeClass("is-invalid");
                $(errTable[key]).addClass("is-valid");
              }
              for (let err in res.responseJSON.errors) {
                if (res.responseJSON.errors[err] != null && res.responseJSON.errors[err][0] != null) {
                  $(errTable[err]).addClass("is-invalid");
                  if ($(errTable[err]).next(".invalid-feedback").length > 0) {
                    $(errTable[err]).siblings(".invalid-feedback").text(res.responseJSON.errors[err][0]);
                  }else {
                    $("<div class='invalid-feedback'>" + res.responseJSON.errors[err][0] + "</div>").insertAfter(errTable[err]);
                  }
                }
              }
            }
          });

        }
      });
    }

    function reorderQuestions(orders) {
      $.ajax({
          method: "POST",
          url: "{{ route("reorder_quiz_questions", compact("quiz")) }}",
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: {
            "questions": orders,
          },
          success: function(res) {
            let alert = makeSuccessAlert("Questions Reordered Successfully");
            setTimeout(() => {
              alert.close();
            }, 2000);
          },
          error: function(res) {
            console.error(res);
          }
      });
    }

  </script>
@endsection
