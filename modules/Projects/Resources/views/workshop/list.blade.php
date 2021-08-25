@extends(Theme::layoutPath())

@section('title') {{ $title }} | @parent @endsection

@section('page-aside')
    @include('projects::workshop.list.page-aside')
@endsection

@section('page-title')
    {{ $title }}
@endsection

@section('page-content')
    <a href="{{ route('workshop::projects::new') }}">
        <button type="button" data-toggle="tooltip" title="Новы праект" class="site-action site-floataction btn-raised btn btn-primary btn-floating">
            <i class="front-icon wb-plus animation-scale-up" aria-hidden="true"></i>
        </button>
    </a>
    @include('projects::workshop.list.page-content')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('global/vendor/jquery-selective/jquery-selective.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/jquery-labelauty/jquery-labelauty.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/select2/select2.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('global/vendor/jquery-selective/jquery-selective.min.js') }}"></script>
    <script src="{{ asset('global/vendor/jquery-labelauty/jquery-labelauty.js') }}"></script>
    <script src="{{ asset('global/vendor/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('global/vendor/select2/i18n/be.js') }}"></script>
    <script src="{{ Module::asset('projects:js/workshop.js') }}"></script>
@endpush

@push('bottom-scripts')
    <script src="{{ asset('global/vendor/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('global/vendor/amcharts/serial.js') }}"></script>
    <script src="{{ asset('global/vendor/amcharts/pie.js') }}"></script>
    <script src="{{ asset('global/vendor/amcharts/themes/light.js') }}"></script>
    <script src="{{ asset('global/vendor/amcharts/lang/be.js') }}"></script>
@endpush