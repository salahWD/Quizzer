@extends("layout.app-master")

@section("content")

  @php
    $mu_user = auth()->user();
    $mu_meters = [
      [__('website Account'), $mu_user->websites()->count(), $mu_user->allowed_websites()],
      [__('Monthly Responses'), $mu_user->total_responses_count(), $mu_user->allowed_responses()],
    ];
  @endphp

  <div class="mu-usage info-bar">
    <div class="container">
      @foreach ($mu_meters as [$label, $used, $allowed])
        @php $pct = is_numeric($allowed) && $allowed > 0 ? min(100, round($used / $allowed * 100)) : (is_numeric($allowed) ? 100 : 8); @endphp
        <div class="mu-meter">
          <span>{{ $label }}</span>
          <span class="bar" aria-hidden="true"><span style="width: {{ $pct }}%"></span></span>
          <b class="mu-ltr">{{ $used }}/{{ $allowed }}</b>
        </div>
      @endforeach
      <a href="{{ route("pricing") }}" class="btn btn-sm btn-outline-primary upgrade">{{ __('Upgrade Plane') }}</a>
    </div>
  </div>

  <div class="container mb-5">
    @if (session('status'))
      <div class="alert alert-warning mt-4" style="width: fit-content">
          {{ session('status') }}
      </div>
    @endif
    @if(!$mu_user->can_open_quiz())
      <div class="row mt-4">
        {!! $mu_user->can_open_quiz_err() !!}
      </div>
    @endif

    <div class="mu-page-head">
      <div>
        <h1 class="title">{{ __('Accounts') }}</h1>
      </div>
      <a href="{{ route("create_website") }}" class="btn btn-primary">{{ __('Add Account') }} <i class="fa fa-plus ms-1"></i></a>
    </div>

    <div class="mu-site-grid">
      @foreach ($websites as $web)
        @php $host = preg_replace("(^https?://)", "", $web->url); @endphp
        <article class="mu-site">
          <div class="mu-site-top">
            <span class="mu-site-icon" aria-hidden="true">{{ mb_substr(preg_replace('/^www\./', '', $host), 0, 1) }}</span>
            <a href="{{ route("edit_website", ["lang" => app()->getLocale(), "website" => $web->id]) }}" class="mu-site-settings" aria-label="{{ __('Setting') }}"><i class="fa fa-gear"></i></a>
          </div>
          <h2>{{ $host }}</h2>
          <a href="{{ route("show_website", ["lang" => app()->getLocale(), "website" => $web->id]) }}" class="btn btn-sm btn-primary">{{ __('Manage') }}</a>
        </article>
      @endforeach
      <a href="{{ route("create_website") }}" class="mu-site mu-site-new">
        <span class="plus" aria-hidden="true">+</span>
        <span>{{ __('Add Account') }}</span>
      </a>
    </div>
  </div>

@endsection
