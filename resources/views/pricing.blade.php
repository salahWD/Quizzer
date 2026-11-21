@extends('layout.master')

@section('styles')
  <style>
    .mu-pricing { padding-block: clamp(2.5rem, 6vw, 5rem) 1rem; }
    .mu-pricing-head { text-align: center; max-width: 640px; margin: 0 auto 2.75rem; }
    .mu-pricing-head h1 { font-size: clamp(2rem, 1.4rem + 2.4vw, 3rem); letter-spacing: -0.02em; margin: 0 0 .6rem; }
    html[lang="ar"] .mu-pricing-head h1 { letter-spacing: 0; }
    .mu-pricing-head p { color: var(--mu-ink-2); margin: 1rem 0 0; }
    .switch-wrapper { position: relative; display: inline-grid; grid-template-columns: 1fr 1fr; padding: 4px; border-radius: 12px; background: var(--mu-mist); }
    .switch-wrapper label { position: relative; z-index: 1; min-width: 110px; padding: .45rem 1rem; text-align: center; cursor: pointer; border-radius: 9px; color: var(--mu-ink-2); font-weight: 500; font-size: .95rem; transition: color .25s var(--mu-ease); }
    .switch-wrapper label:hover { color: var(--mu-pine-2); }
    .switch-wrapper #monthly:checked ~ label[for="monthly"],
    .switch-wrapper #yearly:checked ~ label[for="yearly"] { color: var(--mu-pine-2); }
    .switch-wrapper .highlighter { position: absolute; top: 4px; left: 4px; width: calc(50% - 4px); height: calc(100% - 8px); border-radius: 9px; background: var(--mu-white); box-shadow: var(--mu-shadow-1); transition: transform .3s var(--mu-ease); }
    .switch-wrapper #yearly:checked ~ .highlighter { transform: translateX(100%); }

    .mu-plans { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; align-items: stretch; }
    .mu-plan { position: relative; display: flex; flex-direction: column; padding: 1.75rem; border-radius: var(--mu-r-l); background: var(--mu-white); border: 1px solid var(--mu-line); transition: transform .25s var(--mu-ease), box-shadow .25s var(--mu-ease), border-color .25s; }
    .mu-plan:hover { transform: translateY(-4px); box-shadow: var(--mu-shadow-2); border-color: #c3d6d2; }
    .mu-plan.is-best { background: var(--mu-pine); border-color: var(--mu-pine); color: #fff; }
    .mu-plan.is-best:hover { box-shadow: var(--mu-shadow-3); }
    .mu-plan-name { font-size: 1.05rem; font-weight: 600; margin: 0; color: inherit; }
    .mu-plan-price { display: flex; align-items: baseline; gap: .25rem; margin: 1.25rem 0 1.5rem; direction: ltr; justify-content: flex-start; }
    html[lang="ar"] .mu-plan-price { justify-content: flex-end; }
    .mu-plan-price .sign { font-size: 1.3rem; font-weight: 500; opacity: .7; }
    .mu-plan-price .currency { font-size: 3.2rem; line-height: 1; font-weight: 600; letter-spacing: -.03em; }
    .mu-plan-price .date { font-size: .9rem; opacity: .65; margin-inline-start: .25rem; }
    .mu-plan ul { list-style: none; margin: 0 0 1.75rem; padding: 1.25rem 0 0; border-top: 1px solid var(--mu-line); display: grid; gap: .55rem; font-size: .94rem; flex: 1; }
    .mu-plan.is-best ul { border-top-color: rgba(255,255,255,.16); }
    .mu-plan li { display: flex; gap: .6rem; align-items: flex-start; color: var(--mu-ink-2); }
    .mu-plan.is-best li { color: rgba(255,255,255,.82); }
    .mu-plan li::before { content: ""; flex: none; width: 18px; height: 18px; margin-top: .15rem; border-radius: 50%; background: var(--mu-mint-soft) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230d4b45' stroke-width='3.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E") center / 10px no-repeat; }
    .mu-plan.is-best li::before { background-color: var(--mu-mint); }
    .mu-plan li b { color: var(--mu-ink); font-weight: 600; }
    .mu-plan.is-best li b { color: #fff; }
    .mu-plan .changeable-btn { display: flex; align-items: center; justify-content: center; min-height: 46px; border-radius: var(--mu-r-m); font-weight: 500; text-decoration: none; background: var(--mu-mist); color: var(--mu-pine-2); transition: transform .18s var(--mu-ease), background-color .2s, box-shadow .2s; }
    .mu-plan .changeable-btn:hover { transform: translateY(-2px); background: var(--mu-pine); color: #fff; }
    .mu-plan .changeable-btn:active { transform: scale(.98); }
    .mu-plan.is-best .changeable-btn { background: var(--mu-mint); color: var(--mu-pine-2); }
    .mu-plan.is-best .changeable-btn:hover { background: #45d3aa; box-shadow: 0 12px 30px -12px rgba(43,191,148,.7); }
    @media (max-width: 991.98px) { .mu-plans { grid-template-columns: minmax(0, 1fr); max-width: 460px; margin-inline: auto; } }
  </style>
@endsection

@section('content')
  <div class="container mu-pricing">
    <div class="mu-pricing-head">
      <h1>{{ __('Pricing Table') }}</h1>
      <div class="switch-wrapper" style="direction: ltr;">
        <input class="d-none" id="monthly" type="radio" name="switch" checked>
        <input class="d-none" id="yearly" type="radio" name="switch">
        <label for="monthly">{{ __('Monthly') }}</label>
        <label for="yearly">{{ __('Yearly') }}</label>
        <span class="highlighter"></span>
      </div>
      <p>{{ __('Get a free 14-day trial for your first time') }}</p>
    </div>

    @foreach ([['monthly', $monthly_plans, __('/Monthly'), 'register'], ['yearly', $yearly_plans, __('/YER'), 'login']] as [$period, $plans, $per, $guestRoute])
      <div class="mu-plans {{ $period == 'yearly' ? 'd-none' : '' }}" id="{{ $period }}-plans">
        @foreach($plans as $plan)
          <div class="mu-plan @if($plan->best_seller == 1) is-best @endif">
            <h2 class="mu-plan-name">{{ __($plan->name) }}</h2>
            <div class="mu-plan-price">
              <span class="sign">$</span>
              <span class="currency">{{ $plan->price }}</span>
              <span class="date">{{ $per }}</span>
            </div>
            <ul>
              <li><span><b>{{ $plan->package()["WEBSITES"] }}</b> {{ $plan->package()["WEBSITES"] > 1 ? __("Websites Accounts") : __('Website Account') }}</span></li>
              <li><span><b>{{ $plan->package()["RESPONSES"] }}</b> {{ __('responses / month') }}</span></li>
              @if($plan->package()["TRANSLATABLE"] == 1)
                <li>{{ __('Two Languages') }}</li>
              @else
                <li>{{ __('One Language') }} {{ __('is Allowed') }}</li>
              @endif
              <li>{{ __('Custom Domain') }}</li>
              <li>{{ __('Reporting') }}</li>
              <li>{{ __('UTM Web Tracking') }}</li>
              <li>{{ __('Email Integrations') }}</li>
              <li>{{ __('Advertising Platforms Integrations') }}</li>
              <li>{{ __('Google Tag Manager Integration') }}</li>
              <li>{{ __('Calendly Integration') }}</li>
            </ul>
            <a class="changeable-btn" href="@if(!auth()->check()){{ route($guestRoute) }}@elseif(auth()->user()->is_trailing() || !auth()->user()->took_trail()){{ route("change_package", $plan->id) }}@else{{ route("offer", $plan->id) }}@endif">{{ __('Subscribe Now') }}</a>
          </div>
        @endforeach
      </div>
    @endforeach
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
