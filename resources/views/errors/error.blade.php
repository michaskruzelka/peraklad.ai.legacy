<!DOCTYPE html>
<html class="no-js css-menubar" lang="be">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>@section('title')Памылка@show</title>
    <link rel="apple-touch-icon" href="{{ Theme::asset('img/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ Theme::asset('img/favicon.ico') }}">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ Theme::asset('css/bootstrap.extend.css') }}">
    <link rel="stylesheet" href="{{ Theme::asset('css/site.css') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/toastr/toastr.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ Theme::asset('css/icons.css') }}">
    <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic&subset=latin,cyrillic'>
    <!--[if lt IE 9]>
    <script src="{{ asset('global/vendor/html5sh    iv/html5shiv.min.js') }}"></script>
    <![endif]-->
    <!--[if lt IE 10]>
    <script src="{{ asset('global/vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ asset('global/vendor/respond/respond.min.js') }}"></script>
    <![endif]-->
    <!-- Scripts -->
    <script src="{{ asset('global/vendor/breakpoints/breakpoints.min.js') }}"></script>
    <script>
        Breakpoints();
    </script>
    <style>
        .page-error header h1 {
            font-size: 10em;
            font-weight: 400;
        }
        .page-error header p {
            margin-bottom: 30px;
            font-size: 30px;
            text-transform: uppercase;
        }
        .page-error h2 {
            margin-bottom: 30px;
        }
        .page-error .error-advise {
            margin-bottom: 25px;
            color: #a9afb5;
        }
    </style>
</head>
<body class="page-error page-error-404 layout-full">
<!-- Page -->
<div class="page animsition vertical-align text-center" data-animsition-in="fade-in"
     data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle">
        <header>
            <h1 class="animation-slide-top">@yield('head')</h1>
            <p>@yield('body')</p>
        </header>
        <p class="error-advise">@yield('advise')</p>
        <a class="btn btn-primary btn-round" href="{{ url() }}">ГАЛОЎНАЯ СТАРОНКА</a>
        <footer class="page-copyright">
            <p>© {{ date('Y') }} <a href="{{ url() }}">BelSub</a></p>
            <p>Crafted with <i class="red-600 wb wb-heart"></i> by @michaskruzelka</p>
            <div class="social">
                <a href="javascript:void(0)">
                    <i class="icon bd-twitter" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0)">
                    <i class="icon bd-facebook" aria-hidden="true"></i>
                </a>
            </div>
        </footer>
    </div>
</div>
<!-- Core  -->
<script src="{{ asset('global/vendor/jquery/jquery.js') }}"></script>
<script src="{{ asset('global/vendor/jquery/jquery.js') }}"></script>
<script src="{{ Theme::asset('js/core.js') }}"></script>
<script src="{{ Theme::asset('js/site.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('global/vendor/toastr/toastr.js') }}"></script>
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