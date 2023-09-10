@extends('layout.master')

@section('styles')
  @vite(["resources/sass/home.scss"])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')

  <div class="intro">
    <div class="bg"></div>
    <div class="container">
      <div class="text-center header-padding">
        <h2 class="title h1">{{ __("مساعد ... مساعدك الذكي بسهولة") }}</h2>
        <p class="lead hero-width desc">{{ __('مساعد هو موظف المبيعات الخاص فيك الذي يطبق إستراتيجياتك ويزيد من مبيعاتك اون لاين
          يعمل 24 ساعة و365 يوم بالسنة لتحقيق المبيعات بشكل سهل وسريع وبدون تعقيد
          ') }}</p>
        <div class="mt-4">
          <button class="btn btn-outline">{{ __("تسجيل الدخول") }}</button>
          <button class="btn">{{ __("أنشئ مساعدك الذكي الآن") }}</button>
        </div>
      </div>
      <img class="mw-100 mt-2 p-5" src="{{ url("/images/intro.png") }}" alt="">
    </div>
  </div>

  <div class="section-1">
    <div class="container">
      <div class="text-center">
        <h2 class="title h1">{{ __("أول مساعد ذكي عربي في الشرق الأوسط") }}</h2>
        <p class="lead hero-width desc">{{ __('أنشئ مساراتك البيعية وانضم لقصص النجاح مع مساعد اليوم') }}</p>
        <div class="mt-4 d-flex boxes mt-5">
          <div class="box">
            <div class="icon"><i class="fa-solid fa-shop"></i></div>
            <div class="d-flex flex-column">
              <h2 class="m-0 title">+3000</h2>
              <p class="lead m-0">{{__('مسار بيعي') }}</p>
            </div>
          </div>
          <div class="d-flex box">
            <div class="icon"><i class="fa fa-solid fa-money-bill"></i></div>
            <div class="d-flex flex-column">
              <h2 class="m-0 title">+3000</h2>
              <p class="lead m-0">{{__('عميل محتمل') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-2 bg-light">
    <div class="container">
      <div class="text-center">
        <p class="lead desc">{{ __('مهما كان نوع المنتج أو الخدمة اللي بتبيعها بتقدر تعتمد على مساعد') }}</p>
        <div class="mt-4 d-flex shops mt-5">
          <div class="row w-100">
            <div class="col-4">
              <div class="shop">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("تجارة الكترونية") }}</h2>
                  <p class="lead m-0">{{__('تقدر تبني مسار بيعي لاختيار افضل منتجات او منتج لعميلك المحتمل') }}</p>
                </div>
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
            <div class="col-4">
              <div class="shop">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("دورات تدريبية") }}</h2>
                  <p class="lead m-0">{{__('اعمله اختبار بسيط لتزيد من جودة العميل المحتمل ورفع رغبة الشراء لديه') }}</p>
                </div>
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
            <div class="col-4">
              <div class="shop">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("منتجات رقمية") }}</h2>
                  <p class="lead m-0">{{__('قدم للعميل المحتمل افضل عرض ليقوم بالشراء مباشرة') }}</p>
                </div>
                <div class="icon"><i class="fa-solid fa-shop"></i></div>
              </div>
            </div>
            <div class="col-4">
              <div class="shop">
                <div class="d-flex flex-column">
                  <h2 class="m-0 title">{{ __("خدمات") }}</h2>
                  <p class="lead m-0">{{__('افهم عميلك المحتمل بشكل اكبر وقدم له الحل والعرض المناسب لتزيد من نسبة اهتمامه') }}</p>
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
        <div class="col-6">
          <img class="w-100" src="{{ url("images/languages.png") }}" alt="">
        </div>
        <div class="col-1"></div>
        <div class="col-5">
          <div class="content">
            <h2 class="m-0 title mb-2">{{ __("أوصل لعملائك المحتملين بعلامتك التجارية") }}</h2>
            <p class="lead m-0 mt-2 mb-3">{{__('من شخص شاهد الإعلان لعميل محتمل ') }}</p>
            <ul>
              <li>{{ __("ربط الدومين الخاص بك") }}</li>
              <li>{{ __("إضافة الوان علامتك التجارية") }}</li>
              <li>{{ __("استخدم الخطوط الخاصة فيك") }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-4 bg-light">
    <div class="container">
      <div class="row">
        <div class="col-6">
          <img class="w-100" src="{{ url("images/shipping.png") }}" alt="">
        </div>
        <div class="col-1"></div>
        <div class="col-5 text-start">
          <div class="content">
            <h2 class="m-0 mb-3 title">{{ __("تحليل البيانات بسهولة = قرارات مربحة") }}</h2>
            <ul>
              <li>{{ __("حلل بيناتك حسب فترة زمنية") }}</li>
              <li>{{ __("استخرج تقارير بالبيانات على ملف") }}</li>
              <li>{{ __("اعرف لأي مرحلة وصل") }}</li>
              <li>{{ __("إجابات حقيقية لسهولة البيع") }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section-5">
    <div class="container">
      <div class="row">
        <div class="col-6 text-start">
          <div class="content">
            <h2 class="m-0 mb-3 title">{{ __("عيد استهداف عميلك المحتمل بمرحلته الحالية") }}</h2>
            <p class="lead m-0 mt-2 mb-3">{{ __("استهدف عميلك المحتمل في أي وقت") }}</p>
            <ul>
              <li>{{ __("عيد استهدافه لتذكيره بالعرض او المنتج") }}</li>
              <li>{{ __("استهدفه لزيادة المبيعات") }}</li>
              <li>{{ __("تواصل معاه بناءاً على معلوماته") }}</li>
              <li>{{ __("ابقى عملائك بولاء عالي لك") }}</li>
            </ul>
          </div>
        </div>
        <div class="col-6">
          <img class="w-100" src="{{ url("images/marketing.png") }}" alt="">
        </div>
      </div>
    </div>
  </div>

  <div class="section-6">
    <div class="container">
      <div class="row">
        <div class="col-6 text-start">
          <div class="content">
            <h2 class="m-0 mb-3 title">{{ __("ركز على التسويق واترك البيع علينا ") }}</h2>
            <p class="lead m-0 mt-2 mb-3">{{ __("مساعد يساعدك بفهم عميلك وين ما كان وعلى أي منصة بشكل دقيق وببيانات حقيقية ومهما كان نوع المحتوى اللي تقدم عن طريق") }}</p>
            <ul>
              <li>{{ __("انشاء مسارات بيعية بإستراتيجياتك وطريقتك الخاصة") }}</li>
              <li>{{ __("ربط مساراتك البيعية بأكواد تتبع") }}</li>
              <li>{{ __("ارسال له رسائل اوتوماتيكية بربط تقني بدون برمجة") }}</li>
              <li>{{ __("فهم مرحلة عميلك المحتمل لتسهل اتخاذ القرار عليه") }}</li>
              <li>{{ __("اعطي الجواب المناسب له عشان يشتري منك") }}</li>
            </ul>
          </div>
        </div>
        <div class="col-6">
          <img class="w-100" src="{{ url("images/mahally_smaller.png") }}" alt="">
        </div>
      </div>
    </div>
  </div>

  <div class="section-7">
    <div class="container">
      <h1 class="title text-center">{{ __("علامات تثق بمساعد") }}</h1>
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
          <div class="col-9">
            <h2 class="title">{{ __("امتلك موظف مبيعات خبير في مساعد") }}</h2>
            <p class="lead">{{ __("انشى مسارك البيعي وضع إستراتيجياتك البيعي ودع مساعد يساعدك في المبيعات مع حلول متكاملة لنمو مبيعاتك بشكل دائم") }}</p>
          </div>
          <div class="col-3">
            <button class="btn btn-outline-primary w-100">{{ __("أنشئ حسابك الآن") }}</button>
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
