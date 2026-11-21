<!-- Navbar -->
@php $mu_route = optional(\Request::route())->getName(); @endphp
<header class="mu-header" id="mu-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container mu-header-inner">
      <a class="mu-brand" href="{{ auth()->check() ? route("show_websites") : route("home") }}">
        @if(!empty(env("LOGO")))
          <img src="{{ url("images/".env("LOGO")) }}" alt="{{ env("APP_NAME") }}">
        @else
          <span class="mu-brand-mark" aria-hidden="true"></span>{{ env("APP_NAME") }}
        @endif
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-lg-auto mb-2 mb-lg-0">
          @if(auth()->check())
            <li class="nav-item">
              <a class="nav-link" href="{{ route("show_websites") }}">{{ __('Dashboard') }}</a>
            </li>
          @endif
          <li class="nav-item">
            <a class="nav-link {{ $mu_route == 'features' ? 'active' : '' }}" href="{{ route("features" ) }}">{{ __('Features') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $mu_route == 'case-studies' ? 'active' : '' }}" href="{{ route("case-studies") }}">{{ __('Case Studies') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $mu_route == 'show_template' ? 'active' : '' }}" href="{{ route("show_template") }}">{{ __('Templates') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ $mu_route == 'pricing' ? 'active' : '' }}" href="{{ route("pricing") }}">{{ __('Pricing') }}</a>
          </li>
        </ul>
        <div class="mu-header-actions">
          <div class="dropdown">
            <button type="button" class="btn mu-lang dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/></svg>
              {{ LaravelLocalization::getCurrentLocaleNative() }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                <li>
                  <a class="dropdown-item {{ app()->getLocale() == $localeCode ? 'active' : '' }}" rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                    {{ $properties['native'] }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
          @guest
            @if (Route::has('login'))
              <a class="mu-header-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            @endif
            @if (Route::has('register'))
              <a class="btn btn-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
            @endif
          @else
            <div class="dropdown">
              <a id="navbarDropdown" class="mu-header-link mu-user-toggle dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mu-avatar" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>{{ auth()->user()->name }}
              </a>
              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a href="{{ route('profile') }}" class="dropdown-item">{{ __('Profile') }}</a>
                <a href="{{ route('show_websites') }}" class="dropdown-item">{{ __('Dashboard') }}</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item text-danger">{{ __('Logout') }}</a>
              </div>
            </div>
          @endguest
        </div>
      </div>
    </div>
  </nav>
</header>
<script>
  (function () {
    var h = document.getElementById('mu-header'), on = false;
    function t() {
      var s = window.scrollY > 8;
      if (s !== on) { on = s; h.classList.toggle('is-scrolled', s); }
    }
    window.addEventListener('scroll', t, { passive: true });
    t();
  })();
</script>
