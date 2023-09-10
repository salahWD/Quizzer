@extends('layout.master')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-1"></div>
      <div class="col-10">
        <div id="generic_price_table">
          <section>
            <div class="container">
              <div class="row">
                <div class="col-md-12">
                  <div class="price-heading clearfix">
                    <h1>Pricing Table</h1>
                  </div>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="row">
                @foreach($plans as $plan)
                  <div class="col-md-4">
                    <div class="generic_content clearfix @if($plan->best_seller == 1) active @endif">
                      <div class="generic_head_price clearfix">
                        <div class="generic_head_content clearfix">
                          <div class="head_bg"></div>
                          <div class="head"><span>{{ $plan->name }}</span></div>
                        </div>
                        <div class="generic_price_tag clearfix">
                          <span class="price">
                            <span class="sign">$</span>
                            <span class="currency">{{ $plan->price }}</span>
                            <span class="month">/MON</span>
                          </span>
                        </div>
                      </div>
                      <div class="generic_feature_list">
                        <ul>
                          <li><span>{{ $plan->package()["WEBSITES"] }}</span> website accounts</li>
                          <li>
                            <span style="margin-right: 5px">{{ $plan->package()["RESPONSES"] }}</span>
                            responses / month
                          </li>
                          @if($plan->package()["TRANSLATABLE"] == 1)
                            <li><span>Allow</span> Translation</li>
                          @else
                            <li><span>One Language</span> is Allowed</li>
                          @endif
                          <li><span>UTM</span> Web Tracking</li>
                          <li><span>All</span> Of The Other Features</li>
                        </ul>
                      </div>
                      <div class="generic_price_btn clearfix">
                        <a href=
                        "@if(!auth()->check())
                            {{ route("login") }}
                          @elseif(auth()->user()->is_trailing() || !auth()->user()->took_trail())
                            {{ route("change_package", $plan->id) }}
                          @else
                            {{ route("offer", $plan->id) }}
                          @endif"
                        >Sign up</a>
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
