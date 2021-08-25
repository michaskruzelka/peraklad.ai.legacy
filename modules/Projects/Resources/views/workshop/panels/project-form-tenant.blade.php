{!! Form::open([
    'route' => 'workshop::projects::update',
    'method' => 'put',
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
            ['class' => 'form-control', 'maxlength' => '100', 'disabled' => 'disabled']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('title', 'Арыгінальная назва*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'original_title',
            $project->getInfo()->getOriginalTitle(),
            ['class' => 'form-control', 'maxlength' => '100', 'id' => 'title', 'disabled' => 'disabled']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('imdb-id', 'IMDB', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'imdb',
            $project->getInfo()->getImdbId(),
            ['class' => 'form-control', 'maxlength' => '20', 'id' => 'imdb-id', 'disabled' => 'disabled']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('score', 'Рэйтынг', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'score',
            $project->getInfo()->getImdbRating(),
            ['class' => 'form-control', 'maxlength' => '3', 'id' => 'score', 'disabled' => 'disabled']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('plot', 'Сюжэт', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::textarea(
            'plot',
            $project->getInfo()->getPlot(),
            ['class' => 'form-control', 'rows' => 5, 'id' => 'plot', 'disabled' => 'disabled']
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
        <select id="lang" name="lang" class="form-control" disabled="disabled">
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
        <select name="movie-type" id="movie-type" class="form-control" disabled="disabled">
            <option value="movie" @if('movie' == $project->getType()) selected @endif>фільм</option>
            <option value="series" @if('series' == $project->getType()) selected @endif>серыял</option>
        </select>
    </div>
</div>
@if ($project->getType() == 'movie')
<div class="form-group" id="form-group-year">
    {!! Form::label('year', 'Год*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">

        {!! Form::text(
            'year',
            $project->getInfo()->getYear(),
            ['class' => 'form-control', 'maxlength' => '4', 'id' => 'year', 'disabled' => 'disabled']
        ) !!}
    </div>
</div>
@endif

{!! Form::hidden('id', $project->getId()) !!}

@if ($project->getType() == 'series')
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

<div class="text-right">
    {!! Form::button('Захаваць', [
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'id' => 'save-project'
    ]) !!}
</div>

<script>
    (function() {

        projectStoreFormInit();
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
        @endif
    })();
</script>
@endif

{!! Form::close() !!}
