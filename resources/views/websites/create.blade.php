@extends("layout.app-master")

@section("content")

  <div class="container">
    <form class="my-5 py-5 needs-validation" method="POST" action="{{ route('store_website'); }}">
      {{ csrf_field() }}
      <fieldset class="form-body">
        <h1 class="title text-dark text-center my-5">Create New Website</h1>
        <hr>

        <div class="mb-4">
          <label for="urlInput" class="form-label">Website Property URL:</label>
          <input
              type="text"
              name="url"
              id="urlInput"
              class="form-control
              @if (count($errors) > 0)
                {{ count($errors->get("url")) > 0 ? "is-invalid": "is-valid";}}
              @endif
              "
              value="{{ old("url") ?? "" }}"
              placeholder="Website">
          @if (count($errors->get("url")) > 0)
            <div class="invalid-feedback">
              @foreach ($errors->get("url") as $err)
                {{ $err }}
              @endforeach
            </div>
          @endif
        </div>

        <div class="mb-4">
          <label for="companyInput" class="form-label">Company Name</label>
          <input type="text"
          name="company"
          id="companyInput"
          class="form-control
            @if (count($errors) > 0)
              {{ count($errors->get("company")) > 0 ? "is-invalid": "is-valid";}}
            @endif
          "
          value="{{ old("company") ?? "" }}"
          placeholder="Company">
          @if (count($errors->get("company")) > 0)
            <div class="invalid-feedback">
              @foreach ($errors->get("company") as $err)
                {{ $err }}
              @endforeach
            </div>
          @endif
        </div>
        <button type="submit" class="btn m-auto btn-primary px-5">Create</button>
      </fieldset>
    </form>
  </div>

@endsection
