@extends(Theme::layoutPath())

@push('styles')
    <link rel="stylesheet" href="{{ asset('global/vendor/ladda-bootstrap/ladda.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/formvalidation/formValidation.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/cropper/cropper.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/footable/footable.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('global/vendor/depends-on/dependsOn-1.0.2.min.js') }}"></script>
    <script src="{{ asset('global/vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('global/vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    <script src="{{ asset('global/vendor/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('global/vendor/select2/i18n/be.js') }}"></script>
    <script src="{{ asset('global/vendor/cropper/cropper.min.js') }}"></script>
    <script src="{{ asset('global/vendor/footable/footable.all.min.js') }}"></script>
    <script src="{{ Module::asset('projects:js/workshop.js') }}"></script>
@endpush

@push('bottom-scripts')
    <script src="{{ asset('global/vendor/ladda-bootstrap/spin.js') }}"></script>
    <script src="{{ asset('global/vendor/ladda-bootstrap/ladda.js') }}"></script>
@endpush

@section('title') Новы праект | @parent @endsection

@section('page-aside')
    @include('projects::workshop.create.page-aside')
@endsection

@section('page-title')
    Новы праект
@endsection

@section('page-content')
    <a href="{{ URL::previous() }}">
        <button type="button" data-toggle="tooltip" title="Вярнуцца" class="site-action site-floataction btn-raised btn btn-primary btn-floating">
            <i class="front-icon wb-arrow-left animation-scale-up" aria-hidden="true"></i>
        </button>
    </a>
    @include('projects::workshop.create.page-content')
@endsection
