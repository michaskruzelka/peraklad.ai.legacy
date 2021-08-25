<div class="panel panel-bordered" id="translationSubtitle">
    <div class="panel-heading">
        <h3 class="panel-title">Пераклад</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::subtitles::refreshTranslationPanel', [
                    'releaseId' => $subtitle->getRelease()->getId(),
                    'status' => $status,
                    'n' => $number,
                    'search' => request()->get('search')
               ]) }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('projects::workshop.panels.subtitles.translation')
    </div>
</div>

<ul class="blocks-xs-100 blocks-md-2 blocks-xlg-2">
    <li class="subtitle">
        <div class="panel panel-bordered animation-scale-up" id="versions-panel" style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 0ms;">
            <div class="panel-heading">
                <h3 class="panel-title">Варыянты</h3>
                <div class="panel-actions">
                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
                       aria-hidden="true"></a>
                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                    <a class="panel-action icon wb-refresh"
                       data-toggle="panel-refresh"
                       data-load-type="round-circle"
                       data-load-callback="panelRefreshCallback"
                       data-url="{{ route('workshop::subtitles::refreshVersionsPanel', [
                            'subtitle' => $subtitle->getId()
                       ]) }}"
                       aria-hidden="true"></a>
                </div>
            </div>
            <div class="panel-body">
                @include('projects::workshop.panels.subtitles.versions')
            </div>
        </div>
    </li>
    <li class="subtitle">
        <div class="panel panel-bordered animation-scale-up" id="comments-panel" style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 50ms;">
            <div class="panel-heading">
                <h3 class="panel-title">Каментарыі</h3>
                <div class="panel-actions">
                    <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
                       aria-hidden="true"></a>
                    <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
                    <a class="panel-action icon wb-refresh"
                       data-toggle="panel-refresh"
                       data-load-type="round-circle"
                       data-load-callback="panelRefreshCallback"
                       data-url="{{ route('workshop::subtitles::refreshCommentsPanel', [
                            'subtitle' => $subtitle->getId()
                       ]) }}"
                       aria-hidden="true"></a>
                </div>
            </div>
            <div class="panel-body">
                @include('projects::workshop.panels.subtitles.comments')
            </div>
        </div>
    </li>
</ul>

<script>
    (function(document, window, $) {
        'use strict';

        var panel = $('add-subtitle-comment').parents('.panel');
        $('#versions-panel, #comments-panel').on('loading.done.uikit.panel', function () {
            $(this).find('[data-toggle="tooltip"]').tooltip();
        });

    })(document, window, jQuery);

</script>