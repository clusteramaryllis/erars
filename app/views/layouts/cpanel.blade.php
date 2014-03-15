<!doctype html>
<!--[if IE 8]>    <html class="no-js ie8 ie"> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9 ie"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title')</title>
	@section('top_css')
	@show
	@section('bottom_css')
	<link rel="stylesheet" href="{{ asset('packages/bootstrap/css/bootstrap.min.css') }}">	
	<link rel="stylesheet" href="{{ asset('packages/font-awesome/css/font-awesome.min.css') }}">	
	@show
	@section('top_js')
	<script src="{{ asset('packages/jquery/jquery-1.11.0.min.js') }}"></script>
	<script src="{{ asset('packages/jquery/jquery-migrate-1.2.1.min.js') }}"></script>
	<!--[if lt IE 9]>
		<script src="{{ asset('packages/html5shiv/html5shiv.js') }}"></script>
		<script src="{{ asset('packages/respond/respond.min.js') }}"></script>
	<![endif]-->
	@show
</head>
<body class="@yield('body_class')">
	@include('partials.cpanel.header')
	@yield('content')
	@include('partials.cpanel.footer')
	@section('bottom_js')
	<script src="{{ asset('packages/bootstrap/js/bootstrap.min.js') }}"></script>
	@show
</body>
</html>