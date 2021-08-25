<!DOCTYPE html>
<html class="no-js" lang="be">
<head>
    @include('remark::partials.head')
</head>
<body class="page-aside-fixed site-menubar-fold site-menubar-keep">
    @include('remark::partials.after_body_start')
    <nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">
        @include('remark::partials.header_navbar')
    </nav>
    <div class="site-menubar">
        @include('remark::partials.left_navbar')
    </div>
    <div class="page animsition">
        <div class="page-aside" id="page-aside">
            @include('remark::partials.page_aside')
        </div>
        <div class="page-main" id="page-main">
            @include('remark::partials.page_main')
        </div>
    </div>
    <footer class="site-footer">
        @include('remark::partials.footer')
    </footer>
    @include('remark::partials.before_body_end')
</body>
</html>
