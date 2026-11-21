<footer class="mu-footer">
  <div class="container">
    <div class="mu-footer-grid">
      <div class="mu-footer-brand">
        <a class="mu-brand" href="{{ route('home') }}">
          @if(!empty(env("LOGO")))
            <img src="{{ url("images/".env("LOGO")) }}" alt="{{ env("APP_NAME") }}" loading="lazy">
          @else
            <span class="mu-brand-mark" aria-hidden="true"></span>{{ env("APP_NAME") }}
          @endif
        </a>
        <p>{{ __("The first Arabic smart assistant in the Middle East") }}</p>
        <div class="mu-footer-pay">
          <img src="{{ url("images/pay.png") }}" alt="{{ __('We Accepted') }}" width="808" height="264" loading="lazy">
        </div>
      </div>

      <div>
        <h2>{{ __('Link') }}</h2>
        <ul>
          <li><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
          <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
          <li><a href="{{ route('pricing') }}">{{ __('Pricing') }}</a></li>
        </ul>
      </div>

      <div>
        <h2>{{ __('Features') }}</h2>
        <ul>
          <li><a href="{{ route('features') }}">{{ __('Features of musaad') }}</a></li>
          <li><a href="{{ route('PrivacyPolicy') }}">{{ __('Privacy Policy') }}</a></li>
          <li><a href="{{ route('TermsandConditions') }}">{{ __('Terms and Conditions') }}</a></li>
        </ul>
      </div>

      <div>
        <h2>{{ __('Contact us') }}</h2>
        <ul>
          <li>{{ __('Jordan - Amman - khalda') }}</li>
          <li><a class="mu-ltr" href="https://wa.me/962796300545">+962796300545</a></li>
          <li><a href="mailto:info@musaad.ai">info@musaad.ai</a></li>
        </ul>
      </div>
    </div>

    <div class="mu-footer-bottom">
      <span>{{ __("Copyright 2023 © MUSAAD AI") }} &copy; {{ date("Y") }}</span>
    </div>
  </div>
</footer>
