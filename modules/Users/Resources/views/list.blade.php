@extends(Theme::layoutPath())

@push('styles')
<link rel="stylesheet" href="{{ asset('global/vendor/select2/select2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('global/vendor/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('global/vendor/select2/i18n/be.js') }}"></script>
<script src="{{ Module::asset('users:js/workshop.js') }}"></script>
@endpush

@push('bottom-scripts')
<script src="{{ asset('global/vendor/amcharts/amcharts.js') }}"></script>
<script src="{{ asset('global/vendor/amcharts/serial.js') }}"></script>
<script src="{{ asset('global/vendor/amcharts/pie.js') }}"></script>
<script src="{{ asset('global/vendor/amcharts/themes/light.js') }}"></script>
<script src="{{ asset('global/vendor/amcharts/lang/be.js') }}"></script>
@endpush

@section('title') Перакладчыкі | @parent @endsection

@section('page-aside')
    @include('users::list.page-aside')
@endsection

@section('page-title')
    Перакладчыкі
@endsection

@section('page-content')
    @include('users::list.page-content')
@endsection
