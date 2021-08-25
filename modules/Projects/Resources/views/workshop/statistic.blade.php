<header class="slidePanel-header site-navbar">
    <div class="slidePanel-actions" aria-label="actions" role="group">
        <button type="button" class="btn btn-pure btn-inverse slidePanel-close actions-top icon wb-close"
                aria-hidden="true"></button>
    </div>
    <h1>{{ $release->getMovieTranslatedName() }}</h1>
    <h5>{{ $release->getMovieOriginalName() }}</h5>
</header>
<div class="slidePanel-inner">
    <section class="slidePanel-inner-section">
        <div class="step-info">
            <div class="col-md-4 col-sm-8 col-xs-8">
                <div class="step">
                    <span class="step-numbers blue-600">{{ $progressDays }}</span>
                    <div class="step-desc">
                        <span class="step-title">Спатрэбілася</span>
                        <p>дзён</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-8 col-xs-8">
                <div class="step">
                    <span class="step-numbers orange-600">{{ $unfinishedSubsCount }}</span>
                    <div class="step-desc">
                        <span class="step-title">Засталося</span>
                        <p>перакласці</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-8 col-xs-8">
                <div class="step">
                    <span class="step-numbers green-600">{{ $release->getLoads() }}</span>
                    <div class="step-desc">
                        <span class="step-title">Спампавана</span>
                        <p>разоў</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="line-chart">
            <div class="chart-header">
                <h3 class="blue-grey-700">ТЭНДЭНЦЫЯ</h3>
                <div class="btn-group dropdown">
                    <select class="form-control" id="periods-list-selector">
                        <option value="{{ route('workshop::releases::statistic::trend', [
                            'release' => $release->getId(),
                            'period' => 'day'
                        ]) }}">Дзень</option>
                        <option value="{{ route('workshop::releases::statistic::trend', [
                            'release' => $release->getId(),
                            'period' => 'week'
                        ]) }}">Тыдзень</option>
                    </select>
                </div>
            </div>
            <script>
                var select2Config = {
                    width: "style",
                    language: "be",
                    minimumResultsForSearch: "Infinity"
                };
                $('#periods-list-selector').select2(select2Config).on('change', function(e) {
                    e.preventDefault();
                    var errorText = 'Нешта не так...';
                    $.ajax({
                        url: $(e.currentTarget).val(),
                        type: 'GET',
                        success: function(result) {
                            if ( ! result.status || result.status != 'ok') {
                                toastr.warning(result.response, errorText);
                                return false;
                            }
                            $('#trendsChart').html(result.response);
                        },
                        error: function(result) {
                            if (result.status == 422) {
                                $.each(result.responseJSON, function(key, arr) {
                                    $.each(arr, function(index, message) {
                                        toastr.warning(message);
                                    });
                                });
                            } else {
                                toastr.error(result.status + ' - ' + result.statusText, errorText);
                            }
                        }
                    });
                });
            </script>
        </div>
        <div id="trendsChart">
            @include('projects::workshop.statistic.trends')
        </div>
        @include('projects::workshop.statistic.count')
    </section>
    <div class="slidePanel-footer">
        <i class="icon wb-download bg-green-600 white icon-circle" aria-hidden="true"></i>
        <span>
            Сёння спампавана:
                кірыліца - @if ($downloads && isset($downloads['cyrillic'])) {{ $downloads['cyrillic'] }} @else 0 @endif,
                лацінка - @if ($downloads && isset($downloads['latin'])) {{ $downloads['latin'] }} @else 0 @endif
        </span>
    </div>
</div>
