<nav>
    <ul class="pager">
        <li class="previous @if ( ! $prevSubtitle) disabled @endif">
            <a href="{{ $prevSubtitle ? route('workshop::subtitles::view', [
                'releaseId' => $subtitle->getRelease()->getId(),
                'status' => $status,
                'n' => $prevSubtitle->getNumber(),
                'search' => request()->get('search')
            ]) : 'javascript:void(0)' }}">
                <span aria-hidden="true">←</span> Папярэдні
            </a>
        </li>
        <li class="next @if ( ! $nextSubtitle) disabled @endif">
            <a href="{{ $nextSubtitle ? route('workshop::subtitles::view', [
                'releaseId' => $subtitle->getRelease()->getId(),
                'status' => $status,
                'n' => $nextSubtitle->getNumber(),
                'search' => request()->get('search')
            ]) : 'javascript:void(0)' }}">Наступны <span aria-hidden="true">→</span></a>
        </li>
    </ul>
</nav>

@if (Session::has('flash_notification.message'))
    <div class="well well-{{ Session::get('flash_notification.level') }}">
        {{ Session::get('flash_notification.message') }}
    </div>
@endif

@if ($subtitle->getOriginalText())
    <div class="well">{!! $subtitle->formatText($subtitle->getOriginalText()) !!}</div>
@else
    <div class="well red-600">
        НЯМА АРЫГІНАЛЬНАГА ТЭКСТУ
    </div>
@endif

@if ($subtitle->isEditable() &&  ! $subtitle->getRelease()->isCompleted())
    @include('projects::workshop.panels.subtitles.translation.editable')
@else
    @include('projects::workshop.panels.subtitles.translation.non-editable')
@endif

@if($subtitle->isViewable())
<br />
<div class="panel-group panel-group-simple margin-bottom-0" aria-multiselectable="true" role="tablist">
    <div class="panel">
        <div class="panel-heading" id="historyHeadingOne" role="tab">
            <h4><a class="panel-title collapsed" data-toggle="collapse" href="#historyCollapseOne" aria-controls="historyCollapseOne" aria-expanded="false">
                    Гісторыя
                </a></h4>
        </div>
        <div class="panel-collapse collapse" id="historyCollapseOne" aria-labelledby="historyHeadingOne" role="tabpanel" aria-expanded="false" style="height: 0px;">
            @foreach ($subtitle->getHistory() as $event)
                <p class="small">
                    {{ $event->getCreatedAt()->format('j/n/Y') }} - <a href="">{{ $event->getUserId() }}</a> {!! $event->represent() !!}
                </p>
            @endforeach
        </div>
    </div>
</div>
@endif