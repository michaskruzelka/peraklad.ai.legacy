<section class="page-aside-section">
    <div class="list-group">
        @if ($release->belongsToYou())
        <div class="list-group-item">
            <button id="regenerateSubRip"
                    type="button"
                    class="btn btn-primary btn-block ladda-button"
                    data-style="zoom-in"
                    data-url="{{ route('workshop::releases::subrip::regenerate', [
                        'release' => $release->getId(),
                        'format' => $abc
                    ]) }}"
            >
                <span class="ladda-label">
                    <i class="icon wb-reload margin-right-10" aria-hidden="true"></i>Рэгенерыраваць
                </span>
            </button>
        </div>
        @endif
        <div class="list-group-item">
            <div class="form-group">
                <label for="abc-list-selector">Алфавіт</label>
                <select class="form-control" id="abc-list-selector">
                    <option value="{{ route('workshop::releases::subrip::view', [
                        'release' => $release->getId(),
                        'format' => 'cy'
                    ]) }}" @if ('cy' == $abc) selected @endif>Кірыліца</option>
                    <option value="{{ route('workshop::releases::subrip::view', [
                        'release' => $release->getId(),
                        'format' => 'la'
                    ]) }}" @if ('la' == $abc) selected @endif>Лацінка</option>
                </select>
            </div>
            <div class="form-group">
                <label for="formats-list-selector">Фармат</label>
                <select class="form-control" name="format" id="formats-list-selector">
                    <option value="srt">SubRip (*.srt)</option>
                </select>
            </div>
            <form action="{{ route('workshop::releases::subrip::download', [
                'release' => $release->getId(),
                'format' => $abc
            ]) }}" method="post">
                <div class="form-group">
                    <label for="charsets-list-selector">Кадзіроўка</label>
                    <select name="charset" class="form-control" id="charsets-list-selector">
                    @foreach ($charsets as $charset)
                        <option value="{{ $charset }}">{{ strtoupper($charset) }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="nl-list-selector">Перанос радка</label>
                    <select name="nl" class="form-control" id="nl-list-selector">
                    @foreach ($newLineFormats as $formatKey => $formatValue)
                        <option value="{{ $formatKey }}">{{ $formatValue }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button id="downloadSubRip"
                            type="submit"
                            class="btn btn-primary btn-block"
                            @unless($release->isDownloadable()) disabled="disabled" @endunless
                    >
                        <i class="icon wb-download margin-right-10" aria-hidden="true"></i>Спампаваць
                    </button>
                </div>
                {!! Form::token() !!}
            </form>
        </div>
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
    $('#abc-list-selector').select2(select2Config).on('change', function(e) {
        e.preventDefault();
        $(location).attr("href", $(e.currentTarget).val());
    });
    $('#charsets-list-selector').select2(select2Config);
    $('#nl-list-selector').select2(select2Config);
    $('#formats-list-selector').select2(select2Config);

    $('#regenerateSubRip').on('click', function() {
        var elem = this;
        var l = Ladda.create(this);
        l.start();

        var modal = $('#regenerateSubRipModal');
        modal.modal('toggle');

        $(modal).on('hidden.bs.modal', function () {
            l.stop();
        });

        $(modal).find('#regenerateSubRipConfirmed').on('click', function() {
            var errorText = 'Нешта не так';
            $.ajax({
                url: $(elem).attr('data-url'),
                type: 'GET',
                success: function(result) {
                    if ( ! result.status || result.status != 'ok') {
                        toastr.warning(result.response, errorText);
                        return false;
                    }
                    setTimeout(function() {
                        toastr.success(result.response);
                    }, 500);
                    var refreshEl = $('#subRipFileContent').find('[data-toggle="panel-refresh"]').get(0);
                    $(refreshEl).trigger('click');
                },
                error: function(result) {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                    l.stop();
                },
                complete: function() {
                    l.stop();
                    modal.modal('hide');
                }
            });
        });

    });

</script>