@if($subtitle->isViewable())

    @foreach($subtitle->getVersions() as $version)
        @if ( ! $version->isRemovedStatus())
            <div class="comment media">
                <div class="media-left">
                    <a class="avatar avatar-lg" href="javascript:void(0)">
                        <img src="{{ $version->getAvatarSrc() }}">
                    </a>
                </div>
                <div class="comment-body media-body">
                    <a class="comment-author" href="javascript:void(0)">{{ $version->getUserId() }}</a>
                    <div class="comment-meta">
                        <span class="date">{{ $version->getHumanCreatedAt() }}</span>
                    </div>
                    <div class="comment-content">
                        <p>{!! $version->getText() !!}</p>
                    </div>
                    <div class="comment-actions">
                        @if ($version->isOwner())
                            <button data-toggle="tooltip" data-original-title="Выдаліць" type="button" class="btn btn-icon btn-default btn-outline removeVersion" data-id="{{ $version->getId() }}" data-url="{{ route('workshop::subtitles::removeVersion', [
                                'subtitle' => $subtitle->getId()
                            ]) }}">
                                <i class="icon wb-trash" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if ( ! $version->isOwner())
                            <button type="button"
                                    class="likeVersion btn btn-icon btn-default btn-outline @if ($version->doYouLike()) active @endif"
                                    data-toggle="button"
                                    data-original-title="@if ($version->doYouLike()) Адмяніць @else Упадабаць @endif"
                                    aria-pressed="@if ($version->doYouLike()) true @else false @endif"
                                    data-id="{{ $version->getId() }}"
                                    data-url="{{ route('workshop::subtitles::likeVersion', [
                                        'subtitle' => $subtitle->getId()
                                    ]) }}"
                            >
                            <span class="text">
                                <i class="icon wb-thumb-up" aria-hidden="true"></i>
                                @if ($version->doYouLike())
                                    {{ (($version->getLikesCount()-1) > 0) ? ($version->getLikesCount()-1) : '' }}
                                @else
                                    {{ ($version->getLikesCount() > 0) ? $version->getLikesCount() : '' }}
                                @endif
                            </span>
                            <span class="text-active">
                                <i class="icon wb-thumb-up text-danger" aria-hidden="true"></i>
                                @if ($version->doYouLike()) {{ $version->getLikesCount() }} @else {{ $version->getLikesCount()+1 }} @endif
                            </span>
                            </button>
                        @endif
                        @if ( ! $version->isOwner() && $subtitle->isEditable())
                            <button type="button"
                                    data-original-title="@if ($version->isApprovedStatus()) Адхіліць @else Выбраць @endif"
                                    class="approveVersion btn btn-icon btn-default btn-outline @if ($version->isApprovedStatus()) active @endif"
                                    data-toggle="button"
                                    aria-pressed="@if ($version->isApprovedStatus()) true @else false @endif"
                                    data-id="{{ $version->getId() }}"
                                    data-url="{{ route('workshop::subtitles::approveVersion', [
                                        'subtitle' => $subtitle->getId()
                                    ]) }}"
                            >
                                <i class="icon wb-check text" aria-hidden="true"></i>
                                <i class="icon wb-check text-active text-danger" aria-hidden="true"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <form id="add-subtitle-version" action="{{ route('workshop::subtitles::addVersion', [
        'subtitle' => $subtitle->getId()
    ]) }}" method="post">
        <div class="form-group">
            <textarea id="addVersionText" class="form-control" maxlength="500" name="text" rows="3"></textarea>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">Дадаць</button>
        </div>
    </form>

    <script>
        (function(document, window, $) {
            'use strict';

            initSubtitleVersions();

            $( document ).ready(function() {
                $(['.approveVersion', '.likeVersion']).tooltip();
            });

        })(document, window, jQuery);
    </script>

@else

    <div class="well red-600">
        НАЖАЛЬ, ВАМ НЕЛЬГА ПРАГЛЯДАЦЬ ВАРЫЯНТЫ ПЕРАКЛАДУ
    </div>

@endif