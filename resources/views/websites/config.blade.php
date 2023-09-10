@extends("layout.app-master")

@section("styles")
<style>
  .mw-750 {
    width: 700px;
    margin: auto;
    margin-top: 40px;
  }
</style>
@endsection

@section("content")

  <div class="container">
    <div class="needs-validation form">
      <fieldset class="mw-750">
        <form method="POST" action="{{ route('website_update_config', ['website' => $website->id]) }}">
          @csrf

          @if(!auth()->user()->can_custom_domain())
            <div class="mb-3">
              <label class="form-label" for="custom_domain">Custom Domain</label>
              <input id="custom_domain" name="custom_domain" class="form-control" type="text" value="{{ $website->custom_domain }}" placeholder="enter: custom domain">
            </div>
          @endif

          <small class="text-muted">Available Integrations:</small>

          <div class="integrations mt-4">
            @foreach (config("integrations.INTEGRATIONS") as $integ)
              <div class="mb-2">
                <div class="bg-white border p-3">
                  <div class="d-flex">
                    <img src="{{ $integ["LOGO"] }}" alt="" class="img">
                    <div class="box mx-2">
                      <h5 class="title m-0">{{ $integ["NAME"] }}</h5>
                      <small class="">{{ $integ["DESC"] }}</small>
                    </div>
                    <div class="align-self-center" style="margin-left: auto">
                      <button type="button" data-bs-toggle="modal" data-bs-target="#integration-{{ $integ["ID"] }}" class="btn btn-primary">Set Up</button>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Config Modal --}}

              <div class="modal fade" id="integration-{{ $integ["ID"] }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">integrate {{ $integ["NAME"] }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      @foreach ($integ["FIELDS"] as $key => $field)
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
                      @endforeach
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancel-integration-{{ $integ['ID'] }}">Close</button>
                      <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                    </div>
                  </div>
                </div>
              </div>

            @endforeach
          </div>

          <button type="submit" class="btn btn-primary mb-3 m-auto px-5">Save</button>
        </form>
      </fieldset>
    </div>
  </div>

@endsection

@section("scripts")
  <script>
    window.onload = function () {

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

