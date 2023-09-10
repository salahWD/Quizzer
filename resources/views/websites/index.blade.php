@extends("layout.app-master")

@section("content")

  <div class="info-bar d-flex align-items-center justify-content-center bg-light py-2 gap-3">
    <p class="websites m-0">website Account: <b>{{ auth()->user()->websites()->count() }}/{{ auth()->user()->allowed_websites() }}</b></p>
    <p class="responses m-0">Monthly Responses: <b>{{ auth()->user()->total_responses_count() }}/{{ auth()->user()->allowed_responses() }}</b></p>
    @if(!auth()->user()->is_admin() && auth()->user()->get_package_id() != 3)
      <a href="{{ route("pricing") }}" class="btn btn-primary upgrade">Upgrade Plane</a>
    @endif
  </div>

  <div class="container mt-5 mb-3">
    @if (session('status'))
      <div class="alert alert-warning" style="width: fit-content">
          {{ session('status') }}
      </div>
    @endif
    @if(!auth()->user()->can_open_quiz())
      <div class="row">
        {!! auth()->user()->can_open_quiz_err() !!}
      </div>
    @endif
    <h1 class="title my-5 text-center">@lang('Websites')</h1>
    <a href="{{ route("create_website") }}" class="btn btn-primary px-4 py-2 mb-5">Add Website <i class="fa fa-plus"></i></a>
    @if (count($websites))
    <div class="row">
      @foreach ($websites as $web)
        <div class="col-md-4 col-lg-3">
          <div class="card bg-dark text-white text-center p-4">
            <a href="{{ route("edit_website", ["lang" => app()->getLocale(), "website" => $web->id]) }}" class="align-self-end"><i class="fa fa-1x fa-gear"></i></a>
            <div class="card-body">
              <h5 class="card-title mb-4">{{ preg_replace("(^https?://)", "", $web->url ); }}</h5>
              <a href="{{ route("show_website", ["lang" => app()->getLocale(), "website" => $web->id]) }}" class="btn text-light border-light">Manage</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    @endif
  </div>

@endsection

{{-- @section("scripts")
@endsection --}}
