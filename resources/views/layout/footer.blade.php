<style>
  .footer {
  }
  .footer .title {
    color: rgb(0 73 86);
  }
  .footer ul {
    list-style: none;
    padding: 0;
  }
  .footer ul li {
    font-weight: 400!important;
    font-size: 1rem;
    line-height: 24px;
  }
  .footer ul li a {
    color: rgb(0 77 90);
    text-decoration: none;
  }
</style>

<footer class="footer bg-light py-4 pt-5 mt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-sm-12">
        <h4 class="title">{{ __('Link') }}</h4>
        <ul>
          <li><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
          <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
          <li><a href="{{ route('pricing') }}">{{ __('Pricing') }}</a></li>
        </ul>
      </div>
       <div class="col-md-3 col-sm-12">
        <h4 class="title">{{ __('Link') }}</h4>
        <ul>
          <li><a href="#">{{ __('Features of musaad') }}</a></li>
          <li><a href="{{ route('PrivacyPolicy') }}">{{ __('Privacy Policy') }}</a></li>
          <li><a href="{{ route('TermsandConditions') }}">{{ __('Terms and Conditions') }}</a></li>
        </ul>
      </div>

      <div class="col-md-3 col-sm-12">
        <h4 class="title">{{ __('Contact us') }}</h4>
        <ul>
          <li>
            <a>
              {{ __('Jordan - Amman - khalda') }}
            </a>
          </li>
          <li><a href="https://wa.me/962796300545">+962796300545</a></li>
          <li><a href="mailto:info@musaad.ai">info@musaad.ai</a></li>
        </ul>
      </div>

      <div class="col-md-3 col-sm-12">
        <h4 class="title">{{ __('We Accepted') }}</h4>
        <img src="{{ url("images/pay.png") }}" alt="" width="100%"/>
      </div>
    </div>
    <hr>
    <p class="text-center lead">{{ __("Copyright 2023 © MUSAAD AI") }} &copy; {{ date("Y") }}</p>
  </div>
</footer>
