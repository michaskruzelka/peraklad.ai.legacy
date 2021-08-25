<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="[DESCRIPTION]">
<meta name="keywords" content="[KEYWORDS]">
<meta name="author" content="Michaś Kruželka">
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
<link rel="stylesheet" href="{{ asset('global/fonts/ionicons/ionicons.css') }}">
<link rel="stylesheet" href="{{ asset('global/vendor/toastr/toastr.css') }}">
@stack('styles')
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
<script src="{{ Theme::asset('js/core.js') }}"></script>
<script src="{{ Theme::asset('js/site.js') }}"></script>
<script>
    Breakpoints();
</script>
@stack('scripts')
