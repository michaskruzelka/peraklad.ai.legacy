<div class="panel panel-bordered">
    <div class="panel-heading">
        <h3 class="panel-title">Рэлізы</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="true" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::releases::refreshReleasesForm', ['project' => $project->getInfo()->getSlug()]) }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @include('projects::workshop.panels.releases-form')
    </div>
</div>
<div class="panel panel-bordered">
    <div class="panel-heading">
        <h3 class="panel-title">Фільм</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-minus" aria-expanded="false" data-toggle="panel-collapse"
               aria-hidden="true"></a>
            <a class="panel-action icon wb-expand" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            <a class="panel-action icon wb-refresh"
               data-toggle="panel-refresh"
               data-load-type="round-circle"
               data-load-callback="panelRefreshCallback"
               data-url="{{ route('workshop::projects::refreshUpdateForm', ['project' => $project->getInfo()->getSlug()]) }}"
               aria-hidden="true"></a>
        </div>
    </div>
    <div class="panel-body">
        @if ($project->belongsToYou())
            @include('projects::workshop.panels.project-form')
        @else
            @include('projects::workshop.panels.project-form-tenant')
        @endif
    </div>
</div>
@if ($project->belongsToYou())
<div class="panel panel-bordered is-collapse">
    <div class="panel-heading">
        <h3 class="panel-title">Загрузіць плакат</h3>
        <div class="panel-actions">
            <a class="panel-action icon wb-plus" aria-expanded="true" data-toggle="panel-collapse"
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
@endif
@push('bottom-scripts')
    <script>
        @if (Session::has('flash_notification.message'))
            $( window ).load(function() {
                toastr.{{ Session::get('flash_notification.level') }}('{{ Session::get('flash_notification.message') }}');
            });
        @endif
    </script>
@endpush