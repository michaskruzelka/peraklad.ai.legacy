<div class="navbar-header">
    <button type="button" class="navbar-toggle hamburger hamburger-close navbar-toggle-left hided"
            data-toggle="menubar">
        <span class="sr-only">Toggle navigation</span>
        <span class="hamburger-bar"></span>
    </button>
    <button type="button" class="navbar-toggle collapsed" data-target="#site-navbar-collapse"
            data-toggle="collapse">
        <i class="icon wb-more-horizontal" aria-hidden="true"></i>
    </button>
    <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
        <img class="navbar-brand-logo" src="{{ Theme::asset('img/logo.png') }}" title="Belsub">
        <span class="navbar-brand-text"> Belsub</span>
    </div>
    <button type="button" class="navbar-toggle collapsed" data-target="#site-navbar-search"
            data-toggle="collapse">
        <span class="sr-only">Toggle Search</span>
        <i class="icon wb-search" aria-hidden="true"></i>
    </button>
</div>
<div class="navbar-container container-fluid">
    <!-- Navbar Collapse -->
    <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
        <!-- Navbar Toolbar -->
        <ul class="nav navbar-toolbar">
            <li class="hidden-float" id="toggleMenubar">
                <a data-toggle="menubar" href="#" role="button">
                    <i class="icon hamburger hamburger-arrow-left">
                        <span class="sr-only">Toggle menubar</span>
                        <span class="hamburger-bar"></span>
                    </i>
                </a>
            </li>
            <li class="hidden-xs" id="toggleFullscreen">
                <a class="icon icon-fullscreen" data-toggle="fullscreen" href="#" role="button">
                    <span class="sr-only">Toggle fullscreen</span>
                </a>
            </li>
            @if(isset($leftModules))
                @include('modules', ['modules' => $leftModules])
            @endif
        </ul>
        <!-- End Navbar Toolbar -->
        <!-- Navbar Toolbar Right -->
        <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
            @if(isset($rightModules))
                @include('modules', ['modules' => $rightModules])
            @endif
        </ul>
        <!-- End Navbar Toolbar Right -->
    </div>
    @if(isset($afterModules))
        @include('modules', ['modules' => $afterModules])
    @endif
</div>