<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      @yield('meta')
      <title>{{ config("app.name") }}</title>

      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
      <link href="https://fonts.bunny.net/css?family=readex-pro:300,400,500,600,700&display=swap" rel="stylesheet">

      <style>
        :root {
          --direction: {{ app()->getLocale() == "ar" ? "rtl": "ltr" }};
        }
      </style>

      <!-- Bootstrap 5.2.3 -->
      @if(app()->getLocale() == "ar")
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">
      @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
      @endif

      <!-- Styles -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      @vite(['resources/sass/dashboard.scss', 'resources/sass/ui.scss'])

      @yield('styles')

  </head>
  <body>

    @include('layout.app-header')

    @include('layout.template-navbar')


    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4 bg-light">
          @yield('options')
        </div>
        <div class="col-md-8 px-0">
          @yield('content')
        </div>
      </div>
    </div>

    <!-- Jquery 3.7.0 -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    <!-- Bootstrap 5.2.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

    @yield('scripts')

  </body>
</html>
