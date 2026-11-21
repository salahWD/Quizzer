<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <!-- All Of This Project Is Made By The Legend (Salah) -->
    <!-- To Contact Me: -->
    <!-- Github: https://github.com/salahWD -->
    <!-- Instagram: https://instagram.com/salah_backhach/ -->
    <!-- Telegram: https://t.me/salahbakhash -->
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @yield('meta')
        <title>{{ isset($page_title)? $page_title : __("Musaad , Create an AI sales rep") }}</title>

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
        @vite(['resources/sass/app.scss', 'resources/sass/ui.scss'])

        @yield('styles')

    </head>
    <body class="mu-public">

      @include('layout.header')

      @yield('content')

      <!-- Bootstrap 5.2.3 -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

      @yield('scripts')

    </body>
</html>
