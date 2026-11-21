@extends('layout.master')

@section('meta')
  @php $page_title = __('Musaad , Create an AI sales rep that improves and automates lead generation') @endphp
  <meta name="description" content="{{ __('Musaad is your dedicated sales employee who implements your strategies and increases your online sales. He works 24 hours a day, 365 days a year, to achieve sales easily, quickly, and without complications.') }}">
  <meta name="theme-color" content="#0d4b45">
  <link rel="preload" as="image" href="{{ url('images/landing/intro-1600.webp') }}" imagesrcset="{{ url('images/landing/intro-720.webp') }} 720w, {{ url('images/landing/intro-1600.webp') }} 1600w" imagesizes="(max-width: 1200px) 100vw, 1080px">
  <script>document.documentElement.classList.add('mu-js')</script>
@endsection

@section('styles')
  @vite(["resources/sass/home.scss"])
@endsection

@section('content')

{{-- Inline icon sprite: no icon font on this page --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
    <symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2.5 3.5h2.6l2.3 11.2a1.6 1.6 0 0 0 1.6 1.3h8.6a1.6 1.6 0 0 0 1.6-1.2l1.6-6.8H6.1"/></symbol>
    <symbol id="i-course" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9l10-5 10 5-10 5L2 9z"/><path d="M6 11v5c0 1.5 2.7 3 6 3s6-1.5 6-3v-5"/><path d="M22 9v6"/></symbol>
    <symbol id="i-digital" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M12 7.5v6M9.5 11l2.5 2.5 2.5-2.5"/></symbol>
    <symbol id="i-service" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8z"/><path d="M4 21c.8-3.6 4-6 8-6s7.2 2.4 8 6"/><path d="M17.5 4.5l1.5-1.5M19.5 8.5h2"/></symbol>
  </defs>
</svg>

<main class="mu-landing">

  {{-- 1. Hero --}}
  <section class="mu-hero" aria-labelledby="hero-title">
    <div class="mu-wrap">
      <div class="mu-hero-copy" data-enter>
        <h1 id="hero-title" class="mu-display">{{ __("Musaad , Create an AI sales rep that improves and automates lead generation") }}</h1>
        <div class="mu-hero-side">
          <p class="mu-body-l">{{ __('Musaad is your dedicated sales employee who implements your strategies and increases your online sales. He works 24 hours a day, 365 days a year, to achieve sales easily, quickly, and without complications.') }}</p>
          <div class="mu-hero-actions">
            <a href="{{ route("pricing") }}" class="mu-btn mu-btn-solid">
              {{ __("Create your smart Musaad now") }}
              <svg aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
            <a href="{{ route("login") }}" class="mu-btn mu-btn-ghost">{{ __("Log in") }}</a>
          </div>
        </div>
      </div>

      <div class="mu-stage" data-enter-late>
        <img
          src="{{ url('images/landing/intro-1600.webp') }}"
          srcset="{{ url('images/landing/intro-720.webp') }} 720w, {{ url('images/landing/intro-1600.webp') }} 1600w"
          sizes="(max-width: 1200px) 100vw, 1080px"
          width="1600" height="808"
          fetchpriority="high"
          alt="{{ __('Musaad') }}">
      </div>
    </div>
  </section>

  {{-- Proof: continues the pine stage --}}
  <section class="mu-proof" aria-labelledby="proof-title">
    <div class="mu-wrap">
      <div class="mu-proof-grid" data-reveal>
        <div>
          <h2 id="proof-title" class="mu-h2">{{ __("The first Arabic smart assistant in the Middle East") }}</h2>
          <p class="mu-body-l">{{ __('Create your sales paths and join success stories with Assistant Today') }}</p>
        </div>
        <div class="mu-stats">
          <div class="mu-stat">
            <strong>+3000</strong>
            <span>{{ __('Customer Acquisition Cost Less 96%') }}</span>
          </div>
          <div class="mu-stat">
            <strong>+9000</strong>
            <span>{{ __('Prospective customer') }}</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- 2. Who it's for --}}
  <section class="mu-industries" aria-labelledby="ind-title">
    <div class="mu-wrap">
      <h2 id="ind-title" class="mu-h2" data-reveal>{{ __("No matter the type of product or service you're selling, you can rely on Musaad") }}</h2>
      <div class="mu-ind-grid" data-reveal>
        <article class="mu-ind">
          <div class="mu-ind-icon"><svg aria-hidden="true"><use href="#i-cart"/></svg></div>
          <h3>{{ __("E-commerce") }}</h3>
          <p>{{ __('You can build a sales journey to select the best product or products for your potential customer') }}</p>
        </article>
        <article class="mu-ind">
          <div class="mu-ind-icon"><svg aria-hidden="true"><use href="#i-course"/></svg></div>
          <h3>{{ __("Training courses") }}</h3>
          <p>{{ __('Create a simple test to improve the quality of the prospective customer and boost their purchase intent') }}</p>
        </article>
        <article class="mu-ind">
          <div class="mu-ind-icon"><svg aria-hidden="true"><use href="#i-digital"/></svg></div>
          <h3>{{ __("Digital products") }}</h3>
          <p>{{ __('Offer the prospective customer the best deal to encourage them to make an immediate purchase') }}</p>
        </article>
        <article class="mu-ind">
          <div class="mu-ind-icon"><svg aria-hidden="true"><use href="#i-service"/></svg></div>
          <h3>{{ __("Services") }}</h3>
          <p>{{ __('Understand your potential customer better and provide them with the appropriate solution and offer to increase their interest') }}</p>
        </article>
      </div>
    </div>
  </section>

  {{-- 3. Feature chapters --}}
  <section class="mu-features" aria-label="{{ __('Features') }}">
    <div class="mu-wrap">

      <div class="mu-chapter" data-reveal>
        <div class="mu-frame">
          <img loading="lazy" decoding="async"
            src="{{ url('images/landing/languages-1200.webp') }}"
            srcset="{{ url('images/landing/languages-720.webp') }} 720w, {{ url('images/landing/languages-1200.webp') }} 1200w"
            sizes="(max-width: 991px) 100vw, 680px" width="1200" height="820" alt="">
        </div>
        <div class="mu-chapter-copy">
          <h2 class="mu-h2">{{ __("Connect with your potential customers through your brand") }}</h2>
          <p class="mu-body-l">{{ __('From someone who saw the advertisement to a potential customer') }}</p>
          <ul class="mu-checks">
            <li>{{ __("connect your custom domain") }}</li>
            <li>{{ __("Add the colors of your brand") }}</li>
            <li>{{ __("Use your custom fonts") }}</li>
          </ul>
        </div>
      </div>

      <div class="mu-chapter is-flip" data-reveal>
        <div class="mu-frame is-mint">
          <img loading="lazy" decoding="async"
            src="{{ url('images/landing/shipping-1200.webp') }}"
            srcset="{{ url('images/landing/shipping-720.webp') }} 720w, {{ url('images/landing/shipping-1200.webp') }} 1200w"
            sizes="(max-width: 991px) 100vw, 680px" width="1200" height="797" alt="">
        </div>
        <div class="mu-chapter-copy">
          <h2 class="mu-h2">{{ __("Analyzing data easily leads to profitable decisions") }}</h2>
          <ul class="mu-checks">
            <li>{{ __("Analyze your data by time period") }}</li>
            <li>{{ __("Generate data reports in a file") }}</li>
            <li>{{ __("Determine which stage you've reached") }}</li>
            <li>{{ __("Real answers for ease of selling") }}</li>
          </ul>
        </div>
      </div>

      <div class="mu-chapter" data-reveal>
        <div class="mu-frame">
          <img loading="lazy" decoding="async"
            src="{{ url('images/landing/marketing-1200.webp') }}"
            srcset="{{ url('images/landing/marketing-720.webp') }} 720w, {{ url('images/landing/marketing-1200.webp') }} 1200w"
            sizes="(max-width: 991px) 100vw, 680px" width="1200" height="1050" alt="">
        </div>
        <div class="mu-chapter-copy">
          <h2 class="mu-h2">{{ __("Tailor your approach to target your potential customer at their current stage") }}</h2>
          <p class="mu-body-l">{{ __("Target your potential customer at any time") }}</p>
          <ul class="mu-checks">
            <li>{{ __("Target them again to remind them of the offer or product") }}</li>
            <li>{{ __("Target them to increase sales") }}</li>
            <li>{{ __("Engage with them based on their information") }}</li>
            <li>{{ __("Maintain high loyalty among your customers") }}</li>
          </ul>
        </div>
      </div>

      <div class="mu-chapter is-flip" data-reveal>
        <div class="mu-frame is-mint">
          <img loading="lazy" decoding="async"
            src="{{ url('images/landing/mahally_smaller-1000.webp') }}"
            srcset="{{ url('images/landing/mahally_smaller-720.webp') }} 720w, {{ url('images/landing/mahally_smaller-1000.webp') }} 1000w"
            sizes="(max-width: 991px) 100vw, 680px" width="1000" height="1103" alt="">
        </div>
        <div class="mu-chapter-copy">
          <h2 class="mu-h2">{{ __("Focus on marketing and leave the selling to us") }}</h2>
          <p class="mu-body-l">{{ __("An assistant that helps you understand your customer wherever they are and on any platform, accurately, with real data, regardless of the type of content you deliver") }}</p>
          <ul class="mu-checks">
            <li>{{ __("Create sales journey with your strategies and your unique approach") }}</li>
            <li>{{ __("connect your sales journey to tracking codes") }}</li>
            <li>{{ __("Send automated messages with technical integration, no programming required") }}</li>
            <li>{{ __("Understanding the stage of your potential customer to facilitate decision-making") }}</li>
            <li>{{ __("Provide the appropriate answer to encourage them to buy from you") }}</li>
          </ul>
        </div>
      </div>

    </div>
  </section>

  {{-- 4. Partners --}}
  <section class="mu-partners" aria-labelledby="partners-title">
    <div class="mu-wrap">
      <div class="mu-partners-head" data-reveal>
        <h2 id="partners-title" class="mu-h2">{{ __("Partners trust Musaad") }}</h2>
      </div>
      @php
        $partners = [[220,81],[160,160],[360,125],[160,160],[360,103],[360,99],[160,160],[360,130],[360,135],[268,160],[360,94],[126,160],[360,131],[220,81]];
      @endphp
      <div class="mu-logos" data-reveal>
        @foreach ($partners as $i => $size)
          <div class="mu-logo">
            <img loading="lazy" decoding="async" src="{{ url('images/landing/partner-' . ($i + 1) . '.webp') }}" width="{{ $size[0] }}" height="{{ $size[1] }}" alt="">
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- 5. Close --}}
  <section class="mu-close" aria-labelledby="close-title">
    <div class="mu-wrap">
      <div class="mu-close-panel" data-reveal>
        <div>
          <h2 id="close-title" class="mu-h2">{{ __("Have a sales expert with Musaad") }}</h2>
          <p class="mu-body-l">{{ __("Create your sales journey, set your sales strategies, and let an Musaad help you with integrated solutions for continuous sales growth") }}</p>
        </div>
        <div class="mu-close-actions">
          <a href="{{ route("register") }}" class="mu-btn mu-btn-mint">
            {{ __("Create your account now") }}
            <svg aria-hidden="true"><use href="#i-arrow"/></svg>
          </a>
          <a href="{{ route("pricing") }}" class="mu-btn mu-btn-ghost">{{ __('Pricing') }}</a>
        </div>
      </div>
    </div>
  </section>

</main>

@endsection

@section('scripts')
<script>
  (function () {
    var items = document.querySelectorAll('[data-reveal]');
    if (!('IntersectionObserver' in window)) {
      for (var i = 0; i < items.length; i++) items[i].classList.add('is-in');
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('is-in');
          io.unobserve(e.target);
        }
      });
    }, { rootMargin: '0px 0px -40px 0px', threshold: 0.01 });
    for (var j = 0; j < items.length; j++) io.observe(items[j]);
  })();
</script>
@endsection
