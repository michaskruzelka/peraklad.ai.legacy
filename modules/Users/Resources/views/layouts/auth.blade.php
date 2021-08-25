<!DOCTYPE html>
<html class="no-js css-menubar" lang="be">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="robots" content="noindex, nofollow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@section('title')Субтытры@show</title>
	<link rel="apple-touch-icon" href="{{ Theme::asset('img/apple-touch-icon.png') }}">
	<link rel="shortcut icon" href="{{ Theme::asset('img/favicon.ico') }}">
	<!-- Stylesheets -->
	<link rel="stylesheet" href="{{ Theme::asset('css/bootstrap.extend.css') }}">
	<link rel="stylesheet" href="{{ Theme::asset('css/site.css') }}">
	<!-- Plugins -->
	<link rel="stylesheet" href="{{ asset('global/vendor/animsition/animsition.css') }}">
	<link rel="stylesheet" href="{{ asset('global/vendor/asscrollable/asScrollable.css') }}">
	<link rel="stylesheet" href="{{ asset('global/vendor/switchery/switchery.css') }}">
	<link rel="stylesheet" href="{{ asset('global/vendor/intro-js/introjs.css') }}">
	<link rel="stylesheet" href="{{ asset('global/vendor/slidepanel/slidePanel.css') }}">
	<link rel="stylesheet" href="{{ asset('global/vendor/flag-icon-css/flag-icon.css') }}">
	<link rel="stylesheet" href="{{ asset('global/vendor/toastr/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/formvalidation/formValidation.css') }}">
	<!-- Fonts -->
	<link rel="stylesheet" href="{{ Theme::asset('css/icons.css') }}">
	<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic&subset=latin,cyrillic'>
	<!--[if lt IE 9]>
	<script src="{{ asset('global/vendor/html5shiv/html5shiv.min.js') }}"></script>
	<![endif]-->
	<!--[if lt IE 10]>
	<script src="{{ asset('global/vendor/media-match/media.match.min.js') }}"></script>
	<script src="{{ asset('global/vendor/respond/respond.min.js') }}"></script>
	<![endif]-->
	<!-- Scripts -->
	<script src="{{ asset('global/vendor/modernizr/modernizr.min.js') }}"></script>
	<script src="{{ asset('global/vendor/breakpoints/breakpoints.min.js') }}"></script>
	<script src="{{ asset('global/vendor/jquery/jquery.js') }}"></script>
    <script src="{{ asset('global/vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('global/vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    <script src="{{ Theme::asset('js/core.js') }}"></script>
	<script src="{{ Theme::asset('js/site.js') }}"></script>
    <script src="{{ Module::asset('users:js/workshop.js') }}"></script>
	<script>
		Breakpoints();
	</script>
</head>
<body class="@yield('body-class') layout-full page-dark">
<!--[if lt IE 9]>
<p class="browserupgrade">Вы карыстаецеся ўстарэлым браўзерам. Калі ласка, <a href="http://browsehappy.com/">абнавіце свой браўзер</a>, каб пабачыць усе магчымасці сайта.</p>
<![endif]-->
<!-- Page -->
<div class="page animsition" data-animsition-in="fade-in" data-animsition-out="fade-out">
	<div class="page-content">
		<div class="page-brand-info">
			<div class="brand">
				<img class="brand-img">
				<h2 class="brand-text font-size-40">Belsub</h2>
			</div>
			<p class="font-size-20">Калектыўны пераклад субтытраў.</p>
		</div>
		<div class="page-login-main">
			<div class="brand visible-xs">
				<img class="brand-img">
				<h3 class="brand-text font-size-40">Belsub</h3>
			</div>

            @yield('form')

			<footer class="page-copyright">
				<p>© {{ date('Y') }} <a href="{{ url() }}">BelSub</a></p>
                <p>Crafted with <i class="red-600 wb wb-heart"></i> by @michaskruzelka</p>
				<div class="social">
					<a class="btn btn-icon btn-round social-twitter" href="javascript:void(0)">
						<i class="icon bd-twitter" aria-hidden="true"></i>
					</a>
					<a class="btn btn-icon btn-round social-facebook" href="javascript:void(0)">
						<i class="icon bd-facebook" aria-hidden="true"></i>
					</a>
				</div>
			</footer>
		</div>
	</div>
</div>
<!-- End Page -->
<!-- Core  -->
<script src="{{ asset('global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('global/vendor/asscroll/jquery-asScroll.js') }}"></script>
<script src="{{ asset('global/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ asset('global/vendor/asscrollable/jquery.asScrollable.all.js') }}"></script>
<script src="{{ asset('global/vendor/ashoverscroll/jquery-asHoverScroll.js') }}"></script>
<!-- Plugins -->
<script src="{{ asset('global/vendor/switchery/switchery.min.js') }}"></script>
<script src="{{ asset('global/vendor/intro-js/intro.js') }}"></script>
<script src="{{ asset('global/vendor/screenfull/screenfull.js') }}"></script>
<script src="{{ asset('global/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
<script src="{{ asset('global/vendor/toastr/toastr.js') }}"></script>
<!-- Scripts -->

<script src="{{ Theme::asset('js/sections.js') }}"></script>
<script src="{{ Theme::asset('js/configs.js') }}"></script>
<script src="{{ Theme::asset('js/components.js') }}"></script>
<script>
	(function(document, window, $) {
		'use strict';
		var Site = window.Site;
		$(document).ready(function() {
			Site.run();
		});
	})(document, window, jQuery);
</script>
@stack('bottom-scripts')
</body>
</html>