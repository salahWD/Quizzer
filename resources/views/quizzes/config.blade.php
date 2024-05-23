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
        @if (session('saved'))
          <div class="container px-5 py-4">
            <div class="general-width">
              <div class="mb-3">
                <div class="alert alert-success" style="width: fit-content">
                    {{ __('Saved successfully') }}
                </div>
              </div>
            </div>
            <hr>
          </div>
        @endif
        <div class="container px-5 py-4">
          <div class="general-width">
            <h4>{{ __('Meta Information') }}:</h4>
            <p class="lead">{{ __('Used by social networks and search engines') }}</p>
            <div class="mb-3">
              <label for="metaTitle" class="form-label">{{ __('Title') }}:</label>
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
              <label for="metaDesc" class="form-label">{{ __('Description') }}:</label>
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
                <label class="form-check-label form-label" for="isShowBrand">{{ __('Company Branding') }}:</label>
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
        <div class="container px-5 pt-4 pb-2">
          <div class="mb-3">
            <div class="general-width">
              <div class="form-check form-switch p-0 my-0 d-flex gap-3">
                <label class="form-check-label form-label" for="activate_utm">{{ __('UTM Tracking') }}:</label>
                <input class="@error('activate_utm') is-invalid @enderror form-check-input mx-0 float-none" name="activate_utm" type="checkbox" role="switch" id="activate_utm" {{ $quiz->activate_utm == 1 ? "checked": ""}}>
                @if (count($errors->get("activate_utm")) > 0)
                  <div class="invalid-feedback">
                    @foreach ($errors->get("activate_utm") as $err)
                      {{ $err }}
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          </div>
          <hr>
        </div>
        <div class="container px-5 pt-4 pb-2">
          <div class="mb-3">
            <div class="general-width">
              <div class="form-check form-switch p-0 my-0 d-flex gap-3">
                <label class="form-check-label form-label" for="step_back">{{ __('Step Back / Forward') }}:</label>
                <input class="@error('step_back') is-invalid @enderror form-check-input mx-0 float-none" name="can_go_back" type="checkbox" role="switch" id="step_back" {{ $quiz->can_go_back == 1 ? "checked": ""}}>
                @if (count($errors->get("step_back")) > 0)
                  <div class="invalid-feedback">
                    @foreach ($errors->get("step_back") as $err)
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
            <h4>{{ __('Set Privacy Policy') }}</h4>
            <p class="lead">{{ __('Because you will be collecting information, you should include a link to your company’s privacy policy') }}.</p>
            <div class="mb-3">
              <label for="policyLabel" class="form-label">{{ __('Text') }}:</label>
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
              <label for="policyLink" class="form-label">{{ __('Privacy Policy Link') }}:</label>
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
          <button type="submit" class="btn btn-primary px-4 py-2">{{ __('Save') }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
        </div>
      </div>
    </form>
    <div id="integrations-form">
      <div id="native-integrations" class="page">
        <div class="container px-5 py-4">
          <div class="general-width">
            <h4>{{ __('native integrations') }}:</h4>
            <p class="lead">{{ __("setup your quiz's native integrations") }}</p>
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
              @php
                $tagin = collect(config("integrations.INTEGRATIONS"))->keyBy("INTEGRATION");
              @endphp

              @if($tagin["activeCompaign"] != null || $tagin["getResponse"])
                {{-- Tagin Script For Active Campagin OR getResponse Fields --}}
                <script src="https://unpkg.com/tagin@2.0.2/dist/tagin.min.js"></script>
              @endif

              <div class="row">
                <div class="col-md-4">
                  <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#add-integration-modal">
                    {{ __("Add Integration") }} <i class="fa fa-plus"></i>
                  </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="add-integration-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                  <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">{{ __('Configure Native Integration') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label for="select-narive-integ" class="form-label lead">{{ __('Select Integration') }}:</label>
                          <select id="select-narive-integ" class="form-control">
                            <option value="">-- {{ __('Select Integration') }} --</option>
                            @foreach (config("integrations.INTEGRATIONS") as $integ)
                              @if($integ['IS_USABLE'])
                                @if($integ['INTEGRATION'] == 'facebook' && !empty($integs) && isset($integs['fb']) && $integs['fb'] != null && !isset($integs["fb"]['is_used']))
                                  <option value="{{ $integ['INTEGRATION'] }}-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'activeCompaign' && !empty($integs) && isset($integs['ac']) && $integs['ac'] != null && !isset($integs["ac"]['is_used']))
                                  <option value="{{ $integ['INTEGRATION'] }}-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'getResponse' && !empty($integs) && isset($integs['gr']) && $integs['gr'] != null && !isset($integs["gr"]['is_used']))
                                  <option value="{{ $integ['INTEGRATION'] }}-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'convertKit' && !empty($integs) && isset($integs['ck']) && $integs['ck'] != null && !isset($integs["ck"]['is_used']))
                                  <option value="{{ $integ['INTEGRATION'] }}-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'googleAds' && !empty($integs) && isset($integs['ga']) && $integs['ga'] != null && !isset($integs["ga"]['is_used']))
                                  <option value="google-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'tagManager' && !empty($integs) && isset($integs['tm']) && $integs['tm'] != null && !isset($integs["tm"]['is_used']))
                                  <option value="tagmanager-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'tiktok' && !empty($integs) && isset($integs['tk']) && $integs['tk'] != null && !isset($integs["tk"]['is_used']))
                                  <option value="tiktok-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'twitter' && !empty($integs) && isset($integs['tw']) && $integs['tw'] != null && !isset($integs["tw"]['is_used']))
                                  <option value="twitter-modal">{{ $integ['NAME'] }}</option>
                                @elseif($integ['INTEGRATION'] == 'snapchat' && !empty($integs) && isset($integs['sc']) && $integs['sc'] != null && !isset($integs["sc"]['is_used']))
                                  <option value="snapchat-modal">{{ $integ['NAME'] }}</option>
                                @endif
                              @endif
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
              <hr>
              <div class="row">
                @foreach (config("integrations.INTEGRATIONS") as $integ)
                  @if($integ['IS_USABLE'])

                    @if($integ['INTEGRATION'] == 'facebook' && !empty($integs) && isset($integs['fb']) && $integs['fb'] != null)

                      @php $show_or_hide_fb = (isset($integs["fb"]['is_used']) && $integs["fb"]['is_used'] == true); @endphp

                      <div class="col-md-4 mb-4" @if($show_or_hide_fb != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#facebook-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_fb", $quiz->id) }}" method="POST" id="facebook-delete">@csrf</form>
                            <button type="submit" form="facebook-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="facebook-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $integ['NAME'] }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info') }}:</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-fb" action="{{ route("update_integrations_fb", compact("quiz")) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                      <label for="custom-event" class="form-label lead">{{ __('Set custom event name') }}: <small class="text-danger">{{ __('(required)') }}</small></label>
                                      <input type="text" name="fb[fb-custom-event]" class="form-control" id="custom-event" value="{{ $integs['fb']['custom_event_name'] ?? '' }}">
                                    </div>

                                    <div class="row">
                                      <div class="col-md-8">
                                        {{ __('Content Builder Elements') }}
                                      </div>
                                      <div class="col-md-4">
                                        {{ __('Parameter value') }}
                                      </div>
                                    </div>

                                    @php
                                      $types = [__("Text Question"),__("Image Question"),__("Form Fields"),__("Text"),__("Image Or Video")];
                                    @endphp

                                    @foreach ($quiz->fb_items() as $key => $question)
                                      <div class="card mb-1 bg-light">
                                        <div class="card-body py-2">
                                          <div class="row">
                                            <div class="col-md-8">
                                              <div class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#Question-{{ $key }}" aria-expanded="false" aria-controls="Question-{{ $key }}">
                                                <p class="my-1">
                                                  <span class="badge @if($question::item_type == 1) bg-primary @else "style="background:#e1d19a @endif">@if($question::item_type == 1){{ $types[$question->type-1] }}@else Result @endif</span> {{ $question->title }}
                                                </p>
                                              </div>
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
                                                  value="{{ $question->fb_event ? $question->fb_event->event : ''}}">
                                            </div>
                                            @if ($question::item_type == 1 && $question->answers != null && count($question->answers) > 0)
                                              <div class="accordion" id="question-{{ $key }}-body">
                                                <div class="accordion-item border-0">
                                                  <div id="Question-{{ $key }}" class="accordion-collapse collapse collapsed" data-bs-parent="#question-{{ $key }}-body">
                                                    <div class="accordion-body">
                                                      @foreach($question->answers as $y => $answer)
                                                        <div class="row mb-1">
                                                          <div class="col-md-8">
                                                            <p class="my-1">
                                                              <span class="badge bg-info">Result</span> {{ $answer->text }}
                                                            </p>
                                                          </div>
                                                          <div class="col-md-4">
                                                            <input
                                                                type="hidden"
                                                                class="hidden"
                                                                name="fb[questions][{{ $key }}][answers][{{ $y }}][id]"
                                                                value="{{ $answer->id }}">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                name="fb[questions][{{ $key }}][answers][{{ $y }}][val]"
                                                                value="{{ $answer->fb_event->event ?? '' }}">
                                                          </div>
                                                        </div>
                                                      @endforeach
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            @endif
                                          </div>
                                        </div>
                                      </div>
                                    @endforeach

                                  </form>
                                </div>
                              </div>

                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                              <button type="submit" form="integrations-form-fb" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'tiktok' && !empty($integs) && isset($integs['tk']) && $integs['tk'] != null)

                      @php $show_or_hide_tk = (isset($integs["tk"]['is_used']) && $integs["tk"]['is_used'] == true); @endphp

                      <div class="col-md-4 mb-4" @if($show_or_hide_tk != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tiktok-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_tk", $quiz->id) }}" method="POST" id="tiktok-delete">@csrf</form>
                            <button type="submit" form="tiktok-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="tiktok-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $integ['NAME'] }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info') }}:</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-tk" action="{{ route("update_integrations_tk", compact("quiz")) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                      <label for="custom-event" class="form-label lead">{{ __('Set custom event name') }}: <small class="text-danger">{{ __('(required)') }}</small></label>
                                      <input type="text" name="tk[tk-custom-event]" class="form-control" id="custom-event" value="{{ $integs['tk']['custom_event_name'] ?? '' }}">
                                    </div>

                                    <div class="row">
                                      <div class="col-md-8">
                                        {{ __('Content Builder Elements') }}
                                      </div>
                                      <div class="col-md-4">
                                        {{ __('Parameter value') }}
                                      </div>
                                    </div>

                                    @php
                                      $types = [__("Text Question"),__("Image Question"),__("Form Fields"),__("Text"),__("Image Or Video")];
                                    @endphp

                                    @foreach ($quiz->tk_items() as $key => $question)
                                      <div class="card mb-1 bg-light">
                                        <div class="card-body py-2">
                                          <div class="row">
                                            <div class="col-md-8">
                                              <div class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#Question-{{ $key }}" aria-expanded="false" aria-controls="Question-{{ $key }}">
                                                <p class="my-1">
                                                  <span class="badge @if($question::item_type == 1) bg-primary @else "style="background:#e1d19a @endif">@if($question::item_type == 1){{ $types[$question->type-1] }}@else Result @endif</span> {{ $question->title }}
                                                </p>
                                              </div>
                                            </div>
                                            <div class="col-md-4">
                                              <input
                                                  type="hidden"
                                                  class="hidden"
                                                  name="tk[questions][{{ $key }}][id]"
                                                  value="{{ $question->id }}">
                                              <input
                                                  type="text"
                                                  class="form-control"
                                                  name="tk[questions][{{ $key }}][val]"
                                                  value="{{ !empty($question->tk_event) && isset($question->tk_event->event) ? $question->tk_event->event : ''}}">
                                            </div>
                                            @if ($question::item_type == 1 && $question->answers != null && count($question->answers) > 0)
                                              <div class="accordion" id="question-{{ $key }}-body">
                                                <div class="accordion-item border-0">
                                                  <div id="Question-{{ $key }}" class="accordion-collapse collapse collapsed" data-bs-parent="#question-{{ $key }}-body">
                                                    <div class="accordion-body">
                                                      @foreach($question->answers as $y => $answer)
                                                        <div class="row mb-1">
                                                          <div class="col-md-8">
                                                            <p class="my-1">
                                                              <span class="badge bg-info">Result</span> {{ $answer->text }}
                                                            </p>
                                                          </div>
                                                          <div class="col-md-4">
                                                            <input
                                                                type="hidden"
                                                                class="hidden"
                                                                name="tk[questions][{{ $key }}][answers][{{ $y }}][id]"
                                                                value="{{ $answer->id }}">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                name="tk[questions][{{ $key }}][answers][{{ $y }}][val]"
                                                                value="{{ $answer->tk_event->event ?? '' }}">
                                                          </div>
                                                        </div>
                                                      @endforeach
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            @endif
                                          </div>
                                        </div>
                                      </div>
                                    @endforeach

                                  </form>
                                </div>
                              </div>

                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                              <button type="submit" form="integrations-form-tk" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'twitter' && !empty($integs) && isset($integs['tw']) && $integs['tw'] != null)

                      @php $show_or_hide_tw = (isset($integs["tw"]['is_used']) && $integs["tw"]['is_used'] == true); @endphp

                      <div class="col-md-4 mb-4" @if($show_or_hide_tw != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#twitter-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_tw", $quiz->id) }}" method="POST" id="twitter-delete">@csrf</form>
                            <button type="submit" form="twitter-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="twitter-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $integ['NAME'] }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info') }}:</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-tw" action="{{ route("update_integrations_tw", compact("quiz")) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                      <label for="custom-event" class="form-label lead">{{ __('Set custom event name') }}:</label>
                                      <input type="text" name="tw[tw-custom-event]" class="form-control" id="custom-event" value="{{ $integs['tw']['custom_event_name'] ?? '' }}">
                                    </div>

                                    <div class="row">
                                      <div class="col-md-8">
                                        {{ __('Content Builder Elements') }}
                                      </div>
                                      <div class="col-md-4">
                                        {{ __('Parameter value') }}
                                      </div>
                                    </div>

                                    @php
                                      $types = [__("Text Question"),__("Image Question"),__("Form Fields"),__("Text"),__("Image Or Video")];
                                    @endphp

                                    @foreach ($quiz->tw_items() as $key => $question)
                                      <div class="card mb-1 bg-light">
                                        <div class="card-body py-2">
                                          <div class="row">
                                            <div class="col-md-8">
                                              <div class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#Question-{{ $key }}" aria-expanded="false" aria-controls="Question-{{ $key }}">
                                                <p class="my-1">
                                                  <span class="badge @if($question::item_type == 1) bg-primary @else "style="background:#e1d19a @endif">@if($question::item_type == 1){{ $types[$question->type-1] }}@else {{ __("Result") }} @endif</span> {{ $question->title }}
                                                </p>
                                              </div>
                                            </div>
                                            <div class="col-md-4">
                                              <input
                                                  type="hidden"
                                                  class="hidden"
                                                  name="tw[questions][{{ $key }}][id]"
                                                  value="{{ $question->id }}">
                                              <input
                                                  type="text"
                                                  class="form-control"
                                                  name="tw[questions][{{ $key }}][val]"
                                                  value="{{ !empty($question->tw_event) && isset($question->tw_event->event) ? $question->tw_event->event : ''}}">
                                            </div>
                                            @if ($question::item_type == 1 && $question->answers != null && count($question->answers) > 0)
                                              <div class="accordion" id="question-{{ $key }}-body">
                                                <div class="accordion-item border-0">
                                                  <div id="Question-{{ $key }}" class="accordion-collapse collapse collapsed" data-bs-parent="#question-{{ $key }}-body">
                                                    <div class="accordion-body">
                                                      @foreach($question->answers as $y => $answer)
                                                        <div class="row mb-1">
                                                          <div class="col-md-8">
                                                            <p class="my-1">
                                                              <span class="badge bg-info">{{ __("Answer") }}</span> {{ $answer->text }}
                                                            </p>
                                                          </div>
                                                          <div class="col-md-4">
                                                            <input
                                                                type="hidden"
                                                                class="hidden"
                                                                name="tw[questions][{{ $key }}][answers][{{ $y }}][id]"
                                                                value="{{ $answer->id }}">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                name="tw[questions][{{ $key }}][answers][{{ $y }}][val]"
                                                                value="{{ $answer->tw_event->event ?? '' }}">
                                                          </div>
                                                        </div>
                                                      @endforeach
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            @endif
                                          </div>
                                        </div>
                                      </div>
                                    @endforeach

                                  </form>
                                </div>
                              </div>

                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                              <button type="submit" form="integrations-form-tw" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'snapchat' && !empty($integs) && isset($integs['sc']) && $integs['sc'] != null)

                      @php $show_or_hide_sc = (isset($integs["sc"]['is_used']) && $integs["sc"]['is_used'] == true); @endphp

                      <div class="col-md-4 mb-4" @if($show_or_hide_sc != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#snapchat-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_sc", $quiz->id) }}" method="POST" id="snapchat-delete">@csrf</form>
                            <button type="submit" form="snapchat-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="snapchat-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $integ['NAME'] }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info') }}:</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-sc" action="{{ route("update_integrations_sc", compact("quiz")) }}" method="POST">
                                    @csrf
                                      <label for="ga_remarket" class="form-label">{{ __('Activate Pixel Integration:') }}</label>
                                      <div class="mb-3">
                                        <div class="form-check">
                                          <input
                                              name="sc_on_off"
                                              class="form-check-input"
                                              id="sc_on_off"
                                              @if(!empty($integs['sc'])) checked @endif
                                              type="checkbox">
                                          <label for="sc_on_off" class="lead">{{ __('use snapchat pixel on this quiz') }}:</label>
                                        </div>
                                      </div>
                                  </form>
                                </div>
                              </div>

                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                              <button type="submit" form="integrations-form-sc" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'activeCompaign' && !empty($integs) && isset($integs['ac']) && $integs['ac'] != null)

                      @php $show_or_hide_ac = (isset($integs["ac"]['is_used']) && $integs["ac"]['is_used'] == true); @endphp

                      <div class="col-md-4 mb-4" @if($show_or_hide_ac != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#activeCompaign-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_ac", $quiz->id) }}" method="POST" id="activeCampaign-delete">@csrf</form>
                            <button type="submit" form="activeCampaign-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="activeCompaign-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $integ['NAME'] }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info:') }}</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-ac" action="{{ route("update_integrations_ac", compact("quiz")) }}" method="POST">
                                    @csrf
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ac_fields_tab" type="button" role="tab">{{ __('Fields') }}</button>
                                      </li>
                                      <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ac_tags" type="button" role="tab">{{ __('Tags') }}</button>
                                      </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                      <div class="tab-pane active" id="ac_fields_tab" role="tabpanel">

                                        <div class="my-3">
                                          <label for="ac-lists" class="form-label lead">{{ __('Select ActiveCampaign List') }} <small class="text-danger">({{ __('required') }})</small></label>
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
                                          @if(isset($integs["ac"]['fields']) && $integs["ac"]['fields'] != null && count($integs["ac"]['fields']) > 0)
                                            @foreach($integs["ac"]['fields'] as $i => $field)
                                              <div class="d-flex tagin-field mt-2">
                                                <input type="hidden" name="ac[fields][{{$i}}][id]" value="{{ $field->id }}">
                                                <input type="hidden" name="ac[fields][{{$i}}][type]" class="type-input" @if(isset($field->value_type)) value="{{ $field->value_type ?? "" }}" @endif>
                                                <input type="text" name="ac[fields][{{$i}}][val]" class="form-control tagin" data-title="{{ $field->title }}" value="{{ old('ac.fields.'.$i.'.val') != null ? old('ac.fields.'.$i.'.val') : (isset($field->value) ? $field->value : "") }}" data-value="{{ $field->value_id ?? "" }}">
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
                                                    @if($integs["ac"]['field_values'] != null)
                                                      @foreach($integs["ac"]['field_values'] as $value)
                                                        <li class="dropdown-item tagin-value" data-value="{{ $value->ac_value_title }}" data-id="{{ $value->ac_value_id }}" data-type="{{ $value->ac_value_type }}">({{ $value_types[$value->ac_value_type] }}) {{ $value->ac_value_title }}</li>
                                                      @endforeach
                                                    @endif
                                                  </ul>
                                                </div>
                                              </div>
                                            @endforeach
                                          @endif
                                        </div>

                                      </div>
                                      <div class="tab-pane" id="ac_tags" role="tabpanel">
                                        <div class="tags">
                                          @if(isset($integs["ac"]['answers_tags']) && count($integs["ac"]['answers_tags']) > 0)
                                            @foreach($integs["ac"]['answers_tags'] as $i => $tagAnswer)
                                              @if ($tagAnswer->answers->count() > 0)
                                                <label class="form-label lead mt-3">{{ $tagAnswer->title }}:</label>
                                                @foreach($tagAnswer->answers as $x => $answer)
                                                  <div class="d-flex tagin-field mt-2">
                                                    <input type="hidden" name="ac[tags][{{$i+$x}}][answer]" value="{{ $answer->id }}">
                                                    @php
                                                      $tagname = null;
                                                      if ($answer->acanswer != null && $answer->acanswer->tag != null) {
                                                        if ($integs["ac"]['tags'] != null && count($integs["ac"]['tags']) > 0) {
                                                          foreach($integs["ac"]['tags'] as $tag) {
                                                            if ($tag->id == $answer->acanswer->tag) {
                                                              $tagname = $tag->tag;
                                                            }
                                                          }
                                                        }
                                                      }
                                                    @endphp
                                                    <input type="text" name="ac[tags][{{$i+$x}}][tag]" class="form-control tagin" data-title="{{ $answer->text }}" data-value="{{ isset($answer->acanswer) && isset($answer->acanswer->tag) ? $answer->acanswer->tag : "" }}" value="{{ $tagname }}">
                                                    <div class="no-after-before dropstart">
                                                      <button class="p-1 btn btn-light dropdown-toggle no-after-before" type="button" data-bs-toggle="dropdown">
                                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                            width="32px" height="32px" viewBox="0 0 42 42" enable-background="new 0 0 42 42" xml:space="preserve">
                                                          <g>
                                                            <defs>
                                                              <path id="SVGID_3_" d="M22,25c0,0.6-0.4,1-1,1h-6c-0.6,0-1-0.4-1-1s0.4-1,1-1h6C21.6,24,22,24.4,22,25z M10,25c0,1.1-0.9,2-2,2
                                                                s-2-0.9-2-2s0.9-2,2-2S10,23.9,10,25z M14,11c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,12,14,11.6,14,11z M10,11
                                                                c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,9.9,10,11z M10,18c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,16.9,10,18z M14,18
                                                                c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,19,14,18.6,14,18z M38,33c0,0.6-0.4,1-1,1h-3v3c0,0.6-0.4,1-1,1
                                                                s-1-0.4-1-1v-3h-3c-0.6,0-1-0.4-1-1s0.4-1,1-1h3v-3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3C37.6,32,38,32.4,38,33z M33,40
                                                                c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7S36.9,40,33,40z M3,34c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h30c0.6,0,1,0.4,1,1v21.1
                                                                c-0.3,0-0.7-0.1-1-0.1c-5,0-9,4-9,9c0,0.3,0.1,0.7,0.1,1H3z M36,24.6V3c0-1.7-1.3-3-3-3H3C1.3,0,0,1.3,0,3v30c0,1.7,1.3,3,3,3
                                                                h21.6c1.2,3.5,4.5,6,8.4,6c5,0,9-4,9-9C42,29.1,39.5,25.8,36,24.6z"/>
                                                            </defs>
                                                            <use xlink:href="#SVGID_3_"  overflow="visible" fill="#878787"/>
                                                            <clipPath id="SVGID_4_">
                                                              <use xlink:href="#SVGID_3_"  overflow="visible"/>
                                                            </clipPath>
                                                            <rect x="-5" y="-5" clip-path="url(#SVGID_4_)" fill="#878787" width="52" height="52"/>
                                                          </g>
                                                        </svg>
                                                      </button>
                                                      <ul class="dropdown-menu px-3">
                                                        @if($integs["ac"]['tags'] != null && count($integs["ac"]['tags']) > 0 && $integs["ac"]['tags'][0] != null && isset($integs["ac"]['tags'][0]->tag) && !empty($integs["ac"]['tags'][0]->tag))
                                                          @foreach($integs["ac"]['tags'] as $tag)
                                                            <li class="dropdown-item tagin-value" data-value="{{ $tag->tag }}" data-id="{{ $tag->id }}">{{ $tag->tag }}</li>
                                                          @endforeach
                                                        @endif
                                                      </ul>
                                                    </div>
                                                  </div>
                                                @endforeach
                                              @endif
                                            @endforeach
                                          @endif
                                        </div>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Cancel") }}</button>
                              <button type="submit" form="integrations-form-ac" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'convertKit' && !empty($integs) && isset($integs['ck']) && $integs['ck'] != null)

                      @php $show_or_hide_ck = (isset($integs["ck"]['is_used']) && $integs["ck"]['is_used'] == true); @endphp

                      <div class="col-md-4 mb-4" @if($show_or_hide_ck != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#convertKit-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_ck", $quiz->id) }}" method="POST" id="convertkit-delete">@csrf</form>
                            <button type="submit" form="convertkit-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="convertKit-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $integ['NAME'] }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info:') }}</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-ck" action="{{ route("update_integrations_ck", compact("quiz")) }}" method="POST">
                                    @csrf
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#ck_fields_tab" type="button" role="tab">{{ __('Fields') }}</button>
                                      </li>
                                      <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ck_tags" type="button" role="tab">{{ __('Tags') }}</button>
                                      </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                      <div class="tab-pane active" id="ck_fields_tab" role="tabpanel">

                                        <div class="my-3">
                                          <label for="ck-lists" class="form-label lead">{{ __('Select ConvertKit List') }} <small class="text-danger">({{ __('required') }})</small></label>
                                          <select class="form-select" id="ck-lists" name="ck[list]">
                                            <option>-- {{ __('select list') }} --</option>
                                            @if($integs["ck"]['ck_info'] != null && $integs["ck"]['ck_info']->courses != null)
                                              @foreach($integs["ck"]['ck_info']->courses as $integration_field)
                                                <option value="{{ $integration_field->id }}" @if(($integs["ck"]['active_list'] != null && $integs["ck"]['active_list']->pivot->value == $integration_field->id) || old('ck.list') == $integration_field->id) selected @endif>{{ $integration_field->name }}</option>
                                              @endforeach
                                            @endif
                                          </select>
                                        </div>

                                        <div id="ck_fields">
                                          @if(isset($integs["ck"]['fields']) && $integs["ck"]['fields'] != null && count($integs["ck"]['fields']) > 0)
                                            @foreach($integs["ck"]['fields'] as $i => $field)
                                              <div class="d-flex tagin-field mt-2">
                                                <input type="hidden" name="ck[fields][{{$i}}][id]" value="{{ $field->id }}">
                                                <input type="hidden" name="ck[fields][{{$i}}][type]" class="type-input" @if(isset($field->value_type)) value="{{ $field->value_type ?? "" }}" @endif>
                                                <input type="text" name="ck[fields][{{$i}}][val]" class="form-control tagin" data-title="{{ $field->label }}" value="{{ old('ck.fields.'.$i.'.val') != null ? old('ck.fields.'.$i.'.val') : (isset($field->value) ? $field->value : "") }}" data-value="{{ $field->value_id ?? "" }}">
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
                                                    @if($integs["ck"]['field_values'] != null)
                                                      @foreach($integs["ck"]['field_values'] as $value)
                                                        <li class="dropdown-item tagin-value" data-value="{{ $value->ck_value_title }}" data-id="{{ $value->ck_value_id }}" data-type="{{ $value->ck_value_type }}">({{ $value_types[$value->ck_value_type] }}) {{ $value->ck_value_title }}</li>
                                                      @endforeach
                                                    @endif
                                                  </ul>
                                                </div>
                                              </div>
                                            @endforeach
                                          @endif
                                        </div>

                                      </div>
                                      <div class="tab-pane" id="ck_tags" role="tabpanel">
                                        <div class="tags">
                                          @if(isset($integs["ck"]['answers_tags']) && count($integs["ck"]['answers_tags']) > 0)
                                            @foreach($integs["ck"]['answers_tags'] as $i => $tagAnswer)
                                              @if ($tagAnswer->answers->count() > 0)
                                                <label class="form-label lead mt-3">{{ $tagAnswer->title }}:</label>
                                                @foreach($tagAnswer->answers as $x => $answer)
                                                  <div class="d-flex tagin-field mt-2">
                                                    <input type="hidden" name="ck[tags][{{$i+$x}}][answer]" value="{{ $answer->id }}">
                                                    @php
                                                      $tagname = null;
                                                      if ($answer->ckanswer != null && $answer->ckanswer->tag != null) {
                                                        if ($integs["ck"]['tags'] != null && count($integs["ck"]['tags']) > 0) {
                                                          foreach($integs["ck"]['tags'] as $tag) {
                                                            if ($tag->id == $answer->ckanswer->tag) {
                                                              $tagname = $tag->name;
                                                            }
                                                          }
                                                        }
                                                      }
                                                    @endphp
                                                    <input type="text" name="ck[tags][{{$i+$x}}][tag]" class="form-control tagin" data-title="{{ $answer->text }}" data-value="{{ isset($answer->ckanswer) && isset($answer->ckanswer->tag) ? $answer->ckanswer->tag : "" }}" value="{{ $tagname }}">
                                                    <div class="no-after-before dropstart">
                                                      <button class="p-1 btn btn-light dropdown-toggle no-after-before" type="button" data-bs-toggle="dropdown">
                                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                            width="32px" height="32px" viewBox="0 0 42 42" enable-background="new 0 0 42 42" xml:space="preserve">
                                                          <g>
                                                            <defs>
                                                              <path id="SVGID_3_" d="M22,25c0,0.6-0.4,1-1,1h-6c-0.6,0-1-0.4-1-1s0.4-1,1-1h6C21.6,24,22,24.4,22,25z M10,25c0,1.1-0.9,2-2,2
                                                                s-2-0.9-2-2s0.9-2,2-2S10,23.9,10,25z M14,11c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,12,14,11.6,14,11z M10,11
                                                                c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,9.9,10,11z M10,18c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,16.9,10,18z M14,18
                                                                c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,19,14,18.6,14,18z M38,33c0,0.6-0.4,1-1,1h-3v3c0,0.6-0.4,1-1,1
                                                                s-1-0.4-1-1v-3h-3c-0.6,0-1-0.4-1-1s0.4-1,1-1h3v-3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3C37.6,32,38,32.4,38,33z M33,40
                                                                c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7S36.9,40,33,40z M3,34c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h30c0.6,0,1,0.4,1,1v21.1
                                                                c-0.3,0-0.7-0.1-1-0.1c-5,0-9,4-9,9c0,0.3,0.1,0.7,0.1,1H3z M36,24.6V3c0-1.7-1.3-3-3-3H3C1.3,0,0,1.3,0,3v30c0,1.7,1.3,3,3,3
                                                                h21.6c1.2,3.5,4.5,6,8.4,6c5,0,9-4,9-9C42,29.1,39.5,25.8,36,24.6z"/>
                                                            </defs>
                                                            <use xlink:href="#SVGID_3_"  overflow="visible" fill="#878787"/>
                                                            <clipPath id="SVGID_4_">
                                                              <use xlink:href="#SVGID_3_"  overflow="visible"/>
                                                            </clipPath>
                                                            <rect x="-5" y="-5" clip-path="url(#SVGID_4_)" fill="#878787" width="52" height="52"/>
                                                          </g>
                                                        </svg>
                                                      </button>
                                                      <ul class="dropdown-menu px-3">
                                                        @if($integs["ck"]['tags'] != null && !empty($integs["ck"]['tags']) && $integs["ck"]['tags']->tags != null && !empty($integs["ck"]['tags']->tags))
                                                          @foreach($integs["ck"]["tags"]->tags as $tag)
                                                            <li class="dropdown-item tagin-value" data-value="{{ isset($tag->name) ? $tag->name: "" }}" data-id="{{ $tag->id }}">{{ isset($tag->name) ? $tag->name: "" }}</li>
                                                          @endforeach
                                                        @endif
                                                      </ul>
                                                    </div>
                                                  </div>
                                                @endforeach
                                              @endif
                                            @endforeach
                                          @endif
                                        </div>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Cancel") }}</button>
                              <button type="submit" form="integrations-form-ck" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'getResponse' && !empty($integs) && isset($integs['gr']) && $integs['gr'] != null)
                      @php $show_or_hide_gr = (isset($integs["gr"]['is_used']) && $integs["gr"]['is_used'] == true); @endphp
                      <div class="col-md-4 mb-4" @if($show_or_hide_gr != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getResponse-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_gr", $quiz->id) }}" method="POST" id="getresponse-delete">@csrf</form>
                            <button type="submit" form="getresponse-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="getResponse-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">{{ $integ['NAME'] }}</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info:') }}</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-gr" action="{{ route("update_integrations_gr", compact("quiz")) }}" method="POST">
                                    @csrf
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" id="getResponseTab" role="tablist">
                                      <li class="nav-item" role="presentation">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#gr_fields" type="button" role="tab">{{ __('Fields') }}</button>
                                      </li>
                                      <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#gr_tags" type="button" role="tab">{{ __('Tags') }}</button>
                                      </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                      <div class="tab-pane active" id="gr_fields" role="tabpanel">
                                        <div class="my-3">
                                          <label for="gr-lists" class="form-label lead">{{ __('Select getResponse List') }} <small class="text-danger">({{ __('required') }})</small></label>
                                          <select class="form-select" id="gr-lists" name="gr[list]">
                                            <option>-- select list --</option>
                                            @if(isset($integs["gr"]['gr_info']) && $integs["gr"]['gr_info'] != null)
                                              @foreach($integs["gr"]['gr_info'] as $integration_field)
                                                <option value="{{ $integration_field->campaignId }}" @if(($integs["gr"]['active_list'] != null && $integs["gr"]['active_list']->pivot->value == $integration_field->campaignId) || old('gr.list') == $integration_field->campaignId) selected @endif>{{ $integration_field->name }}</option>
                                              @endforeach
                                            @endif
                                          </select>
                                        </div>

                                        <div id="gr_fields">
                                          @if(isset($integs["gr"]['fields']) && count($integs["gr"]['fields']) > 0)
                                            @foreach($integs["gr"]['fields'] as $i => $field)
                                              @if(!isset($field->hidden) || (isset($field->hidden) && $field->hidden == "false" && empty($field->values)))
                                                <div class="d-flex tagin-field mt-2">
                                                  <input type="hidden" name="gr[fields][{{$i}}][id]" value="{{ $field->customFieldId }}">
                                                  <input type="hidden" name="gr[fields][{{$i}}][type]" class="type-input" @if(isset($field->value_type)) value="{{ $field->value_type ?? "" }}" @endif>
                                                  <input type="text" name="gr[fields][{{$i}}][val]" class="form-control tagin" data-title="{{ $field->name }}" value="{{ old('gr.fields.'.$i.'.val') != null ? old('gr.fields.'.$i.'.val') : (isset($field->value) ? $field->value : "") }}" data-value="{{ $field->value_id ?? "" }}">
                                                  <div class="no-after-before dropstart">
                                                    <button class="p-1 btn btn-light dropdown-toggle no-after-before" type="button" data-bs-toggle="dropdown">
                                                      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                          width="32px" height="32px" viewBox="0 0 42 42" enable-background="new 0 0 42 42" xml:space="preserve">
                                                        <g>
                                                          <defs>
                                                            <path id="SVGID_5_" d="M22,25c0,0.6-0.4,1-1,1h-6c-0.6,0-1-0.4-1-1s0.4-1,1-1h6C21.6,24,22,24.4,22,25z M10,25c0,1.1-0.9,2-2,2
                                                              s-2-0.9-2-2s0.9-2,2-2S10,23.9,10,25z M14,11c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,12,14,11.6,14,11z M10,11
                                                              c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,9.9,10,11z M10,18c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,16.9,10,18z M14,18
                                                              c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,19,14,18.6,14,18z M38,33c0,0.6-0.4,1-1,1h-3v3c0,0.6-0.4,1-1,1
                                                              s-1-0.4-1-1v-3h-3c-0.6,0-1-0.4-1-1s0.4-1,1-1h3v-3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3C37.6,32,38,32.4,38,33z M33,40
                                                              c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7S36.9,40,33,40z M3,34c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h30c0.6,0,1,0.4,1,1v21.1
                                                              c-0.3,0-0.7-0.1-1-0.1c-5,0-9,4-9,9c0,0.3,0.1,0.7,0.1,1H3z M36,24.6V3c0-1.7-1.3-3-3-3H3C1.3,0,0,1.3,0,3v30c0,1.7,1.3,3,3,3
                                                              h21.6c1.2,3.5,4.5,6,8.4,6c5,0,9-4,9-9C42,29.1,39.5,25.8,36,24.6z"/>
                                                          </defs>
                                                          <use xlink:href="#SVGID_5_"  overflow="visible" fill="#878787"/>
                                                          <clipPath id="SVGID_6_">
                                                            <use xlink:href="#SVGID_5_"  overflow="visible"/>
                                                          </clipPath>
                                                          <rect x="-5" y="-5" clip-path="url(#SVGID_6_)" fill="#878787" width="52" height="52"/>
                                                        </g>
                                                      </svg>
                                                    </button>
                                                    <ul class="dropdown-menu px-3">
                                                      @php $value_types = ['Quiz Name', 'Content Item', 'Content Result', 'From Field', 'date']; @endphp
                                                      @if($integs["gr"]['field_values'] != null)
                                                        @foreach($integs["gr"]['field_values'] as $value)
                                                          <li class="dropdown-item tagin-value" data-value="{{ $value->gr_value_title }}" data-id="{{ $value->gr_value_id }}" data-type="{{ $value->gr_value_type }}">({{ $value_types[$value->gr_value_type] }}) {{ $value->gr_value_title }}</li>
                                                        @endforeach
                                                      @endif
                                                    </ul>
                                                  </div>
                                                </div>
                                              @endif
                                            @endforeach
                                          @endif
                                        </div>

                                      </div>
                                      <div class="tab-pane" id="gr_tags" role="tabpanel">
                                        <div class="tags">
                                          @if(isset($integs["gr"]['answers_tags']) && is_array($integs["gr"]['answers_tags']) && count($integs["gr"]['answers_tags']) > 0)
                                            @foreach($integs["gr"]['answers_tags'] as $i => $tagAnswer)
                                              @if ($tagAnswer->answers->count() > 0)
                                                <label class="form-label lead mt-3">{{ $tagAnswer->title }}:</label>
                                                  @foreach($tagAnswer->answers as $x => $answer)
                                                    <div class="d-flex tagin-field mt-2">
                                                      <input type="hidden" name="gr[tags][{{$i+$x}}][answer]" value="{{ $answer->id }}">
                                                      @php
                                                        $tagname = null;
                                                        if ($answer->granswer != null && $answer->granswer->tag != null) {
                                                          foreach($integs["gr"]['tags'] as $tag) {
                                                            if ($tag->tagId == $answer->granswer->tag) {
                                                              $tagname = $tag->name;
                                                            }
                                                          }
                                                        }
                                                      @endphp
                                                      <input type="text" name="gr[tags][{{$i+$x}}][tag]" class="form-control tagin" data-title="{{ $answer->text }}" data-value="{{ isset($answer->granswer) && isset($answer->granswer->tag) ? $answer->granswer->tag : "" }}" value="{{ $tagname }}">
                                                      <div class="no-after-before dropstart">
                                                        <button class="p-1 btn btn-light dropdown-toggle no-after-before" type="button" data-bs-toggle="dropdown">
                                                          <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                              width="32px" height="32px" viewBox="0 0 42 42" enable-background="new 0 0 42 42" xml:space="preserve">
                                                            <g>
                                                              <defs>
                                                                <path id="SVGID_7_" d="M22,25c0,0.6-0.4,1-1,1h-6c-0.6,0-1-0.4-1-1s0.4-1,1-1h6C21.6,24,22,24.4,22,25z M10,25c0,1.1-0.9,2-2,2
                                                                  s-2-0.9-2-2s0.9-2,2-2S10,23.9,10,25z M14,11c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,12,14,11.6,14,11z M10,11
                                                                  c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,9.9,10,11z M10,18c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,16.9,10,18z M14,18
                                                                  c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,19,14,18.6,14,18z M38,33c0,0.6-0.4,1-1,1h-3v3c0,0.6-0.4,1-1,1
                                                                  s-1-0.4-1-1v-3h-3c-0.6,0-1-0.4-1-1s0.4-1,1-1h3v-3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3C37.6,32,38,32.4,38,33z M33,40
                                                                  c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7S36.9,40,33,40z M3,34c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h30c0.6,0,1,0.4,1,1v21.1
                                                                  c-0.3,0-0.7-0.1-1-0.1c-5,0-9,4-9,9c0,0.3,0.1,0.7,0.1,1H3z M36,24.6V3c0-1.7-1.3-3-3-3H3C1.3,0,0,1.3,0,3v30c0,1.7,1.3,3,3,3
                                                                  h21.6c1.2,3.5,4.5,6,8.4,6c5,0,9-4,9-9C42,29.1,39.5,25.8,36,24.6z"/>
                                                              </defs>
                                                              <use xlink:href="#SVGID_7_"  overflow="visible" fill="#878787"/>
                                                              <clipPath id="SVGID_8_">
                                                                <use xlink:href="#SVGID_7_"  overflow="visible"/>
                                                              </clipPath>
                                                              <rect x="-5" y="-5" clip-path="url(#SVGID_8_)" fill="#878787" width="52" height="52"/>
                                                            </g>
                                                          </svg>
                                                        </button>
                                                        <ul class="dropdown-menu px-3">
                                                          @foreach($integs["gr"]['tags'] as $tag)
                                                            <li class="dropdown-item tagin-value" data-value="{{ $tag->name }}" data-id="{{ $tag->tagId }}">{{ $tag->name }}</li>
                                                          @endforeach
                                                        </ul>
                                                      </div>
                                                    </div>
                                                  @endforeach
                                                @endif
                                            @endforeach
                                          @endif
                                        </div>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                              </div>

                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Cancel") }}</button>
                              <button type="submit" form="integrations-form-gr" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'googleAds' && !empty($integs) && isset($integs['ga']) && $integs['ga'] != null)

                      @php $show_or_hide_ga = (isset($integs["ga"]['is_used']) && $integs["ga"]['is_used'] == true); @endphp

                      <div class="col-md-4 mb-4" @if($show_or_hide_ga != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#google-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_ga", $quiz->id) }}" method="POST" id="google-delete">@csrf</form>
                            <button type="submit" form="google-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="google-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Modal title</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info:') }}</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-ga" action="{{ route("update_integrations_ga", compact("quiz")) }}" method="POST">
                                    @csrf
                                      <label for="ga_remarket" class="form-label">{{ __('On Content Load') }}:</label>
                                      <div class="mb-3">
                                        <label for="ga_start_remarket" class="lead">{{ __('Remarketing Tag') }}:</label>
                                        <textarea
                                            name="ga_start_remarket"
                                            placeholder="{{ __('Paste in the gtag() Event Snippet') }}"
                                            class="form-control"
                                            id="ga_start_remarket">{{ isset($integs["ga"]) ? $integs["ga"]['start_remarket'] : "" }}</textarea>
                                      </div>
                                      <label for="ga_remarket" class="form-label">{{ __('On Content Finish') }}:</label>
                                      <div class="mb-3">
                                        <label for="ga_finish_remarket" class="lead">{{ __('Remarketing Tag') }}:</label>
                                        <textarea
                                            name="ga_finish_remarket"
                                            placeholder="{{ __('Paste in the gtag() Event Snippet') }}"
                                            class="form-control"
                                            id="ga_finish_remarket">{{ isset($integs["ga"]) ? $integs["ga"]['finish_remarket'] : "" }}</textarea>
                                      </div>
                                      <div class="mb-3">
                                        <label for="ga_finish_conversion" class="lead">Conversion Tag:</label>
                                        <textarea
                                            name="ga_finish_conversion"
                                            placeholder="{{ __('Paste in the gtag() Event Snippet') }}"
                                            class="form-control"
                                            id="ga_finish_conversion">{{ isset($integs["ga"]) ? $integs["ga"]['finish_conversion'] : "" }}</textarea>
                                      </div>

                                      @if(isset($integs["ga"]['content_items']) && $integs["ga"]['content_items'] != null)

                                        <div class="accordion" id="ga-items">
                                          @foreach($integs["ga"]['content_items'] as $i => $item)
                                            <div class="accordion-item">
                                              <h2 class="accordion-header" id="heading{{$i}}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseItem{{$i}}" aria-expanded="false" aria-controls="collapseItem{{$i}}">
                                                  <span class="badge mx-2 @if($item->ga_type == 1) bg-primary" @else " style="background: #e1d19a" @endif>
                                                    {{ $item->ga_type == 1 ? "Content" : "Result" }}
                                                  </span>
                                                  {{ $item->ga_title }}
                                                </button>
                                              </h2>
                                              <div id="collapseItem{{$i}}" class="accordion-collapse collapse collapsed" labelledby="heading{{$i}}" data-bs-parent="#ga-items">
                                                <div class="accordion-body">
                                                  <input type="hidden" name="ga_items[{{$i}}][item_id]" value="{{ $item->ga_id }}">
                                                  <input type="hidden" name="ga_items[{{$i}}][item_type]" value="{{ $item->ga_type }}">
                                                  <textarea
                                                      class="form-control mb-2"
                                                      name="ga_items[{{$i}}][remarket]"
                                                      placeholder="{{ __('Paste in the gtag() Event Snippet') }}">{{ $item->marketing_tag ?? "" }}</textarea>
                                                  <textarea
                                                      class="form-control"
                                                      name="ga_items[{{$i}}][conversion]"
                                                      placeholder="{{ __('Paste in the gtag() Event Snippet') }}">{{ $item->conversion_tag ?? "" }}</textarea>
                                                </div>
                                              </div>
                                            </div>
                                          @endforeach
                                        </div>
                                      @endif
                                  </form>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                              <button type="submit" form="integrations-form-ga" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @elseif($integ['INTEGRATION'] == 'tagManager' && !empty($integs) && isset($integs['tm']) && $integs['tm'] != null)

                      @php $show_or_hide_tm = (isset($integs["tm"]['is_used']) && $integs["tm"]['is_used'] == true); @endphp
                      <div class="col-md-4 mb-4" @if($show_or_hide_tm != true) style="display:none !important" @endif>
                        <div class="card">
                          <div class="card-body text-center">
                            <h5 class="card-title mb-3">{{ __($integ['NAME']) }}</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tagmanager-modal">
                              {{ __("Use") }}
                            </button>
                            <form class="d-none" action="{{ route("delete_integrations_tm", $quiz->id) }}" method="POST" id="tagmanager-delete">@csrf</form>
                            <button type="submit" form="tagmanager-delete" class="btn btn-danger">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Modal -->
                      <div class="modal fade" id="tagmanager-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Modal title</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-4">
                                  <h3 class="title">{{ __('info:') }}</h3>
                                </div>
                                <div class="col-md-8">
                                  <form id="integrations-form-tm" action="{{ route("update_integrations_tm", compact("quiz")) }}" method="POST">
                                    @csrf
                                      <label for="ga_remarket" class="form-label">{{ __('include google tags') }}:</label>
                                      <div class="mb-3">
                                        <div class="form-check">
                                          <input
                                              name="tm_on_off"
                                              class="form-check-input"
                                              id="tm_on_off"
                                              @if(!empty($integs['tm'])) checked @endif
                                              type="checkbox">
                                          <label for="tm_on_off" class="lead">{{ __('use Tag manager on this quiz') }}:</label>
                                        </div>
                                      </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                              <button type="submit" form="integrations-form-tm" class="btn btn-primary px-4 py-2">{{ __("Save") }} <i style="margin-left: 8px" class="fa fa-save"></i></button>
                            </div>
                          </div>
                        </div>
                      </div>

                    @endif

                  @endif
                @endforeach
              </div>

              @if($tagin["activeCompaign"] != null || $tagin["getResponse"])
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

          @endif
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
            {{ __('General') }}
            <p class="lead mb-0">{{ __("basic setting") }}</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-page="native-integrations">
            {{ __("Native Integrations") }}
            <p class="lead mb-0">{{ __("integrate your quizzes using native integrations") }}</p>
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

    activePage("general");

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
                            <path id="SVGID_9_" d="M22,25c0,0.6-0.4,1-1,1h-6c-0.6,0-1-0.4-1-1s0.4-1,1-1h6C21.6,24,22,24.4,22,25z M10,25c0,1.1-0.9,2-2,2
                              s-2-0.9-2-2s0.9-2,2-2S10,23.9,10,25z M14,11c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,12,14,11.6,14,11z M10,11
                              c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,9.9,10,11z M10,18c0,1.1-0.9,2-2,2s-2-0.9-2-2s0.9-2,2-2S10,16.9,10,18z M14,18
                              c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1s-0.4,1-1,1H15C14.4,19,14,18.6,14,18z M38,33c0,0.6-0.4,1-1,1h-3v3c0,0.6-0.4,1-1,1
                              s-1-0.4-1-1v-3h-3c-0.6,0-1-0.4-1-1s0.4-1,1-1h3v-3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3C37.6,32,38,32.4,38,33z M33,40
                              c-3.9,0-7-3.1-7-7s3.1-7,7-7s7,3.1,7,7S36.9,40,33,40z M3,34c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h30c0.6,0,1,0.4,1,1v21.1
                              c-0.3,0-0.7-0.1-1-0.1c-5,0-9,4-9,9c0,0.3,0.1,0.7,0.1,1H3z M36,24.6V3c0-1.7-1.3-3-3-3H3C1.3,0,0,1.3,0,3v30c0,1.7,1.3,3,3,3
                              h21.6c1.2,3.5,4.5,6,8.4,6c5,0,9-4,9-9C42,29.1,39.5,25.8,36,24.6z"/>
                          </defs>
                          <use xlink:href="#SVGID_9_"  overflow="visible" fill="#878787"/>
                          <clipPath id="SVGID_10_">
                            <use xlink:href="#SVGID_9_"  overflow="visible"/>
                          </clipPath>
                          <rect x="-5" y="-5" clip-path="url(#SVGID_10_)" fill="#878787" width="52" height="52"/>
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

    $("form").on('submit', function (e) {
      document.querySelectorAll('.tagin-field .tagin').forEach(input => {
        input.value = input.dataset.value ?? null;
      });
    });

    $("#select-narive-integ").on('input', function() {
      $("#add-integration-modal").modal('hide');
      $("#" + $(this).val()).modal('show');
    });

  </script>
@endsection
