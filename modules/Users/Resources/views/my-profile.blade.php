@extends(Theme::layoutPath())

@push('styles')
<link rel="stylesheet" href="{{ asset('global/vendor/formvalidation/formValidation.css') }}">
<link rel="stylesheet" href="{{ asset('global/vendor/cropper/cropper.css') }}">
<link rel="stylesheet" href="{{ asset('global/fonts/brand-icons/brand-icons.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('global/vendor/formvalidation/formValidation.min.js') }}"></script>
<script src="{{ asset('global/vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
<script src="{{ asset('global/vendor/cropper/cropper.min.js') }}"></script>
<script src="{{ Module::asset('users:js/workshop.js') }}"></script>
@endpush

@section('title') Мой профіль | @parent @endsection

@section('page-aside')
    @include('users::my-profile.page-aside')
@endsection

@section('page-title')
    Мой профіль - {{ $user->getId() }}
@endsection

@section('page-content')
    @include('users::my-profile.page-content')
@endsection
