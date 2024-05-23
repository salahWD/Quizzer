@extends('layout.master')

@section("styles")
<style>
  .card-body .title {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    font-size: 1.2rem;
    height: 3.1rem;
    text-overflow: ellipsis;
    overflow: hidden;
  }
</style>
@endsection

@section("content")

  <div class="container">
    @if(!auth()->check())
      @if (count($errors->all()) > 0)
        <div class="invalid-feedback">
          @foreach ($errors->all() as $err)
            {{ $err }}
          @endforeach
        </div>
      @endif
      @include('templates.templates-show-none-user')
    @else
      <div class="row my-3">
        <div class="col-2"></div>
        <div class="col-5">
          <input id="search-by-name" type="search" class="form-control" placeholder="{{ __("search by name") }}">
        </div>
        <div class="col-4">
          <div class="d-flex gap-3">
            @if (auth()->check())
              @if (auth()->user()->is_admin())
                <a href="{{ route('create_template') }}" class="btn btn-primary">{{ __("Create Template") }}</a>
              @endif
              @if (isset($website) && !empty($website))
                <a href="{{ route('create_quiz', $website->id) }}" class="btn btn-primary">{{ __("Create Ai Salse Rep") }}</a>
              @endif
            @endif
          </div>
        </div>
        <div class="col-1"></div>
      </div>

      @if (count($errors->all()) > 0)
        <div class="invalid-feedback">
          @foreach ($errors->all() as $err)
            {{ $err }}
          @endforeach
        </div>
      @endif

      <div class="row pt-2 templates-holder">
        @foreach ($templates as $template)
          <div class="col-3 template-item">
            <div class="card">
              @if ($template->intro_image != null)
                <img class="card-image border" src="{{ url("images/uploads/$template->intro_image") }}">
              @else
                <div class="card-image border" style="aspect-ratio: 2/1;background-color: {{ $template->background_color }}"></div>
              @endif
              <div class="card-body">
                <h4 class="title">{{ $template->name ?? "Template Title" }}</h4>
                <p class="lead">{{ $template->template_desc }}</p>
                <small>{{ $template->type == 2 ? __("outcome"): __("scoring") }}</small>
                <div class="row px-0 m-auto mt-2">
                  @if (auth()->check() && auth()->user()->is_admin())
                    <div class="col px-0">
                      <a href="{{ route("edit_template", $template->id) }}" class="btn w-100 btn-success">{{ __("Edit") }}</a>
                    </div>
                  @else
                    <div class="col px-0">
                      <a href="{{ route("preview_template", $template->id) }}" class="btn w-100">{{ __("Preview") }}</a>
                    </div>
                  @endif
                  @if (auth()->check() && auth()->user()->is_admin())
                    <div class="col">
                      <a href="{{ route("delete_template", $template->id) }}" class="btn w-100 btn-danger">{{ __("Delete") }}</a>
                    </div>
                  @else
                    @if (isset($website) && $website !== null)
                      <div class="col">
                        <form action="{{ route("select_template", ["website" => $website->id, "quiz_id" => $template->id]) }}" method="POST">
                          @csrf
                          <button type="submit" class="btn w-100">{{ __("Use") }}</button>
                        </form>
                      </div>
                    @endif
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

@endsection

@section('scripts')
  <script>

    document.getElementById("search-by-name").addEventListener("input", function () {
      console.log(this.value)
      if (this.value != null && this.value != "") {
        let search = this.value;
        Array.of(...document.querySelectorAll(".templates-holder .template-item")).forEach(template => {
          let titleEl = template.querySelector(".title");
          if (!titleEl.innerText.includes(search)) {
            if (document.querySelectorAll(".templates-holder .template-item:not(.d-none)").length > 1) {
              template.classList.add("d-none");
            }
          }else {
            template.classList.remove("d-none");
          }
        });
      }else {
        Array.of(...document.querySelectorAll(".templates-holder .template-item")).forEach(template => {
          template.classList.remove("d-none");
        });
      }
    });
  </script>
@endsection
