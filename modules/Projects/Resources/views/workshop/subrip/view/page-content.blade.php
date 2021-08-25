<div class="panel panel-bordered" id="subRipFileContent">
    <div class="panel-heading">
        <h3 class="panel-title">@if ($abc == 'cy') Кірыліца @else Лацінка @endif</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::releases::subrip::refreshSubRipPanel', [
                    'releaseId' => $release->getId(),
                    'format' => $abc
               ]) }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('projects::workshop.panels.subrip.file')
    </div>
</div>
