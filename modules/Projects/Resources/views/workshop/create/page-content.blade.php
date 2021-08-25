<div class="panel panel-bordered">
    <div class="panel-heading">
        <h3 class="panel-title">Фільм</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::projects::refreshCreateForm') }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('projects::workshop.panels.project-form', compact('languages', 'topLangsNum'))
    </div>
</div>
<div class="panel panel-bordered" id="panel-poster">
    <div class="panel-heading">
        <h3 class="panel-title">Загрузіць плакат</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::projects::refreshUploadPoster') }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('projects::workshop.panels.upload-poster')
    </div>
</div>
