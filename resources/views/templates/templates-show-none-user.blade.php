
{{--
  this page will be included
  so don't add any [@extends] here
  --}}

{{-- Random Image --}}
{{-- https://source.unsplash.com/1200x600/ --}}

@vite(['resources/sass/templates-for-nonuser.css'])

<section class="bg-white">
  <div class="container">
    <div class="row align-children">
      <div class="col-md-7 col-sm-6 text-center mb-xs-24" style="order: 1">
        <img class="img-responsive" alt="Get Unlimited Quizzes" src="https://149842022.v2.pressablecdn.com/wp-content/uploads/sites/59/2016/03/macbook-preview-flexible.png">
      </div>
      <div class="col-md-4 col-md-offset-1 col-sm-5 col-sm-offset-1">
        <div class="">
          <h3>Get Unlimited Quizzes</h3>
          <div class="mb32">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fuga dolores obcaecati quidem aut, incidunt ea in?</p>
          </div>
          <a class="btn btn-lg btn-filled" href="https://google.com">let's see</a>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container mt-5">
  <div class="row">
    <!-- CARD 1-->
    @foreach ($templates as $template)
      <div class="col-lg-4">
        <a href='{{ route("preview_template", $template->id) }}' class="url-box">
          <figure class='newsCard news-Slide-up' style="aspect-ratio: 2/1">
            @if ($template->intro_image != null)
              <img class="card-image border w-100" src="{{ url("images/uploads/$template->intro_image") }}">
            @else
              <img style="position: absolute; inset:0;opacity: 0.55" src="https://source.unsplash.com/400x250/?background"/>
              <div class="card-image border" style="aspect-ratio: 2/1;background-color: {{ $template->background_color }}"></div>
            @endif
            <div class='newsCaption px-4'>
              <div class="d-flex align-items-center justify-content-between cnt-title">
                <h5 class='newsCaption-title text-white m-0'>{{ $template->name ?? "Template Title" }}</h5>
                <i class="fas fa-arrow-alt-circle-right "></i>
              </div>
              <div class='newsCaption-content d-flex'>
                <p class="col-10 py-3 px-0">{{ $template->template_desc }}</p>
              </div>
            </div>
            <span class="overlay"></span>
          </figure>
        </a>
      @endforeach
    </div>
  </div>
</div>
