@extends("layout.app-quiz-build")

@section('styles')
  <style>
    .config-options .nav-item {
      margin-top: 4px
    }
    .config-options .nav-item:first-of-type {
      margin-top: 14px
    }
    .config-options .nav-link {
      transition: 0.25s ease-out;
      position: relative;
      cursor: pointer;
    }
    .config-options .nav-link.disabled {
      user-select: none;
    }
    .config-options .nav-link::after {
      content: "";
      width: 0%;
      background-color: #0d6efd;
      height: 2px;
      transition: 0.25s ease-out;
      border-radius: 2px;
      position: absolute;
      bottom: 0;
      left: 0;
    }
    .config-options .nav-link:not(.disabled):hover,
    .config-options .nav-link.active {
      background: #f2f2f2;
    }
    .config-options .nav-link:not(.disabled):hover::after,
    .config-options .nav-link.active::after {
      width: 30%;
    }
    .sidebar .config-options .nav-item .nav-link.active {
      color: #0d6efd;
    }
    .sidebar .config-options .nav-item .nav-link.active .lead {
      color: #222;
    }
    .no-after-before::after,
    .no-after-before::before {
      content: none !important;
    }
    .dropdown-item {
      user-select: none;
    }
  </style>
  <link rel="stylesheet" href="https://unpkg.com/tagin@2.0.2/dist/tagin.min.css">
@endsection

