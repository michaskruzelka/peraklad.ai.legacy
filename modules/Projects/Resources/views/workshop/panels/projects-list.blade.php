@if ($releases->count() > 0)

    <table class="table">
        <thead>
        <tr>
            <th>Статус</th>
            <th>Рэліз</th>
            <th>Гатоўнасць</th>
            <th>Удзельнікі</th>
            <th>Дзеянні</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($releases as $release)
            @include('projects::workshop.panels.projects-list.' . $release->getState(), compact('release'))
        @endforeach
        </tbody>
    </table>

    <?php $currentState = is_null(request()->route('state')) ? 'all' : request()->route('state'); ?>

    @if (config('projects.releasesLimitPerPage') < $releases->count())
        <div class="text-right">
            <ul class="pagination">
                <?php $initPage = $page < 3 ? 1 : $page-2; ?>
                <li @if($page == 1) class="disabled" @endif>
                    <a href="{{ route('workshop::releases::refreshReleasesList', [
                        'userId' => $usId,
                        'in' => $in,
                        'mode' => $mode,
                        'page' => $page-1,
                        'state' => $currentState,
                        'search' => request()->get('search'),
                        'year' => request()->get('year'),
                        'lang' => request()->get('lang')
                    ]) }}" aria-label="Назад">
                        <span aria-hidden="true">«</span>
                    </a>
                </li>
                @for ($i = $initPage; $i < $initPage+5; $i++)
                    @if ($page == $i)
                        <li class="active">
                            <a>{{ $i }} <span class="sr-only">(current)</span></a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('workshop::releases::refreshReleasesList', [
                                'userId' => $usId,
                                'in' => $in,
                                'mode' => $mode,
                                'page' => $i,
                                'state' => $currentState,
                                'search' => request()->get('search'),
                                'year' => request()->get('year'),
                                'lang' => request()->get('lang')
                            ]) }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endif
                    @if ($releases->count() <= $i*config('projects.releasesLimitPerPage'))
                        <?php break; ?>
                    @endif
                @endfor
                <li @if(($page*config('projects.releasesLimitPerPage')) >= $releases->count()) class="disabled" @endif>
                    <a href="{{ route('workshop::releases::refreshReleasesList', [
                        'userId' => $usId,
                        'in' => $in,
                        'mode' => $mode,
                        'page' => $page+1,
                        'state' => $currentState,
                        'search' => request()->get('search'),
                        'year' => request()->get('year'),
                        'lang' => request()->get('lang')
                    ]) }}" aria-label="Далей">
                        <span aria-hidden="true">»</span>
                    </a>
                </li>
            </ul>
        </div>
    @endif

    <script>
        (function(document, window, $) {
            'use strict';

            initProjectsList();

        })(document, window, jQuery);
    </script>

@else

    <p>Няма праектаў</p>

@endif

