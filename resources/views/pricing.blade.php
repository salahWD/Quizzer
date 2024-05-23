@extends('layout.master')

@section('styles')
  <style>
    :root {
      --white: white;
      --gray: #999;
      --lightgray: whitesmoke;
      --darkgreen: #6e3e98;
    }
    .switch-wrapper {
      position: relative;
      display: inline-flex;
      padding: 4px;
      border: 1px solid lightgrey;
      margin-bottom: 16px;
      border-radius: 30px;
      background: var(--white);
    }
    .switch-wrapper [type="radio"] ~ label {
      color: var(--white);
      background: transparent;
    }
    .switch-wrapper [type="radio"]:not(:checked) ~ label[for="yearly"],
    .switch-wrapper [type="radio"]:not(:checked) ~ label[for="monthly"] {
      color: var(--gray);
    }
    .switch-wrapper [type="radio"]:not(:checked) ~ label[for="yearly"]:hover,
    .switch-wrapper [type="radio"]:not(:checked) ~ label[for="monthly"]:hover {
      color: black;
    }
    .switch-wrapper [type="radio"]#yearly:checked ~ label[for="yearly"]:hover,
    .switch-wrapper [type="radio"]#monthly:checked ~ label[for="monthly"]:hover,
    .switch-wrapper [type="radio"]#yearly:checked ~ label[for="yearly"],
    .switch-wrapper [type="radio"]#monthly:checked ~ label[for="monthly"] {
      color: var(--white);
    }
    .switch-wrapper
      [type="radio"]:checked#monthly
      + label[for="yearly"]
      ~ .highlighter {
      transform: none;
    }

    .switch-wrapper
      [type="radio"]:checked#yearly
      + label[for="monthly"]
      ~ .highlighter {
      transform: translateX(100%);
    }

    .switch-wrapper label {
      font-size: 16px;
      z-index: 1;
      min-width: 100px;
      line-height: 32px;
      cursor: pointer;
      border-radius: 30px;
      transition: color 0.25s ease-in-out;
    }

    .switch-wrapper .highlighter {
      position: absolute;
      top: 4px;
      left: 4px;
      width: calc(50% - 4px);
      height: calc(100% - 8px);
      border-radius: 30px;
      background: var(--darkgreen);
      transition: transform 0.25s ease-in-out;
    }
    .generic_content:hover {
      box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }
  </style>
@endsection