@section("content")

  <div class="quiz-content">
    <form action="{{ route("update_config_quiz", compact("quiz")) }}" method="POST">
      @csrf
      <div id="general" class="page config-form">
        <div class="container px-5 py-4">
          <div class="general-width">
            <h4>Meta Information:</h4>
            <p class="lead">Used by social networks and search engines</p>
            <div class="mb-3">
              <label for="metaTitle" class="form-label">Title:</label>
              <input class="@error('meta_label') is-invalid @enderror form-control" name="meta_title" id="metaTitle" value="{{ old("meta_title") ?? $quiz->meta_title }}">
              @if (count($errors->get("meta_description")) > 0)
                <div class="invalid-feedback">
                  @foreach ($errors->get("meta_description") as $err)
                    {{ $err }}
                  @endforeach
                </div>
              @endif
            </div>
            <div class="mb-3">
              <label for="metaDesc" class="form-label">Description:</label>
              <textarea class="@error('meta_description') is-invalid @enderror form-control" name="meta_description" id="metaDesc">{{ old("meta_description") ?? $quiz->meta_description }}</textarea>
                @if (count($errors->get("meta_description")) > 0)
                  <div class="invalid-feedback">
                    @foreach ($errors->get("meta_description") as $err)
                      {{ $err }}
                    @endforeach
                  </div>
                @endif
            </div>
          </div>
          <hr>
        </div>
        <div class="container px-5 pt-4 pb-2">
          <div class="mb-3">
            <div class="general-width">
              <div class="form-check form-switch p-0 my-0 d-flex gap-3">
                <label class="form-check-label form-label" for="isShowBrand">Company Branding:</label>
                <input class="@error('show_logo') is-invalid @enderror form-check-input mx-0 float-none" name="show_logo" type="checkbox" role="switch" id="isShowBrand" {{ $quiz->show_logo ? "checked": ""}}>
                @if (count($errors->get("show_logo")) > 0)
                  <div class="invalid-feedback">
                    @foreach ($errors->get("show_logo") as $err)
                      {{ $err }}
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          </div>
          <hr>
        </div>
        <div class="container px-5 py-4">
          <div class="general-width">
            <h4>Set Privacy Policy</h4>
            <p class="lead">Because you will be collecting information, you should include a link to your company’s privacy policy.</p>
            <div class="mb-3">
              <label for="policyLabel" class="form-label">Text:</label>
              <textarea class="@error('policy_label') is-invalid @enderror form-control" name="policy_label" id="policyLabel">{{ old("policy_label") ?? $quiz->policy_label }}</textarea>
              @if (count($errors->get("policy_label")) > 0)
                <div class="invalid-feedback">
                  @foreach ($errors->get("policy_label") as $err)
                    {{ $err }}
                  @endforeach
                </div>
              @endif
            </div>
            <div class="mb-3">
              <label for="policyLink" class="form-label">Privacy Policy Link:</label>
              <input class="@error('policy_link') is-invalid @enderror form-control" name="policy_link" id="policyLink" value="{{ old("policy_link") ?? $quiz->policy_link }}">
              @if (count($errors->get("policy_link")) > 0)
                <div class="invalid-feedback">
                  @foreach ($errors->get("policy_link") as $err)
                    {{ $err }}
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        </div>
        <div class="container px-5 py-4">
          <hr>
          <button type="submit" class="btn btn-primary px-4 py-2">Save <i style="margin-left: 8px" class="fa fa-save"></i></button>
        </div>
      </div>
    </form>
    <form id="integrations-form" action="{{ route("update_integrations", compact("quiz")) }}" method="POST">
      @csrf
      <div id="native-integrations" class="page">
        <div class="container px-5 py-4">
          <div class="general-width">
            <h4>native integrations:</h4>
            <p class="lead">setup your quiz's native integrations</p>
            <div class="mb-3">
              @if ($errors->any())
              <div class="alert alert-danger mb-3">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif
            </div>
          </div>
          <hr>

          @if(count(config("integrations.INTEGRATIONS")) > 0)
            <div class="accordion" id="integrationsAccording">
              @foreach (config("integrations.INTEGRATIONS") as $integ)
                @if($integ['IS_USABLE'])
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="integration-{{ $integ['ID'] }}">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $integ['ID'] }}" aria-expanded="true" aria-controls="collapse-{{ $integ['ID'] }}">
                        {{ $integ['NAME'] }}
                      </button>
                    </h2>
                    <div id="collapse-{{ $integ['ID'] }}" class="accordion-collapse collapse" aria-labelledby="integration-{{ $integ['ID'] }}" data-bs-parent="#integrationsAccording">
                      <div class="accordion-body">

                        @if($integ['INTEGRATION'] == 'facebook')

                          <div class="mb-3">
                            <label for="custom-event" class="form-label lead">Set custom event name: <small class="text-danger">(required)</small></label>
                            <input type="text" name="fb[fb-custom-event]" class="form-control" id="custom-event" value="{{ $integs['fb']['custom_event_name'] ?? '' }}">
                          </div>
                          <div class="row">
                            <div class="col-md-8">
                              Content Builder Elements
                            </div>
                            <div class="col-md-4">
                              Parameter value
                            </div>
                          </div>

                          @foreach ($quiz->questions_with_answers as $key => $question)
                            <div class="card">
                              <div class="card-body py-2">
                                <div class="row">
                                  <div class="col-md-8">
                                    <p class="my-1">
                                      <span class="badge @if(in_array($question->type, [1,2,3,4,5])) bg-primary @endif">{{ $question->types()[$question->type]['name'] }}</span> {{ $question->title }}
                                    </p>
                                  </div>
                                  <div class="col-md-4">
                                    <input
                                        type="hidden"
                                        class="hidden"
                                        name="fb[questions][{{ $key }}][id]"
                                        value="{{ $question->id }}">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="fb[questions][{{ $key }}][val]"
                                        value="{{ $question->has_integrations($integ['INTEGRATION']) ? $question->get_integration($integ['INTEGRATION'])->value : ''}}">
                                  </div>
                                </div>
                              </div>
                            </div>
                          @endforeach

                        @elseif($integ['INTEGRATION'] == 'activeCompaign')

                          {{-- Tagin Script For Active Campagin Fields --}}
                          <script src="https://unpkg.com/tagin@2.0.2/dist/tagin.min.js"></script>

                          <div class="mb-3">
                            <label for="ac-lists" class="form-label lead">Select ActiveCampaign List <small class="text-danger">(required)</small></label>
                            <select class="form-select" id="ac-lists" name="ac[list]">
                              <option>-- select list --</option>
                              @if($integs["ac"]['ac_info'] != null && $integs["ac"]['ac_info']->lists != null)
                                @foreach($integs["ac"]['ac_info']->lists as $integration_field)
                                  <option value="{{ $integration_field->id }}" @if(($integs["ac"]['active_list'] != null && $integs["ac"]['active_list']->pivot->value == $integration_field->id) || old('ac.list') == $integration_field->id) selected @endif>{{ $integration_field->name }}</option>
                                @endforeach
                              @endif
                            </select>
                          </div>

                          <div id="ac_fields">
                            @if(isset($integs["ac"]['fields']) && count($integs["ac"]['fields']) > 0)
                              @foreach($integs["ac"]['fields'] as $i => $field)
                                <div class="d-flex tagin-field mt-2">
                                  <input type="hidden" name="ac[fields][{{$i}}][id]" value="{{ $field->id }}">
                                  <input type="hidden" name="ac[fields][{{$i}}][type]" class="type-input" @if(isset($field->value_type)) value="{{ $field->value_type ?? "" }}" @endif>
                                  <input type="text" name="ac[fields][{{$i}}][val]" class="form-control tagin" data-title="{{ $field->title }}" value="{{ old('ac.fields.'.$i.'.val') != null ? old('ac.fields.'.$i.'.val') : (isset($field->value) ? $field->value : "") }}">
                                  <div class="no-after-before dropstart">
                                    <button class="p-1 btn btn-light dropdown-toggle no-after-before" type="button" data-bs-toggle="dropdown">
                                      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                          width="32px" height="32px" viewBox="0 0 42 42" enable-background="new 0 0 42 42" xml:space="preserve">
                                        <g>
                                          <defs>
                                            <path id="SVGID_1_" d="M22,25c0,0.6-0.4,1-1,1h-6c-0.6,0-1-0.4-1-1s0.4-1,1-1h6C21.6,24,22,24.4,22,25z M10,25c0,1.1-0.9,2-2,2
                                              s-2-0.9-2-2s0.9-2,2-2S10,23.9,10,25z M14,11c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,12,14,11.6,14,11z M10,11
                                              c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,9.9,10,11z M10,18c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,16.9,10,18z M14,18
                                              c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,19,14,18.6,14,18z M38,33c0,0.6-0.4,1-1,1h-3v3c0,0.6-0.4,1-1,1
                                              s-1-0.4-1-1v-3h-3c-0.6,0-1-0.4-1-1s0.4-1,1-1h3v-3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3C37.6,32,38,32.4,38,33z M33,40
                                              c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7S36.9,40,33,40z M3,34c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h30c0.6,0,1,0.4,1,1v21.1
                                              c-0.3,0-0.7-0.1-1-0.1c-5,0-9,4-9,9c0,0.3,0.1,0.7,0.1,1H3z M36,24.6V3c0-1.7-1.3-3-3-3H3C1.3,0,0,1.3,0,3v30c0,1.7,1.3,3,3,3
                                              h21.6c1.2,3.5,4.5,6,8.4,6c5,0,9-4,9-9C42,29.1,39.5,25.8,36,24.6z"/>
                                          </defs>
                                          <use xlink:href="#SVGID_1_"  overflow="visible" fill="#878787"/>
                                          <clipPath id="SVGID_2_">
                                            <use xlink:href="#SVGID_1_"  overflow="visible"/>
                                          </clipPath>
                                          <rect x="-5" y="-5" clip-path="url(#SVGID_2_)" fill="#878787" width="52" height="52"/>
                                        </g>
                                      </svg>
                                    </button>
                                    <ul class="dropdown-menu px-3">
                                      @php $value_types = ['Quiz Name', 'Content Item', 'Content Result', 'From Field', 'date']; @endphp
                                      @foreach($integs["ac"]['field_values'] as $value)
                                        <li class="dropdown-item tagin-value" data-value="{{ $value->ac_value_title }}" data-id="{{ $value->ac_value_id }}" data-type="{{ $value->ac_value_type }}">({{ $value_types[$value->ac_value_type] }}) {{ $value->ac_value_title }}</li>
                                      @endforeach
                                    </ul>
                                  </div>
                                </div>
                              @endforeach
                            @endif
                          </div>

                          <script>

                            function transformToType(id) {
                              let arr1 = ['Quiz','Question','Result','Field','Date'];
                              if (arr1[id] != null && arr1[id] != undefined) {
                                return arr1[id];
                              }
                              return false;
                            }
                            Tagin.prototype.createTag = function(t) {
                              const e =
                                "this.closest('div').dispatchEvent(new CustomEvent('tagin:remove', { detail: this }));this.parentElement.parentElement.parentElement.querySelector('.tagin').dataset.value='';";
                                return `<span class="${this.classTag}">${t}<span onclick="${e}" class="${this.classRemove}"></span></span>`;
                            };
                            Tagin.prototype.removeTages = function() {
                              if (this.getTags().length > 0) {
                                this.target.value = '';
                                Array.from(this.wrapper.getElementsByClassName(this.classTag)).forEach(el => {
                                  el.remove();
                                });
                              }
                            };
                            document.querySelectorAll('.tagin-field').forEach(field => {
                              let valueInput = field.querySelector('.tagin');
                              let tagin = new Tagin(valueInput, {
                                  placeholder: valueInput.dataset.title,
                                  separator: '|',
                                });
                              tagin.input.setAttribute('readonly' ,true);
                              field.querySelectorAll('.tagin-value').forEach(value => {
                                value.addEventListener('click', function () {
                                  tagin.removeTages()
                                  tagin.addTag(value.dataset.value)
                                  tagin.target.dataset.value = value.dataset.id;
                                  tagin.target.parentElement.querySelector(".type-input").value = transformToType(value.dataset.type);
                                })
                              });
                              field.querySelectorAll('.tagin-tag-remove').forEach(removeBtn => {
                                removeBtn.addEventListener('click', function () {
                                  tagin.target.dataset.value = '';
                                })
                              });
                            });

                          </script>

                        @endif

                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          @endif

          <hr>
          <button type="submit" class="btn btn-primary px-4 py-2">Save <i style="margin-left: 8px" class="fa fa-save"></i></button>
        </div>
      </div>
    </form>
  </div>

