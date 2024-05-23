@extends('layout.master')

@section('meta')
  @php $page_title = __('Musaad , Create an AI sales rep that improves and automates lead generation') @endphp
@endsection

@section('styles')
  @vite(["resources/sass/home.scss"])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')

  <div class="intro">
    <div class="bg"></div>
    <div class="container">
      <div class="text-center header-padding">
        <h2 class="title h1">{{ __("Musaad , Create an AI sales rep that improves and automates lead generation") }}</h2>
        <p class="lead hero-width desc">{{ __('Musaad is your dedicated sales employee who implements your strategies and increases your online sales. He works 24 hours a day, 365 days a year, to achieve sales easily, quickly, and without complications.') }}</p>
        <div class="mt-4">
          <a href="{{ route("login") }}" class="btn btn-outline">{{ __("Log in") }}</a>
          <a href="{{ route("pricing") }}" class="btn">{{ __("Create your smart Musaad now") }}</a>
        </div>
      </div>
      <img class="mw-100 mt-2 p-5" src="{{ url("images/intro.png") }}" alt="">
    </div>
  </div>

  <div class="section-1" id="section-1">
    <div class="container">
      <div class="text-center">
        <h2 class="title h1">{{ __("The first Arabic smart assistant in the Middle East") }}</h2>
        <p class="lead hero-width desc">{{ __('Create your sales paths and join success stories with Assistant Today') }}</p>
        <div class="mt-4 d-flex boxes mt-5">
          <div class="row text-md-center text-end">
            <div class="col-md-6">
              <div class="box">
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">+3000</h2>
                  <p class="lead m-0">{{__('Customer Acquisition Cost Less 96%') }}</p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="box">
                <div class="icon"><i class="fa fa-solid fa-money-bill"></i></div>
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">+9000</h2>
                  <p class="lead m-0">{{__('Prospective customer') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-2 bg-light">
    <div class="container">
      <div class="text-center">
        <p class="lead desc">{{ __("No matter the type of product or service you're selling, you can rely on Musaad") }}</p>
        <div class="mt-4 d-flex shops mt-5">
          <div class="row w-100">
            <div class="col-lg-6 col-xl-4">
              <div class="shop justify-content-end">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("E-commerce") }}</h2>
                  <p class="lead m-0">{{__('You can build a sales journey to select the best product or products for your potential customer') }}</p>
                </div>
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
            <div class="col-lg-6 col-xl-4">
              <div class="shop justify-content-end">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("Training courses") }}</h2>
                  <p class="lead m-0">{{__('Create a simple test to improve the quality of the prospective customer and boost their purchase intent') }}</p>
                </div>
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
            <div class="col-lg-6 col-xl-4">
              <div class="shop justify-content-end">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("Digital products") }}</h2>
                  <p class="lead m-0">{{__('Offer the prospective customer the best deal to encourage them to make an immediate purchase') }}</p>
                </div>
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
            <div class="col-lg-6 col-xl-4">
              <div class="shop justify-content-end">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("Services") }}</h2>
                  <p class="lead m-0">{{__('Understand your potential customer better and provide them with the appropriate solution and offer to increase their interest') }}</p>
                </div>
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-3">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <img class="w-100" src="{{ url("images/languages.png") }}" alt="">
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
          <div class="content">
            <h2 class="m-0 title mb-2">{{ __("Connect with your potential customers through your brand") }}</h2>
            <p class="lead m-0 mt-2 mb-3">{{__('From someone who saw the advertisement to a potential customer') }}</p>
            <ul>
              <li>{{ __("connect your custom domain") }}</li>
              <li>{{ __("Add the colors of your brand") }}</li>
              <li>{{ __("Use your custom fonts") }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-4 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <img class="w-100" src="{{ url("images/shipping.png") }}" alt="">
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5 text-start">
          <div class="content">
            <h2 class="m-0 mb-3 title">{{ __("Analyzing data easily leads to profitable decisions") }}</h2>
            <ul>
              <li>{{ __("Analyze your data by time period") }}</li>
              <li>{{ __("Generate data reports in a file") }}</li>
              <li>{{ __("Determine which stage you've reached") }}</li>
              <li>{{ __("Real answers for ease of selling") }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-5">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-start">
          <div class="content">
            <h2 class="m-0 mb-3 title">{{ __("Tailor your approach to target your potential customer at their current stage") }}</h2>
            <p class="lead m-0 mt-2 mb-3">{{ __("Target your potential customer at any time") }}</p>
            <ul>
              <li>{{ __("Target them again to remind them of the offer or product") }}</li>
              <li>{{ __("Target them to increase sales") }}</li>
              <li>{{ __("Engage with them based on their information") }}</li>
              <li>{{ __("Maintain high loyalty among your customers") }}</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <img class="w-100" src="{{ url("images/marketing.png") }}" alt="">
        </div>
      </div>
    </div>
  </div>

  <div class="section-6">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-start">
          <div class="content">
            <h2 class="m-0 mb-3 title">{{ __("Focus on marketing and leave the selling to us") }}</h2>
            <p class="lead m-0 mt-2 mb-3">{{ __("An assistant that helps you understand your customer wherever they are and on any platform, accurately, with real data, regardless of the type of content you deliver") }}</p>
            <ul>
              <li>{{ __("Create sales journey with your strategies and your unique approach") }}</li>
              <li>{{ __("connect your sales journey to tracking codes") }}</li>
              <li>{{ __("Send automated messages with technical integration, no programming required") }}</li>
              <li>{{ __("Understanding the stage of your potential customer to facilitate decision-making") }}</li>
              <li>{{ __("Provide the appropriate answer to encourage them to buy from you") }}</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <img class="w-100" src="{{ url("images/mahally_smaller.png") }}" alt="">
        </div>
      </div>
    </div>
  </div>

  <div class="section-7">
    <div class="container">
      <h1 class="title text-center">{{ __("Partners trust Musaad") }}</h1>
      <div class="row align-items-center logos">
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item1.png") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item2.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item3.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item4.png") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item5.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item6.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item7.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item8.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item9.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item10.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item11.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item12.webp") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item13.png") }}" alt="">
        </div>
        <div class="col-2 logo">
          <img class="w-75 mx-auto" src="{{ url("images/item14.png") }}" alt="">
        </div>
      </div>
    </div>
  </div>

  <div class="section-8">
    <div class="container">
      <div class="alert alert-success" role="alert">
        <div class="row align-items-center">
          <div class="col-md-9">
            <h2 class="title">{{ __("Have a sales expert with Musaad") }}</h2>
            <p class="lead">{{ __("Create your sales journey, set your sales strategies, and let an Musaad help you with integrated solutions for continuous sales growth") }}</p>
          </div>
          <div class="col-md-3 mt-md-0 mt-3">
            <a href="{{ route("login") }}" class="btn btn-outline-primary w-100">{{ __("Create your account now") }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')

  <script>

    // Used to toggle the menu on small screens when clicking on the menu button
    function myFunction() {
      var x = document.getElementById("navDemo");
      if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
      } else {
        x.className = x.className.replace(" w3-show", "");
      }
    }

  </script>
@endsection
