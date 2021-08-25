<div class="panel panel-bordered" id="my-project-list">
    <div class="panel-heading">
        <ul class="panel-info">
            @foreach($statesInfo as $state => $stateInfo)
                <li>
                    <div class="num {{ $stateInfo['color'] }}-600">
                        <a href="{{ route($routeName, $routeBaseParams + [
                            'in' => $in,
                            'mode' => $mode,
                            'page' => 1,
                            'state' => $state,
                            'search' => request()->get('search'),
                            'year' => request()->get('year'),
                            'lang' => request()->get('lang')
                        ]) }}">
                            @if (isset($stateInfo['count'])) {{ $stateInfo['count'] }} @else 0 @endif
                        </a>
                    </div>
                    <p><a href="{{ route($routeName, $routeBaseParams + [
                        'in' => $in,
                        'mode' => $mode,
                        'page' => 1,
                        'state' => $state,
                        'search' => request()->get('search'),
                        'year' => request()->get('year'),
                        'lang' => request()->get('lang')
                    ]) }}">
                            {{ $stateInfo['title'] }}
                    </a></p>
                </li>
            @endforeach
        </ul>
        <?php $currentState = is_null(request()->route('state')) ? 'all' : request()->route('state'); ?>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::releases::refreshReleasesList', [
                    'user' => $usId,
                    'in' => $in,
                    'mode' => $mode,
                    'page' => 1,
                    'state' => $currentState,
                    'search' => request()->get('search'),
                    'year' => request()->get('year'),
                    'lang' => request()->get('lang')
               ]) }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('projects::workshop.panels.projects-list')
    </div>
</div>
@if ($releases->count() > 0)
<script>
    $(window).load(function() {
        AppProjects.handleSlidePanel();
        AppProjects.handleSelective();
        $(panel).find('.wb-stats-bars').tooltip();
        $(panel).find('.addMember-item').tooltip();
    });
    var panel = $('#my-project-list');
    $(panel).on('loading.done.uikit.panel', function() {
        AppProjects.handleSelective();
        $(this).find('[data-toggle="tooltip"]').tooltip();
        $(panel).find('.wb-stats-bars').tooltip();
    });
</script>
@endif
