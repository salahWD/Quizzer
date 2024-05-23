<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="{{ auth()->check() ? route("show_websites") : route("home") }}">@if(!empty(env("LOGO"))) <img style="max-height: 26px;" src="{{ url("images/".env("LOGO")) }}" alt=""> @else {{ env("APP_NAME") }} @endif</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appMainHeader" aria-controls="appMainHeader" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="appMainHeader">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @if (isset($websites) && count($websites) > 0)
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="websites" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              @lang('Websites')
            </a>
            <ul class="dropdown-menu" aria-labelledby="websites">
              @foreach ($websites as $web)
                <li><a class="dropdown-item" href="{{ route("show_website", $web->id) }}">{{ preg_replace("(^https?://)", "", $web->url ); }}</a></li>
              @endforeach
            </ul>
          </li>
        @endif
        @if(auth()->check() && auth()->user()->status == "admin")
          <li class="nav-item">
            <a class="nav-link" href="{{ route("create_theme") }}">{{ __("Themes") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("show_template") }}">{{ __("Templates") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("show_users") }}">{{ __("Users") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("show_coupons") }}">{{ __("Coupons") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("manage_payments") }}">{{ __("Payments") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("website_manage_domains") }}">{{ __("Domains") }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route("dash_general_reports") }}">{{ __("Reports") }}</a>
          </li>
        @endif
      </ul>
      <div class="dropdown mx-3">
        <button type="button" class="btn btn-custom btn-primary dropdown-toggle" data-bs-toggle="dropdown">
          {{ LaravelLocalization::getCurrentLocaleNative() }}
        </button>
        <ul class="dropdown-menu">
          @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <li>
              <a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                {{ $properties['native'] /* name | native */ }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
      <ul class="navbar-nav mb-2 mb-lg-0">
        @guest
          @if (Route::has('login'))
            <li class="nav-item">
              <a class="btn btn-primary" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
          @endif

          @if (Route::has('register'))
            <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
            </li>
          @endif
        @else
          <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="box-shadow: none">
              <a href="{{ route('profile') }}" class="nav-link">{{ auth()->user()->name }}</a>
              <a href="{{ route('subscription_info', auth()->user()->id) }}" class="nav-link">{{ __("subscription info") }}</a>
              <a href="{{ route('logout') }}" class="nav-link text-danger">{{ __('Logout') }}</a>
            </div>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>

<nav class="navbar p-0 navbar-expand-lg navbar-dark" style="background: #6e3e98;color:white">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          @if(isset($website) && $website != null && $website->id != null)
            <a class="nav-link" href="{{ route("show_website", $website->id) }}">{{ __('Dashboard') }}</a>
          @elseif(isset($quiz->website) && $quiz->website != null && $quiz->website->id != null)
            <a class="nav-link" href="{{ route("show_website", $quiz->website->id) }}">{{ __('Dashboard') }}</a>
          @else
            <a class="nav-link" href="{{ route("show_websites") }}">{{ __('Dashboard') }}</a>
          @endif
        </li>
      </ul>
      <form class="d-flex">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              {{ __('Setting') }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="navbarDropdown">
              @php
                $trig = true;
              @endphp
              @if(isset($website) && $website != null && $website->url != null)
                <li><a class="dropdown-item" href="#">{{ preg_replace("(^https?://)", "", $website->url ); }}</a></li>
                <li><a class="dropdown-item" href="{{ route('website_config', $website->id) }}">{{ __('Integrations') }}</a></li>
                @if(auth()->check() && auth()->user()->can_custom_domain())
                  <li><a class="dropdown-item" href="{{ route('website_custom_domain', $website->id) }}">{{ __('custom domain') }}</a></li>
                @endif
                @php $trig = false @endphp
                <li><hr class="dropdown-divider"></li>
              @endif
              @if($trig && isset($quiz) && $quiz != null && $quiz->website != null && $quiz->website->url != null)
                <li><a class="dropdown-item" href="#">{{ preg_replace("(^https?://)", "", $quiz->website->url ); }}</a></li>
                <li><a class="dropdown-item" href="{{ route('website_config', $quiz->website->id) }}">{{ __('Integrations') }}</a></li>
                <li><a class="dropdown-item" href="{{ route('website_custom_domain', $quiz->website->id) }}">{{ __('custom domain') }}</a></li>
                @php $trig = false @endphp
                <li><hr class="dropdown-divider"></li>
              @endif
              <li><a class="dropdown-item" href="{{ route("show_websites") }}">{{ __('Back To Home') }}</a></li>
            </ul>
          </li>
        </ul>
      </form>
    </div>
  </div>
</nav>
