@extends("layout.app-master")

@section("styles")
<style>
  .mw-750 {
    width: 700px;
    margin: auto;
    margin-top: 40px;
  }
  .img {
    max-width: 55px;
    object-fit: cover;
    object-position: center center;
  }
  .nav-item {
    position: relative;
  }
  .modal-body .nav-item .nav-link {
    color: inherit;
    border-bottom: 1px solid #ddd;
    padding-bottom: 15px;
  }
  .nav-item .nav-link.active::before {
    content: "\F105";
    position: absolute;
    right: 15px;
    top: 0;
    bottom: 0;
    height: 22px;
    margin: auto;
    font: normal normal normal 14px/1 FontAwesome;
    color: #016fdf;
    font-size: 22px;
    font-weight: 700;
  }
  .sidebar .page {
    border: none;
  }
  .sidebar .page .active {
    border-bottom: 2px solid rgba(13, 110, 253, 1);
  }
  .nav-item.active {
    color: white;
  }
  .nav-item.active a {
    color: inherit;
  }
  html[lang="ar"] .nav-item .nav-link.active::before {
    left: 15px;
    right: unset;
  }
</style>
@endsection

@section("content")

  <div class="container">
    <div class="needs-validation form">
      <fieldset class="mw-750">
        <form method="POST" action="{{ route('website_update_config', ['website' => $website->id]) }}">
          @csrf

          @if(session()->has('success') && session('success') == true)
            <p class="alert alert-success">
              {{ __('Success! the Integrations has been saved') }}
            </p>
          @endif

          <small class="text-muted">{{ __('Available Integrations') }}:</small>

          <div class="integrations mt-4">
            @foreach (config("integrations.INTEGRATIONS") as $integ_key => $integ)
              <div class="one-integration">
                <div class="mb-2">
                  <div class="bg-white border p-3">
                    <div class="d-flex">
                      <img src="{{ $integ["LOGO"] }}" alt="" class="img">
                      <div class="box mx-2">
                        <h5 class="title m-0">{{ __($integ["NAME"]) }}</h5>
                        <small class="">{{ __($integ["DESC"]) }}</small>
                      </div>
                      <div class="align-self-center ms-auto">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#integration-{{ $integ["ID"] }}" class="btn btn-primary">{{ __('Set Up') }}</button>
                        @php
                        $integval = $website->get_integration($integ["INTEGRATION"]);
                        @endphp
                        @if ($website->has_integration($integ["INTEGRATION"]) && (!empty($integval->email) || !empty($integval->key) || !empty($integval->value)))
                          <button type="submit" class="btn delete-integ btn-danger">{{ __('Delete') }}</button>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Config Modal --}}

                <div class="modal fade" id="integration-{{ $integ["ID"] }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">integrate {{ $integ["NAME"] }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-4">

                            <ul class="nav config-options config-pages flex-column">
                              @if(isset($integ["FIELDS"]) && count($integ["FIELDS"]) > 0)
                                @foreach ($integ["FIELDS"] as $i => $field)
                                  <li class="nav-item">
                                    <a class="nav-link @if($i == 0) active @endif" data-page="{{$integ_key}}-page-{{$i}}">
                                      {{ __($field["NAME"]) }}
                                    </a>
                                  </li>
                                @endforeach
                              @endif
                            </ul>

                          </div>
                          <div class="col-8">
                            @if(isset($integ["FIELDS"]) && count($integ["FIELDS"]) > 0)
                              @foreach ($integ["FIELDS"] as $key => $field)
                                <div class="page" id="{{$integ_key}}-page-{{$key}}" @if($key != 0) style="display: none" @endif>
                                  <label class="form-label" for="{{ $field["NAME"] }}-{{ $integ["ID"] }}">{{ $field["NAME"] }}</label>
                                  @if($field["INPUT"][0] == "textarea")
                                    <textarea class="form-control" style="min-height: 330px;" name="{{$integ["INTEGRATION"]}}_{{$integ["ID"]}}_{{$key}}" id="{{ $field["NAME"] }}-{{ $integ["ID"] }}" placeholder="{{ $field["INPUT"][1] }}">{{ $website->has_integration($integ["INTEGRATION"]) ? $website->get_integration($integ["INTEGRATION"])[$field["INPUT"][2]] : "" }}</textarea>
                                  @else
                                    <input
                                        class="form-control"
                                        id="{{ $field["NAME"] }}-{{ $integ["ID"] }}"
                                        type="{{ $field["INPUT"][0] }}"
                                        placeholder="{{ $field["INPUT"][1] }}"
                                        name="{{$integ["INTEGRATION"]}}_{{$integ["ID"]}}_{{$key}}"
                                        value="{{ $website->has_integration($integ["INTEGRATION"]) ? $website->get_integration($integ["INTEGRATION"])[$field["INPUT"][2]] : "" }}">
                                  @endif
                                </div>
                              @endforeach
                            @elseif(isset($integ["BTN_URL"]) && $integ["BTN_URL"] != null)
                              <a href="{{ $integ["BTN_URL"] }}" class="btn">{{ $integ["BTN_TEXT"] }}</a>
                            @endif
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancel-integration-{{ $integ['ID'] }}">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            @endforeach
          </div>
        </form>
      </fieldset>
    </div>
  </div>

@endsection

@section("scripts")
  <script>
    function activePage(id) {
      $(".config-pages").find(".nav-link[data-page=\"" + id + "\"]").addClass("active").parent().siblings(".nav-item").find(".nav-link").removeClass("active");
      $("#" + id).parent().find('.page').hide();
      $("#" + id).fadeIn();
    }

    window.onload = function () {

      $(".delete-integ").on("click", function (e) {
        $(this).parents(".one-integration").find("input, textarea").each(function () {
          $(this).val("");
        })
      })

      $(".config-pages").find(".nav-link[data-page]").each(function () {
        $(this).click(function () {
          activePage($(this).data("page"));
        });
      });

      @foreach (config("integrations.INTEGRATIONS") as $integ)

        var modal{{$integ["ID"]}} = document.getElementById('integration-{{ $integ["ID"] }}');

        modal{{$integ["ID"]}}.addEventListener('show.bs.modal', function (event) {

          var button = event.relatedTarget;
          var recipient = button.getAttribute('data-bs-whatever');

          modal{{$integ["ID"]}}.querySelectorAll('.modal-body textarea, .modal-body input').forEach(input => {
            input.dataset.value = input.value;
          });

          let cancelInteg{{$integ["ID"]}}Btn = document.getElementById("cancel-integration-{{ $integ['ID'] }}");

          cancelInteg{{$integ["ID"]}}Btn.addEventListener("click", function() {
            modal{{$integ["ID"]}}.querySelectorAll('.modal-body textarea, .modal-body input').forEach(input => {
              input.value = input.dataset.value;
            });
          });

        });

      @endforeach

    }
  </script>
@endsection

