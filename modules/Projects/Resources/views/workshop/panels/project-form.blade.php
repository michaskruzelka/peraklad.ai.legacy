@if( ! $project->getId())
    {!! Form::open([
        'class' => 'form-horizontal',
        'autocomplete' => 'off',
        'id' => 'projects-list'
    ]) !!}
    <div class="form-group">
        {!! Form::label('projects-list-selector', 'Існуючыя праекты', array('class' => 'col-sm-3 control-label')) !!}
        <div class="col-sm-9">
            <select class="form-control" id="projects-list-selector"></select>
        </div>
    </div>
    <script>
        (function() {
            projectKeySearch("{{ route('workshop::projects::keysearch') }}");
        })();
    </script>
    {!! Form::close() !!}
@endif

{!! Form::open([
    'route' => $project->getId() ? 'workshop::projects::update' : 'workshop::projects::store',
    'method' => $project->getId() ? 'put' : 'post',
    'class' => 'form-horizontal',
    'id' => 'project-store',
    'autocomplete' => 'off'
]) !!}
<div class="form-group">
    {!! Form::label('translated_title', 'Назва па-беларуску*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'translated_title',
            $project->getInfo()->getTranslatedTitle(),
            ['class' => 'form-control', 'maxlength' => '100']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('title', 'Арыгінальная назва*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-6">
        {!! Form::text(
            'original_title',
            $project->getInfo()->getOriginalTitle(),
            ['class' => 'form-control', 'maxlength' => '100', 'id' => 'title']
        ) !!}
    </div>
    <div class="col-sm-3">
        <button data-search-key="title" type="button" class="btn btn-primary ladda-button imdb" data-style="zoom-in">
            <span class="ladda-label">
                <i class="icon wb-search margin-right-10" aria-hidden="true"></i>IMDb
            </span>
        </button>
    </div>
</div>
<div class="form-group">
    {!! Form::label('imdb-id', 'IMDB', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-6">
        {!! Form::text(
            'imdb',
            $project->getInfo()->getImdbId(),
            ['class' => 'form-control', 'maxlength' => '20', 'id' => 'imdb-id']
        ) !!}
    </div>
    <div class="col-sm-3">
        <button data-search-key="imdb-id" type="button" class="btn btn-primary ladda-button imdb" data-style="zoom-in">
            <span class="ladda-label">
                <i class="icon wb-search margin-right-10" aria-hidden="true"></i>IMDb
            </span>
        </button>
    </div>
</div>
<div class="form-group">
    {!! Form::label('score', 'Рэйтынг', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'score',
            $project->getInfo()->getImdbRating(),
            ['class' => 'form-control', 'maxlength' => '3', 'id' => 'score']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('plot', 'Сюжэт', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::textarea(
            'plot',
            $project->getInfo()->getPlot(),
            ['class' => 'form-control', 'rows' => 5, 'id' => 'plot']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('poster', 'Плакат', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9" id="poster-image">
        <img src="{{ $project->getInfo()->getPoster()->getSrc() }}" />
        {!! Form::hidden(
            'poster',
            $project->getInfo()->getPoster()->getSrc(),
            ['class' => 'form-control', 'id' => 'poster-hidden']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('lang', 'Мова арыгінала*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        <select id="lang" name="lang" class="form-control">
            <optgroup>
                {{ $i=1 }}
                @foreach($languages as $language)
                    <option value="{{ $language->getId() }}"
                        @if($language->getId() == $project->getInfo()->getLanguage()->getIso6393b()) selected @endif
                    >{{ $language->getBelName() }}</option>
                    @if ($i == $topLangsNum)
            </optgroup>
            <optgroup>
                @endif
                {{ $i++ }}
                @endforeach
            </optgroup>
        </select>
    </div>
</div>
<div class="form-group">
    {!! Form::label('movie-type', 'Тып*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        <select name="movie-type" id="movie-type" class="form-control" @if($project->getId()) disabled @endif>
            <option value="movie" @if('movie' == $project->getType()) selected @endif>фільм</option>
            <option value="series" @if('series' == $project->getType()) selected @endif>серыял</option>
        </select>
    </div>
</div>
<div class="form-group" id="form-group-year">
    {!! Form::label('year', 'Год*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">

        {!! Form::text(
            'year',
            $project->getInfo()->getYear(),
            ['class' => 'form-control', 'maxlength' => '4', 'id' => 'year']
        ) !!}
    </div>
</div>

<div id="movie-series">
    <button type="button" id="add-ser"  class="btn btn-outline btn-primary btn-sm center-block">
        <i class="icon wb-plus" aria-hidden="true"></i> Дадаць серыю
    </button><br />
    <table id="series-table" class="table table-bordered table-hover toggle-circle"
           data-page-size="10">
        <thead>
        <tr>
            <th data-sort-ignore="true" class="min-width">Сезон</th>
            <th data-sort-ignore="true" class="min-width">Серыя</th>
            <th data-sort-ignore="true">Арыгінальная назва</th>
            <th data-sort-ignore="true">Назва па-беларуску</th>
            <th data-sort-ignore="true" class="min-width">Год</th>
            <th data-sort-ignore="true" class="min-width"></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="hide-if-no-paging">
        <tr>
            <td colspan="6">
                <div class="text-right">
                    <ul class="pagination"></ul>
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

@if ($project->getId())
    {!! Form::hidden('id', $project->getId()) !!}

    <div class="modal fade modal-slide-from-bottom" id="removeProjectModal" aria-hidden="false" role="dialog" tabindex="-1" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Вы ўпэўнены?</h4>
                </div>
                <div class="modal-body">
                    <p>Пасля выдалення Вы не зможаце аднавіць праект.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Не</button>
                    <button type="button" class="btn btn-primary" id="removeProjectConfirmed">Упэўнены</button>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="text-right">
    @if ($project->getId())
    {!! Form::button('Выдаліць', [
        'type' => 'button',
        'class' => 'btn btn-danger',
        'id' => 'delete-project'
    ]) !!}
    @endif
    {!! Form::button('Захаваць', [
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'id' => 'save-project'
    ]) !!}
</div>

<script>
    (function() {
        var searchImdbUrl = '{{ route('workshop::projects::searchImdb') }}';
        var placeholderSrc = '{{ Module::asset('projects:img/movie-placeholder.jpg') }}';
        var destroyUrl = '{{ route('workshop::projects::destroy') }}';
        projectDestroyInit(destroyUrl);
        projectStoreFormInit();
        projectFormInit(searchImdbUrl, placeholderSrc);
        seriesTableInit();
        var seriesTable = $('#series-table');
        @if('series' == $project->getType() && $project->getEpisodes())
            @foreach($project->getEpisodes() as $episode)
                var footable = seriesTable.data('footable');
                var newRow = getEpisodeRow(
                    '{{ $episode->getSeason() }}',
                    '{{ $episode->getEpisode() }}',
                    '{{ $episode->getInfo()->getOriginalTitle() }}',
                    '{{ $episode->getInfo()->getTranslatedTitle() }}',
                    '{{ $episode->getInfo()->getYear() }}',
                    '{{ $episode->getInfo()->getImdbId() }}',
                    '{{ $episode->getId() }}',
                    @if($episode->belongsToYou()) true @else false @endif
                );
                footable.appendRow(newRow);
                addValidationField();
            @endforeach
        @elseif('movie' != $project->getType())
            var footable = seriesTable.data('footable');
            var newRow = getEpisodeRow();
            footable.appendRow(newRow);
            addValidationField();
            $('#movie-type').trigger('change');
        @endif
    })();
</script>
{!! Form::close() !!}
