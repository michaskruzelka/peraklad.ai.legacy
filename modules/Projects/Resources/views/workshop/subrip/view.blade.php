@extends(Theme::layoutPath())

@push('styles')
<link rel="stylesheet" href="{{ asset('global/vendor/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('global/vendor/ace/ace.css') }}">
<link rel="stylesheet" href="{{ asset('global/vendor/ladda-bootstrap/ladda.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('global/vendor/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('global/vendor/select2/i18n/be.js') }}"></script>
<script src="{{ Module::asset('projects:js/workshop.js') }}"></script>
@endpush

@push('bottom-scripts')
<script src="{{ asset('global/vendor/ace/ace.js') }}"></script>
<script src="{{ asset('global/vendor/ladda-bootstrap/spin.js') }}"></script>
<script src="{{ asset('global/vendor/ladda-bootstrap/ladda.js') }}"></script>
@endpush

@section('title') Файл субтытраў | {{ $release->getRipName() }} | @parent @endsection

@section('page-aside')
    @include('projects::workshop.subrip.view.page-aside')
@endsection

@section('page-title')
    Файл субтытраў - {{ $release->getRipName() }}
@endsection

@section('page-content')

    <div class="modal fade modal-slide-from-bottom" id="regenerateSubRipModal" aria-hidden="false" role="dialog" tabindex="-1" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Вы ўпэўнены?</h4>
                </div>
                <div class="modal-body">
                    <p>Усе вашыя змены над файлам будуць страчаныя.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Не</button>
                    <button type="button" class="btn btn-primary" id="regenerateSubRipConfirmed">Упэўнены</button>
                </div>
            </div>

        </div>
    </div>

    <a href="{{ route('workshop::projects::my') }}">
        <button type="button" data-toggle="tooltip" title="Мае праекты" class="site-action site-floataction btn-raised btn btn-primary btn-floating">
            <i class="front-icon wb-arrow-left animation-scale-up" aria-hidden="true"></i>
        </button>
    </a>
    @include('projects::workshop.subrip.view.page-content')
@endsection
