@extends('layout.master')

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
          <input type="search" class="form-control" placeholder="search by name">
        </div>
        <div class="col-3">
          <div class="d-flex gap-3">
            @if (auth()->check())
              @if (auth()->user()->is_admin())
                <a href="{{ route('create_template') }}" class="btn btn-primary">{{ __("Create Template") }}</a>
              @endif
              @if (isset($website) && !empty($website))
                <a href="{{ route('create_quiz', $website->id) }}" class="btn btn-primary">{{ __("Create Quiz") }}</a>
              @endif
            @endif
          </div>
        </div>
        <div class="col-2"></div>
      </div>

      @if (count($errors->all()) > 0)
        <div class="invalid-feedback">
          @foreach ($errors->all() as $err)
            {{ $err }}
          @endforeach
        </div>
      @endif

      <div class="row pt-2">
        @foreach ($templates as $template)
          <div class="col-3">
            <div class="card">
              @if ($template->intro_image != null)
                <img class="card-image border" src="{{ url("images/uploads/$template->intro_image") }}">
              @else
                <div class="card-image border" style="aspect-ratio: 2/1;background-color: {{ $template->background_color }}"></div>
              @endif
              <div class="card-body">
                <h4 class="title">{{ $template->name ?? "Template Title" }}</h4>
                <p class="lead">{{ $template->template_desc }}</p>
                <small>{{ $template->type == 2 ? "outcome": "scoring" }}</small>
                <div class="row px-0 m-auto mt-2">
                  @if (auth()->check())
                    @if (auth()->user()->is_admin())
                      <div class="col px-0">
                        <a href="{{ route("edit_template", $template->id) }}" class="btn w-100 btn-success">{{ __("Edit") }}</a>
                      </div>
                    @endif
                  @else
                    <div class="col px-0">
                      <a href="{{ route("preview_template", $template->id) }}" class="btn w-100">{{ __("Preview") }}</a>
                    </div>
                  @endif
                  <div class="col-1"></div>
                  @if (auth()->check())
                    @if (auth()->user()->is_admin())
                      <div class="col px-0">
                        <a href="{{ route("delete_template", $template->id) }}" class="btn w-100 btn-danger">{{ __("Delete") }}</a>
                      </div>
                    @else
                      <div class="col px-0">
                        @if (null !== $website && auth()->check())
                          <form action="{{ route("select_template", ["website" => $website->id, "quiz_id" => $template->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn w-100">{{ __("Use") }}</button>
                          </form>
                        @endif
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
