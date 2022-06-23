<!DOCTYPE html>
<html lang="ar">
<head>
	<meta charset="utf-8" />
	<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/dist/images/smart center logo.png') }}">
	<link rel="shortcut icon" type="image/png" href="{{ asset('/dist/images/smart center logo.png') }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>
		@yield('title')
	</title>
	<meta name="_token" content="{{ csrf_token() }}">
	<!--     Fonts and icons     -->
	<link href="{{ asset('/material/css/fontawesome.css') }}" rel="stylesheet">
	<!-- CSS Files -->
	<link href="{{ asset('/dist/css/bootstrap.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('material/css/custom.css') }}">
	<link rel="stylesheet" href="{{ asset('dist/css/parents.css') }}">
	<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
	@yield('css')
</head>
<body>
	@include('parents.layouts.navbar')

	@yield('content')

	<script src="{{ asset('material') }}/js/core/jquery.min.js"></script>
	<script src="{{ asset('dist/js/notify.min.js') }}"></script>
	<script src="{{ asset('/dist/js/bootstrap.min.js') }}"></script>
	@yield('js')
</body>

</html>