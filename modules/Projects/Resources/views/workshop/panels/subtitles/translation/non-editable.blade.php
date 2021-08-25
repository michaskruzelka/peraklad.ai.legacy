@if ($subtitle->isViewable())

@if ($subtitle->getTranslatedText() && $subtitle->isSaved())
    <div class="well">
        {!! $subtitle->formatText($subtitle->getTranslatedText()) !!}
    </div>
@else
    <div class="well red-600">
        НЯМА ПЕРАКЛАДУ
    </div>
@endif

@else

    <div class="well red-600">
        НАЖАЛЬ, ПРАГЛЯД ПЕРАКЛАДУ ЗАБАРОНЕНЫ ЎЛАДАЛЬНІКАМ
    </div>

@endif