@section('content')
  <div class="container pricing">
    <div class="row">
      <div class="col-1"></div>
      <div class="col-10">
        <div id="generic_price_table">
          <section>
            <div class="container">
              <div class="row">
                <div class="col-md-12">
                  <div class="price-heading clearfix">
                    <h1>{{ __('Pricing Table') }}</h1>
                    <div class="switch-wrapper" style="direction: ltr;">
                      <input class="d-none" id="monthly" type="radio" name="switch" checked>
                      <input class="d-none" id="yearly" type="radio" name="switch">
                      <label for="monthly">{{ __('Monthly') }}</label>
                      <label for="yearly">{{ __('Yearly') }}</label>
                      <span class="highlighter"></span>
                    </div>
                    <p class="lead mb-5" style="color: #666;font-weight:400;">{{ __('Get a free 14-day trial for your first time') }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="row plans-page" id="monthly-plans">
                @foreach($monthly_plans as $plan)
                  <div class="col-md-4">
                    <div class="generic_content clearfix @if($plan->best_seller == 1) active @endif shadow-sm border">
                      <div class="generic_head_price clearfix">
                        <div class="generic_head_content clearfix">
                          <div class="head_bg"></div>
                          <div class="head"><span style="letter-spacing: 0">{{ __($plan->name) }}</span></div>
                        </div>
                        <div class="generic_price_tag clearfix">
                          <span class="price">
                            <span class="sign">$</span>
                            <span class="currency">{{ $plan->price }}</span>
                            <span class="date">{{ __('/Monthly') }}</span>
                          </span>
                        </div>
                      </div>
                      <div class="generic_feature_list">
                        <ul>
                          <li><span>{{ $plan->package()["WEBSITES"] }}</span> {{ $plan->package()["WEBSITES"] > 1 ? __("Websites Accounts"): __('Website Account') }}</li>
                          <li>
                            <span style="margin-right: 5px">{{ $plan->package()["RESPONSES"] }}</span>
                            {{ __('responses / month') }}
                          </li>
                          @if($plan->package()["TRANSLATABLE"] == 1)
                            <li><span>{{ __('Two Languages') }}</span></li>
                          @else
                            <li><span>{{ __('One Language') }} </span> {{ __('is Allowed') }}</li>
                          @endif
                          <li><span>{{ __('Custom Domain') }}</span></li>
                          <li><span>{{ __('Reporting') }}</span></li>
                          <li><span>{{ __('UTM Web Tracking') }}</span></li>
                          <li><span>{{ __('Email Integrations') }}</span></li>
                          <li><span>{{ __('Advertising Platforms Integrations') }}</span></li>
                          <li><span>{{ __('Google Tag Manager Integration') }}</span></li>
                          <li><span>{{ __('Calendly Integration') }}</span></li>
                        </ul>
                      </div>
                      <div class="generic_price_btn clearfix">
                        <a class="changeable-btn" href=
                        "@if(!auth()->check())
                            {{ route("register") }}
                          @elseif(auth()->user()->is_trailing() || !auth()->user()->took_trail())
                            {{ route("change_package", $plan->id) }}
                          @else
                            {{ route("offer", $plan->id) }}
                          @endif"
                        >{{ __('Subscribe Now') }}</a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="row plans-page d-none" id="yearly-plans">
                @foreach($yearly_plans as $plan)
                  <div class="col-md-4">
                    <div class="generic_content clearfix @if($plan->best_seller == 1) active @endif shadow-sm border">
                      <div class="generic_head_price clearfix">
                        <div class="generic_head_content clearfix">
                          <div class="head_bg"></div>
                          <div class="head"><span style="letter-spacing: 0">{{ __($plan->name) }}</span></div>
                        </div>
                        <div class="generic_price_tag clearfix">
                          <span class="price">
                            <span class="sign">$</span>
                            <span class="currency">{{ $plan->price }}</span>
                            <span class="date">{{ __('/YER') }}</span>
                          </span>
                        </div>
                      </div>
                      <div class="generic_feature_list">
                        <ul>
                          <li><span>{{ $plan->package()["WEBSITES"] }}</span> {{ __('Website Account') }}</li>
                          <li>
                            <span style="margin-right: 5px">{{ $plan->package()["RESPONSES"] }}</span>
                            {{ __('responses / month') }}
                          </li>
                          @if($plan->package()["TRANSLATABLE"] == 1)
                            <li><span>{{ __('Two Languages') }}</span></li>
                          @else
                            <li><span>{{ __('One Language') }} </span> {{ __('is Allowed') }}</li>
                          @endif
                          <li><span>{{ __('Custom Domain') }}</span></li>
                          <li><span>{{ __('Reporting') }}</span></li>
                          <li><span>{{ __('UTM Web Tracking') }}</span></li>
                          <li><span>{{ __('Email Integrations') }}</span></li>
                          <li><span>{{ __('Advertising Platforms Integrations') }}</span></li>
                          <li><span>{{ __('Google Tag Manager Integration') }}</span></li>
                          <li><span>{{ __('Calendly Integration') }}</span></li>
                        </ul>
                      </div>
                      <div class="generic_price_btn clearfix">
                        <a class="changeable-btn" href=
                        "@if(!auth()->check())
                            {{ route("login") }}
                          @elseif(auth()->user()->is_trailing() || !auth()->user()->took_trail())
                            {{ route("change_package", $plan->id) }}
                          @else
                            {{ route("offer", $plan->id) }}
                          @endif"
                        >{{ __('Subscribe Now') }}</a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </section>
        </div>
      </div>
      <div class="col-1"></div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    const switchInputs = document.querySelectorAll(".switch-wrapper input");

    for (const switchInput of switchInputs) {
      switchInput.addEventListener("input", function () {
        changePackages(switchInput.id)
      });
    }

    function changePackages(type) {
      if (type == "yearly") {
        document.getElementById("yearly-plans").classList.remove("d-none");
        document.getElementById("monthly-plans").classList.add("d-none");
      }else {
        document.getElementById("yearly-plans").classList.add("d-none");
        document.getElementById("monthly-plans").classList.remove("d-none");
      }
    }
    changePackages("monthly");

  </script>
@endsection
