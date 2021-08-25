@extends(Theme::layoutPath())

@push('styles')
<link rel="stylesheet" href="{{ asset('global/vendor/formvalidation/formValidation.css') }}">
<link rel="stylesheet" href="{{ asset('global/vendor/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('global/vendor/summernote/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('global/vendor/nouislider/nouislider.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('global/vendor/formvalidation/formValidation.min.js') }}"></script>
<script src="{{ asset('global/vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
<script src="{{ asset('global/vendor/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('global/vendor/select2/i18n/be.js') }}"></script>
<script src="{{ asset('global/vendor/summernote/summernote.js') }}"></script>
<script src="{{ asset('global/vendor/summernote/lang/summernote-be-BE.js') }}"></script>
<script src="{{ Module::asset('projects:js/workshop.js') }}"></script>
@endpush

@push('bottom-scripts')
<script src="{{ asset('global/vendor/nouislider/nouislider.min.js') }}"></script>
@endpush

@section('title') Субтытры | {{ $subtitle->getRelease()->getMovieTranslatedName() }} | @parent @endsection

@section('page-aside')
    @include('projects::workshop.subtitles.view.page-aside')
@endsection

@section('page-title')
    Субтытры - {{ $subtitle->getRelease()->getMovieTranslatedName() }} - {{ $subtitle->getNumber() }}
@endsection

@section('page-content')
    <a href="{{ route('workshop::projects::my') }}">
        <button type="button" data-toggle="tooltip" title="Мае праекты" class="site-action site-floataction btn-raised btn btn-primary btn-floating">
            <i class="front-icon wb-arrow-left animation-scale-up" aria-hidden="true"></i>
        </button>
    </a>
    @include('projects::workshop.subtitles.view.page-content')
@endsection
