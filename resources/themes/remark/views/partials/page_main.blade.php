<div class="page-header">
    <h1 class="page-title" id="page-title">
        @yield('page-title')
    </h1>
    <div class="page-header-actions">
        @if(isset($mainActionsModules))
            @include('modules', ['modules' => $mainActionsModules])
        @endif
    </div>
</div>
<div class="page-content" id="page-content">
    @yield('page-content')
</div>