@endsection

@section('options')

  <div class="sidebar">

    <div class="container">

      <ul class="nav config-options flex-column">
        <li class="nav-item">
          <a class="nav-link active" data-page="general">
            General
            <p class="lead mb-0">basic setting</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-page="native-integrations">
            Native Integrations
            <p class="lead mb-0">integrate your quizzes using native integrations</p>
          </a>
        </li>
      </ul>

    </div>

  </div>

@endsection


@section('scripts')
  <script>
    function activePage(id) {
      $(".config-options").find(".nav-link[data-page=\"" + id + "\"]").addClass("active").parent().siblings(".nav-item").find(".nav-link").removeClass("active");
      $("#" + id).fadeIn().parent().siblings().find('.page').hide();
    }
    $(".config-options .nav-link").each(function () {
      $(this).click(function () {
        activePage($(this).data("page"));
      });
    });

    activePage("native-integrations");

    $("#ac-lists").on('input', function () {
      $.ajax({
          method: "GET",
          url: `/ajax/quiz/{{$quiz->id}}/ac/${$(this).val()}/fields`,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: {
            list: $(this).val(),
          },
          success: function(res) {
            let value_types = ['Quiz Name', 'Content Item', 'Content Result', 'From Field', 'date'];
            $("#ac_fields").html("");
            res[0].forEach((field, i) => {
              let element = `
                <div id="field-${i}-${field.id}" class="d-flex tagin-field mt-2">
                  <input type="hidden" name="ac[fields][${i}][id]" value="${field.id}">
                  <input type="hidden" name="ac[fields][${i}][type]" class="type-input" value="${field.value_type ?? ""}">
                  <input id="field-${i}-${field.id}-value" type="text" name="ac[fields][${i}][val]" class="form-control tagin" data-title="${field.title}" value="${field.value != null ? field.value : "" }">
                  <div class="no-after-before dropstart">
                    <button class="p-1 btn btn-light dropdown-toggle no-after-before" type="button" data-bs-toggle="dropdown">
                      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                          width="32px" height="32px" viewBox="0 0 42 42" enable-background="new 0 0 42 42" xml:space="preserve">
                        <g>
                          <defs>
                            <path id="SVGID_1_" d="M22,25c0,0.6-0.4,1-1,1h-6c-0.6,0-1-0.4-1-1s0.4-1,1-1h6C21.6,24,22,24.4,22,25z M10,25c0,1.1-0.9,2-2,2
                              s-2-0.9-2-2s0.9-2,2-2S10,23.9,10,25z M14,11c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,12,14,11.6,14,11z M10,11
                              c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,9.9,10,11z M10,18c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,16.9,10,18z M14,18
                              c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,19,14,18.6,14,18z M38,33c0,0.6-0.4,1-1,1h-3v3c0,0.6-0.4,1-1,1
                              s-1-0.4-1-1v-3h-3c-0.6,0-1-0.4-1-1s0.4-1,1-1h3v-3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3C37.6,32,38,32.4,38,33z M33,40
                              c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7S36.9,40,33,40z M3,34c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h30c0.6,0,1,0.4,1,1v21.1
                              c-0.3,0-0.7-0.1-1-0.1c-5,0-9,4-9,9c0,0.3,0.1,0.7,0.1,1H3z M36,24.6V3c0-1.7-1.3-3-3-3H3C1.3,0,0,1.3,0,3v30c0,1.7,1.3,3,3,3
                              h21.6c1.2,3.5,4.5,6,8.4,6c5,0,9-4,9-9C42,29.1,39.5,25.8,36,24.6z"/>
                          </defs>
                          <use xlink:href="#SVGID_1_"  overflow="visible" fill="#878787"/>
                          <clipPath id="SVGID_2_">
                            <use xlink:href="#SVGID_1_"  overflow="visible"/>
                          </clipPath>
                          <rect x="-5" y="-5" clip-path="url(#SVGID_2_)" fill="#878787" width="52" height="52"/>
                        </g>
                      </svg>
                    </button>
                    <ul class="dropdown-menu px-3">`;

              for (const opt in res[1]) {
                element += `<li class="dropdown-item tagin-value" data-value="${res[1][opt].ac_value_title}" data-id="${res[1][opt].ac_value_id}" data-type="${res[1][opt].ac_value_type}">(${value_types[res[1][opt].ac_value_type]}) ${res[1][opt].ac_value_title}</li>`;
              }
              element += `</ul></div></div>`;
              $("#ac_fields").append(element);

              let fieldEl = $(`#field-${i}-${field.id}`)[0];
              let valueInput = $(`#field-${i}-${field.id}-value`);
              let tagin = new Tagin(valueInput[0], {
                  placeholder: valueInput[0].dataset.title,
                  separator: '|',
                });
              tagin.input.setAttribute('readonly' ,true);
              fieldEl.querySelectorAll('.tagin-value').forEach(value => {
                value.addEventListener('click', function () {
                  tagin.removeTages()
                  tagin.addTag(value.dataset.value)
                  tagin.target.dataset.value = value.dataset.id;
                  tagin.target.parentElement.querySelector(".type-input").value = transformToType(value.dataset.type);
                })
              });

            });
          },
          error: function(res) {
            console.error(res);
          }
      });

    });

    $("#integrations-form").on('submit', function (e) {
      document.querySelectorAll('.tagin-field .tagin').forEach(input => {
        input.value = input.dataset.value ?? null;
      });
    });

  </script>
@endsection
