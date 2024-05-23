@extends("layout.app-master")

@section("content")

  <div class="container">
    <div class="needs-validation form">
      <fieldset class="mw-500">
        <form method="POST" action="{{ route('update_website', ['website' => $website->id]); }}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <h1 class="title text-dark text-center my-5">{{ $website->company }}</h1>
          <legend>{{ __('Site & Organization Settings') }}</legend>
          <p class="lead text-muted">{{ __('Manage your account and organizations') }}</p>
          <hr>
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <div class="mb-4">
            <label for="urlInput" class="form-label">{{ __('Website Property URL') }}:</label>
            <input
                type="text"
                name="url"
                id="urlInput"
                class="form-control {{ count($errors->get("url")) > 0 ? "is-invalid": "is-valid";}}"
                placeholder="{{ __('Website') }}"
                value="{{ old("url") ?? $website->url }}">
            @if (count($errors->get("url")) > 0)
              <div class="invalid-feedback">
                @foreach ($errors->get("url") as $err)
                  {{ $err }}
                @endforeach
              </div>
            @endif
          </div>

          <div class="mb-4">
            <label for="companyInput" class="form-label">{{ __('Company/Organization Name') }}</label>
            <input type="text"
            name="company"
            id="companyInput"
            class="form-control {{ count($errors->get("company")) > 0 ? "is-invalid": "is-valid";}}"
            placeholder="{{ __('Company') }}"
            value="{{ old("company") ?? $website->company}}">
            @if (count($errors->get("company")) > 0)
              <div class="invalid-feedback">
                @foreach ($errors->get("company") as $err)
                  {{ $err }}
                @endforeach
              </div>
            @endif
          </div>

          <div class="mb-4">
            <input type="file" name="image" id="logoInput" class="d-none">
            @if(!empty($website->logo_image))
              <img id="previewLogo" src="{{ url("images/uploads/$website->logo_image") }}" alt="" style="max-width:100px;max-height:100px">
            @else
              <img id="previewLogo" class="d-none" src="" alt="" style="max-width:100px;max-height:100px">
            @endif
            <p class="lead">{{ __('It is preferable to upload your own logo in size (80 x 80)') }}</p>
            <button type="button" onclick="logoInput.click();" class="btn btn-success">{{ __('Upload Logo') }}</button>
            <button type="button" id="deleteBtn" class="btn btn-danger @if(empty($website->logo_image)) d-none @endif">{{ __('Delete Logo') }}</button>
            <a href="{{ route("website_config", $website->id) }}" class="btn btn-primary">{{ __('Config') }}</a>
          </div>

          <div class="form-check form-switch p-0 my-4 d-flex gap-3">
            <label class="form-check-label" for="branindgInput">{{ __('Platform Brand') }}:</label>
            <input class="form-check-input mx-0 float-none" disabled name="show_watermark" type="checkbox" role="switch" id="branindgInput" {{ $website->show_watermark ? "checked": ""}}>
          </div>

          <button type="submit" class="btn btn-primary mb-3 m-auto px-5">{{ __('Save') }}</button>
        </form>
        <form method="POST" action="{{ route('delete_website', ['website' => $website->id]); }}">
          {{ csrf_field() }}
          <button type="submit"  class="btn m-auto btn-danger px-5">{{ __('Delete') }} <i class="fa fa-trash"></i></button>
        </form>
      </fieldset>
    </div>
  </div>

@endsection

@section('scripts')
  <script>
    logoInput.addEventListener("change", function () {
      const [file] = this.files;

      if (file) {
        $("#previewLogo").attr("src", URL.createObjectURL(file))
      }
      $("#previewLogo").removeClass("d-none")
      $("#deleteBtn").removeClass("d-none")
    });
    $("#deleteBtn").click(function () {
      removeLogo();
    });
    function removeLogo() {
      $.ajax({
        url: "{{ route("delete_website_logo", $website->id) }}",
        method: "POST",
        success: function (res) {
          $("#logoInput").val("")
          $("#previewLogo").addClass("d-none")
          $("#deleteBtn").addClass("d-none")
        },
        error: (err) => console.error(err)
      });
    }
  </script>
@endsection
