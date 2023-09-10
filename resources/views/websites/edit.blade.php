@extends("layout.app-master")

@section("content")

  <div class="container">
    <div class="needs-validation form">
      <fieldset class="mw-500">
        <form method="POST" action="{{ route('update_website', ['website' => $website->id]); }}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <h1 class="title text-dark text-center my-5">{{ $website->company }}</h1>
          <legend>Site & Organization Settings</legend>
          <p class="lead text-muted">Manage your website and organizations</p>
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
            <label for="urlInput" class="form-label">Website Property URL:</label>
            <input
                type="text"
                name="url"
                id="urlInput"
                class="form-control {{ count($errors->get("url")) > 0 ? "is-invalid": "is-valid";}}"
                placeholder="Website"
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
            <label for="companyInput" class="form-label">Company/Organization Name</label>
            <input type="text"
            name="company"
            id="companyInput"
            class="form-control {{ count($errors->get("company")) > 0 ? "is-invalid": "is-valid";}}"
            placeholder="Company"
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
            <button type="button" onclick="logoInput.click();" class="btn btn-success">Upload Logo</button>
            <a href="{{ route("website_config", $website->id) }}" class="btn btn-primary">Config</a>
          </div>

          <div class="form-check form-switch p-0 my-4 d-flex gap-3">
            <label class="form-check-label" for="branindgInput">{{ getenv("APP_NAME", true) }} Branding:</label>
            <input class="form-check-input mx-0 float-none" name="show_watermark" type="checkbox" role="switch" id="branindgInput" {{ $website->show_watermark ? "checked": ""}}>
          </div>

          <button type="submit" class="btn btn-primary mb-3 m-auto px-5">Save</button>
        </form>
        <form method="POST" action="{{ route('delete_website', ['website' => $website->id]); }}">
          {{ csrf_field() }}
          <button type="submit"  class="btn m-auto btn-danger px-5">Delete <i class="fa fa-trash"></i></button>
        </form>
      </fieldset>
    </div>
  </div>

@endsection
