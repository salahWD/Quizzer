@extends("layout.app-master")

@section("styles")
<style>
  .mw-750 {
    width: 700px;
    margin: auto;
    margin-top: 40px;
  }
  .img {
    max-width: 55px;
    object-fit: cover;
    object-position: center center;
  }
</style>
@endsection

@section("content")
<div class="container">
  <div class="needs-validation form">
    <fieldset class="mw-750">
      <form method="POST" action="{{ route('website_update_custom_domain', ['website' => $website->id]) }}">
        @csrf

        @if(session()->has('success') && session('success') == true)
          <p class="alert alert-success">
            {{ __('Success! edits has been saved') }}
          </p>
        @endif

        @if(auth()->user()->can_custom_domain())
          <label class="form-label" for="custom_domain">{{ __('Custom Domain') }}</label>
          <div class="input-group mb-3">
            <input id="custom_domain" name="custom_domain" class="form-control" type="text" value="{{ $website->custom_domain }}" placeholder="{{ __('enter: custom domain') }}">
            <div class="input-group-append">
              <button class="btn btn-outline-danger" name="custom_domain" type="submit" value=""><i class="fa fa-trash"></i></button>
            </div>
          </div>
        @endif

        @if($website->domain_status !== null)
          @if($website->domain_status == 1)
            <p class="alert alert-warning">
              {{ __("domain is under verification, will inform you once it's done") }}
            </p>
          @elseif($website->domain_status == 2)
            <p class="alert alert-success">
              {{ __('this domain') }} <b>({{ $website->custom_domain }})</b> {{ __('Has Been Approved By The Admin') }}
            </p>
          @else
            <p class="alert alert-danger">
              {{ __('this domain') }} <b>({{ $website->custom_domain }})</b> {{ __('has been rejected by the admin') }}
            </p>
          @endif
        @endif
        <iframe width="560" height="315" src="https://www.youtube.com/embed/rBM8FaWlzBk?si=-Jv2tE5yEAiAI0nw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        <p class="lead mt-4"><b>Add DNS Record To Your Service Provider:</b></p>
        <table class="table border text-center">
          <thead class="table-dark">
            <tr>
              <th scope="col">TYPE</th>
              <th scope="col">HOST</th>
              <th scope="col">VALUE</th>
              <th scope="col">TTL</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">A</th>
              <td>@</td>
              <td>51.89.104.24</td>
              <td>Automatic</td>
            </tr>
          </tbody>
        </table>

        <button type="submit" class="btn btn-primary mb-3 m-auto px-5">{{ __('Save') }}</button>
      </form>
    </fieldset>
  </div>
</div>
@endsection
