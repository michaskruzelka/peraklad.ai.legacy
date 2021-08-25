@if ($subtitle->getRelease()->belongsToYou())
@if ($subtitle->getRelease()->isCompleted())
    <section class="page-aside-section">
        <div class="list-group">
            <div class="list-group-item">
                <button id="completeRelease"
                        type="submit"
                        class="btn btn-primary btn-block"
                        data-url="{{ route('workshop::releases::backToEdit', ['release' => $subtitle->getRelease()->getId()]) }}"
                >
                    <i class="icon wb-pencil margin-right-10" aria-hidden="true"></i>Рэдагаваць субтытры
                </button>
            </div>
        </div>
    </section>
@else
    <section class="page-aside-section">
        <div class="list-group">
            <div class="list-group-item">
                <button id="completeRelease"
                        type="submit"
                        class="btn btn-primary btn-block"
                        data-url="{{ route('workshop::releases::complete', ['release' => $subtitle->getRelease()->getId()]) }}"
                >
                    <i class="icon wb-check margin-right-10" aria-hidden="true"></i>Завяршыць праект
                </button>
            </div>
        </div>
    </section>
@endif
@endif
<section class="page-aside-section">
    <div class="list-group">
        <span class="list-group-item">
            <div id="slider-limit"></div>
            <script>
                $(document).ready(function() {
                    var updateTimingUrl = '{{ route('workshop::subtitles::updateTiming', ['subtitle' => $subtitle->getId()]) }}';
                    var isEditable = {{ (int) ($subtitle->isEditable() && ! $subtitle->getRelease()->isCompleted()) }};
                    var startMin = {{ $subtitle->getTimeRange()->getBottomLine(true) }};
                    var startMax = {{ $subtitle->getTimeRange()->getTopLine(true) }};
                    var rangeMin = {{ $minRange }};
                    var rangeMax = {{ $maxRange }};
                    initTimeRangeSlider(updateTimingUrl, isEditable, startMin, startMax, rangeMin, rangeMax);
                });
            </script>
        </span>
    </div>
</section>

<section class="page-aside-section" id="subtitlesSearch">
    <div class="list-group">
        <div class="list-group-item">
            <div class="form-group">
                <select class="form-control" id="statuses-list-selector">
                    <option value="{{ route('workshop::subtitles::view', [
                        'releaseId' => $subtitle->getRelease()->getId(),
                        'status' => 'all'
                    ]) }}" @if ($status == 'all') selected @endif>Усе</option>
                    <option value="{{ route('workshop::subtitles::view', [
                        'releaseId' => $subtitle->getRelease()->getId(),
                        'status' => 'cl'
                    ]) }}" @if ($status == 'cl') selected @endif>Новыя</option>
                    <option value="{{ route('workshop::subtitles::view', [
                        'releaseId' => $subtitle->getRelease()->getId(),
                        'status' => 'un'
                    ]) }}" @if ($status == 'un') selected @endif>Перакладаюцца</option>
                    <option value="{{ route('workshop::subtitles::view', [
                        'releaseId' => $subtitle->getRelease()->getId(),
                        'status' => 'sa'
                    ]) }}" @if ($status == 'sa') selected @endif>Гатовыя</option>
                </select>
            </div>
            <form method="get" action="{{ route('workshop::subtitles::view', [
                'releaseId' => $subtitle->getRelease()->getId(),
                'status' => $status
            ]) }}">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" maxlength="100" class="form-control" name="search" value="{{ request()->get('search') }}" placeholder="Увядзіце тэкст...">
                        <span class="input-group-btn">
                          <button type="submit" class="btn btn-primary"><i class="wb-search" aria-hidden="true"></i></button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
        @foreach ($subtitles as $i => $sub)
            @if ($sub == $subtitle)
                <a href="javascript:void(0)">
                    <div class="list-group-item active {{ $sub->getColor() }}">
                        {{ $sub->getNumber() }}. {{ $sub->strictFormatText($sub->getOriginalText() ? $sub->getOriginalText() : ($sub->isViewable() ? $sub->getTranslatedText() : 'СХАВАНЫ ПЕРАКЛАД')) }}
                    </div>
                </a>
            @else
                <a href="{{ route('workshop::subtitles::view', [
                    'releaseId' => $subtitle->getRelease()->getId(),
                    'status' => $status,
                    'search' => request()->get('search'),
                    'n' => $sub->getNumber()
                ]) }}">
                    <div class="list-group-item {{ $sub->getColor() }}">
                        {{ $sub->getNumber() }}. {{ $sub->strictFormatText($sub->getOriginalText() ? $sub->getOriginalText() : ($sub->isViewable() ? $sub->getTranslatedText() : 'СХАВАНЫ ПЕРАКЛАД')) }}
                    </div>
                </a>
            @endif
        @endforeach
        <ul class="pager">
            @if ($prevGroupItem)
                <li title="Папярэднія"><a href="{{ route('workshop::subtitles::view', [
                    'releaseId' => $subtitle->getRelease()->getId(),
                    'status' => $status,
                    'search' => request()->get('search'),
                    'n' => $prevGroupItem->getNumber()
                ]) }}"><span aria-hidden="true">←</span></a></li>
            @else
                <li title="Папярэднія" class="disabled">
                    <a href="javascript:void(0)"><span aria-hidden="true">←</span></a>
                </li>
            @endif
            @if ($nextGroupItem)
                <li title="Наступныя"><a href="{{ route('workshop::subtitles::view', [
                    'releaseId' => $subtitle->getRelease()->getId(),
                    'status' => $status,
                    'search' => request()->get('search'),
                    'n' => $nextGroupItem->getNumber()
                ]) }}"><span aria-hidden="true">→</span></a></li>
            @else
                <li title="Наступныя" class="disabled">
                    <a href="javascript:void(0)"><span aria-hidden="true">→</span></a>
                </li>
            @endif
        </ul>
    </div>
</section>

<section class="page-aside-section">
    <h5 class="page-aside-title">Тлумачэнні</h5>
    <div class="list-group">
        <span class="list-group-item">

        </span>
    </div>
</section>

<script>
    var select2Config = {
        width: "style",
        language: "be",
        minimumResultsForSearch: "Infinity"
    };
    $('#statuses-list-selector').select2(select2Config).on('change', function(e) {
        e.preventDefault();
        $(location).attr("href", $(e.currentTarget).val());
    });
    $('#completeRelease').on('click', function() {
        $(location).attr("href", $(this).attr('data-url'));
    });
</script>