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