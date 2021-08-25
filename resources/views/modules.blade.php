@unless (empty($modules))
    @foreach($modules as $module)
        {!! $module !!}
    @endforeach
@endunless