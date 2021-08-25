<div class="site-menubar-body">
    @if(isset($topModules))
        @include('modules', ['modules' => $topModules])
    @endif
</div>
<div class="site-menubar-footer">
    @if(isset($bottomModules))
        @include('modules', ['modules' => $bottomModules])
    @endif
</div>