<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/dist/images/smart center logo.png') }}">
  <link rel="shortcut icon" type="image/png" href="{{ asset('/dist/images/smart center logo.png') }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') | سمارت سنتر لإدارة الدروس الخصوصية</title>
  <meta name="_token" content="{{ csrf_token() }}">
  <link href="{{ asset('/material/css/fontawesome.css') }}" rel="stylesheet">

  <!-- CSS Files -->
  <link href="{{ asset('material') }}/css/material-dashboard.css?v=2.1.1" rel="stylesheet">
  <link href="{{ asset('material') }}/css/material-dashboard-rtl.css?v=1.1" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('material/css/custom.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
  @yield('css')
</head>

<body class="{{ $class ?? '' }}">
  @auth()
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
    @include('admin.layouts.page_templates.auth')
  @endauth
  @guest()
    @include('admin.layouts.page_templates.guest')
  @endguest

  <!-- JS Files -->
  <script src="{{ asset('material') }}/js/core/jquery.min.js"></script>
  <script src="{{ asset('material') }}/js/core/popper.min.js"></script>
  <script src="{{ asset('material') }}/js/core/bootstrap-material-design.min.js"></script>
  <script src="{{ asset('material') }}/js/plugins/moment.min.js"></script>
  <script src="{{ asset('material/js/plugins/bootstrap-bundle.js') }}"></script>
  <script src="{{ asset('material') }}/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script>
  <script src="{{ asset('dist/js/notify.min.js') }}"></script>
  @stack('js')
  @yield('js')
</body>
</html>