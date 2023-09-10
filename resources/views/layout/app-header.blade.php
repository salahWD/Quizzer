<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="{{ route("show_websites") }}">{{ env("APP_NAME") }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
            <a class="nav-link" href="{{ route("create_theme") }}">{{ __("Create Themes") }}</a>
          </li>
        @endif
        @if(auth()->check() && auth()->user()->status == "admin")
          <li class="nav-item">
            <a class="nav-link" href="{{ route("show_template") }}">{{ __("Manage Template") }}</a>
          </li>
        @endif
        @if(auth()->check() && auth()->user()->status == "admin")
          <li class="nav-item">
            <a class="nav-link" href="{{ route("show_users") }}">{{ __("Manage Users") }}</a>
          </li>
        @endif
        @if(auth()->check() && auth()->user()->status == "admin")
          <li class="nav-item">
            <a class="nav-link" href="{{ route("show_coupons") }}">{{ __("Manage Coupons") }}</a>
          </li>
        @endif
        @if(auth()->check() && auth()->user()->status == "admin")
          <li class="nav-item">
            <a class="nav-link" href="{{ route("manage_payments") }}">{{ __("Approve Payments") }}</a>
          </li>
        @endif
      </ul>
      <div class="dropdown mx-3">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
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
      <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
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
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <a href="{{ route('logout') }}" class="nav-link">{{ __('Logout') }}</a>
            </div>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>
