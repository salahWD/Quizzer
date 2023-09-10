@extends('layout.master')

@section('content')
  <div class="d-flex justify-content-center align-items-center mt-5 mb-5">
    <div class="col-md-4">
      <div class="border border-3 border-danger"></div>
      <div class="card  bg-white shadow p-5">
        <div class="mb-4 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="75" height="75" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
        </div>
        <div class="text-center">
          <h1 class="text-danger">Error Occurred !</h1>
          <p>somthing went wrong, please try agin or contact us</p>
          <a href="{{ route("home") }}" class="btn btn-danger">{{ __('Home') }}</a>
        </div>
      </div>
    </div>
  </div>
@endsection
