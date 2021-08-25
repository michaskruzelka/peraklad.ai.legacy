<div class="modal fade modal-slide-from-bottom" id="opensubtitlesModal" aria-hidden="false" aria-labelledby="opensubtitlesModalLabel" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <form class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="opensubtitlesModalLabel">
                    Калі ласка, выберыце адзін са знойдзеных рэлізаў
                </h4>
            </div>
            <div class="modal-body">
                <select class="search-result"></select><br/>
                <button type="button" class="btn btn-primary" id="pick-release">Выбраць</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade modal-slide-from-bottom" id="removeModal" aria-hidden="false" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Вы ўпэўнены?</h4>
            </div>
            <div class="modal-body">
                <p>Пасля выдалення Вы зможаце аднавіць рэліз цягам наступных 10 хвілін.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Не</button>
                <button type="button" class="btn btn-primary" id="removeConfirmed">Упэўнены</button>
            </div>
        </div>

    </div>
</div>

{!! Form::open([
   'route' => 'workshop::releases::store',
   'class' => 'form-horizontal',
   'autocomplete' => 'off',
   'id' => 'release-store',
   'enctype' => 'multipart/form-data'
]) !!}
<div id="releases">
    <table id="releases-table" class="table table-bordered table-hover toggle-circle"
           data-page-size="10">
        <tbody>
            <tr>
                <td class="private-mode-column">
                    <input type="checkbox" name="private_mode" data-labelauty="Прыватны рэжым|Прыватны рэжым"/>
                </td>
                <td class="orthography-column">
                    <select name="orthography" class="form-control">
                        @foreach(config("projects.orthographies") as $key => $orthography)
                            <option value="{{ $key }}">{{ $orthography['alt-value'] }}</option>
                        @endforeach
                    </select>
                </td>
                @if($project->getType() == 'series')
                <td class="episode-column">
                    <select name="release_episode" class="form-control">
                        @foreach($project->getEpisodes() as $episode)
                            <option value="{{ $episode->getId() }}">{{ $episode->getInfo()->getTranslatedTitle() }}</option>
                        @endforeach
                    </select>
                </td>
                @endif
                <td class="form-group">
                    <div class="input-group input-group-file">
                        <input type="text" class="form-control" name="new_rip_name" id="new_rip_name" placeholder="Імя рыпа" maxlength="100" />
                        <span class="input-group-btn">
                            <span class="btn btn-outline btn-file" data-toggle="tooltip" data-original-title="Выбраць">
                                <i class="icon wb-attach-file" aria-hidden="true"></i>
                                <input type="file" name="new_release_file">
                                <input type="hidden" name="opensubtitles_id" id="opensubtitles-id">
                                <input type="hidden" name="opensubtitles_charset" id="opensubtitles-charset">
                            </span>
                            <button type="button" id="isTranslated" class="btn btn-default btn-outline" data-toggle="button" data-original-title="Гатовы пераклад">
                                <i class="icon wb-check text" aria-hidden="true"></i>
                                <i class="icon wb-check text-active text-danger" aria-hidden="true"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-primary ladda-button"
                                    data-toggle="tooltip"
                                    data-original-title="opensubtitles.org"
                                    data-style="zoom-in"
                                    onclick="searchOpensubtitles(this, '{{ route('workshop::releases::searchOpensubtitles') }}')"
                            >
                                <span class="ladda-label">
                                    <i class="icon wb-search" aria-hidden="true"></i>
                                </span>
                            </button>
                            <button type="button" id="upload-release" class="btn btn-primary ladda-button" data-toggle="tooltip" data-original-title="Загрузіць" data-style="zoom-in">
                                <span class="ladda-label">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                </span>
                            </button>
                        </span>
                    </div>
                </td>
            </tr>

            <?php foreach($project->getReleases() as $release): ?>
            <?php if($release->getState() == array_search('destroyed', config('projects.states'))) continue; ?>
            <tr>
                <td class="private-mode-column">
                    <input type="checkbox" class="update-mode" data-labelauty="Прыватны рэжым|Прыватны рэжым"
                        @if($release->getMode() == array_search('private', config('projects.modes'))) checked @endif
                        @if($release->getState() == array_search('failed', config('projects.states'))
                            ||  ! $release->belongsToYou()) disabled @endif
                    />
                </td>
                <td class="orthography-column">
                    <select class="form-control update-orthography"
                        @if($release->getState() == array_search('failed', config('projects.states'))
                            ||  ! $release->belongsToYou()) disabled @endif>
                        @foreach(config("projects.orthographies") as $key => $orthography)
                            <option value="{{ $key }}"
                            @if($key === $release->getOrthography()) selected @endif
                            >{{ $orthography['alt-value'] }}</option>
                        @endforeach
                    </select>
                </td>
                @if($project->getType() == 'series')
                    <td class="episode-column">
                        <select class="form-control release-episode"
                        @if($release->getState() == array_search('failed', config('projects.states'))
                            ||  ! $release->belongsToYou()) disabled @endif>
                            @foreach($project->getEpisodes() as $episode)
                                <option value="{{ $episode->getId() }}"
                                @if($episode->getReleases()->contains($release))) selected @endif
                                >{{ $episode->getInfo()->getTranslatedTitle() }}</option>
                            @endforeach
                        </select>
                    </td>
                @endif
                <td>
                    <div class="input-group input-group-file">
                        <input type="text" class="form-control update-rip" placeholder="Імя рыпа" value="{{ $release->getRipName() }}"
                            @if($release->getState() == array_search('failed', config('projects.states'))
                            ||  ! $release->belongsToYou()) disabled @endif/>
                        <input type="hidden" name="release_id" value="{{ $release->getId() }}" />
                        <span class="input-group-btn">
                            @if($release->getState() != array_search('failed', config('projects.states')))
                            <span type="button" class="btn btn-pure btn-outline" data-toggle="tooltip" data-original-title="Субтытры" onclick="window.location.replace('{{ route('workshop::releases::subrip::view', ['release' => $release->getId()]) }}')">
                                <i class="icon ion-closed-captioning" aria-hidden="true"></i>
                            </span>
                            <button type="button" data-toggle="tooltip" data-original-title="Пераклад" class="btn btn-default btn-outline" onclick="window.location.replace('{{ route('workshop::subtitles::view', ['releaseId' => $release->getId()]) }}');
                                ">
                                <i class="icon wb-indent-increase" aria-hidden="true"></i>
                            </button>
                            @if($release->belongsToYou())
                            <button type="button" class="btn btn-default btn-outline delete-release" data-toggle="tooltip" data-original-title="Выдаліць">
                                <i class="icon wb-trash" aria-hidden="true"></i>
                            </button>
                            @endif
                            @else
                            <button type="button" class="btn btn-default btn-outline restore-release" data-toggle="tooltip" data-original-title="Аднавіць">
                                <i class="icon ion-arrow-return-left" aria-hidden="true"></i>
                            </button>
                            @endif
                        </span>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
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
{!! Form::close() !!}
<script>
    (function() {
        var updateUrl = '{{ route('workshop::releases::update') }}';
        var restoreUrl = '{{ route('workshop::releases::restore') }}';
        var destroyUrl = '{{ route('workshop::releases::destroy') }}';
        releaseFormInit(updateUrl, restoreUrl, destroyUrl);
    })();
</script>
@push('bottom-scripts')
<script>
    $('#isTranslated').tooltip();
</script>
@endpush