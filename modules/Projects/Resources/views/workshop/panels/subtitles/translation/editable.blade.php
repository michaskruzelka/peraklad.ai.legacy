<div id="translatedEditor" class="well"> {!! $subtitle->formatText($subtitle->getTranslatedText()) !!}</div>
@if ($subtitle->isSaved())
    <div class="text-right">
    {!! Form::button('Змяніць', [
        'type' => 'button',
        'class' => 'btn btn-primary',
        'id' => 'edit-translation',
        'data-url' => route('workshop::subtitles::edit', ['subtitle' => $subtitle->getId()])
    ]) !!}
    {!! Form::button('Захаваць', [
        'type' => 'button',
        'class' => 'btn btn-primary hidden',
        'id' => 'save-translation',
        'data-url' => route('workshop::subtitles::save', ['subtitle' => $subtitle->getId()])
    ]) !!}
    </div>
@else
    <div class="text-right">
    {!! Form::button('Змяніць', [
        'type' => 'button',
        'class' => 'btn btn-primary hidden',
        'id' => 'edit-translation',
        'data-url' => route('workshop::subtitles::edit', ['subtitle' => $subtitle->getId()])
    ]) !!}
    {!! Form::button('Захаваць', [
        'type' => 'button',
        'class' => 'btn btn-primary',
        'id' => 'save-translation',
        'data-url' => route('workshop::subtitles::save', ['subtitle' => $subtitle->getId()])
    ]) !!}
    </div>
@endif


<script>
    var underSaveUrl = '{{ route('workshop::subtitles::underSave', ['subtitle' => $subtitle->getId()]) }}';
    initSubtitleTranslation(underSaveUrl, {{ (int) ! $subtitle->isSaved() }});
</script>
