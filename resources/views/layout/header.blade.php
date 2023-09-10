<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand" href="{{ route("home") }}">{{ env("APP_NAME") }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @if(auth()->check())
          <li class="nav-item">
            <a class="nav-link" href="{{ route("show_websites") }}">Dashboard</a>
          </li>
        @endif
        <li class="nav-item">
          <a class="nav-link" href="{{ route("features" ) }}">Features</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route("case-studies") }}">Case Studies</a>
        </li>
        <li class="nav-item {{-- dropdown --}}">
          <a class="nav-link" href="{{ route("show_template") }}">
            Templates
          </a>
          {{--<a class="nav-link dropdown-toggle" href="#" id="templatesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Templates
          </a>
           <ul class="dropdown-menu" aria-labelledby="templatesDropdown">
            <li><a class="dropdown-item" href="/templates/quizzes">Quiz Templates</a></li>
            <li><a class="dropdown-item" href="/templates/quizzes">Survey Templates</a></li>
            <li><a class="dropdown-item" href="/templates/forms">Form Templates</a></li>
          </ul> --}}
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route("pricing") }}">Pricing</a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto mb-2 mb-lg-0">
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
