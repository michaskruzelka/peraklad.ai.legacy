@if($subtitle->isViewable())

@foreach($subtitle->getComments() as $comment)
@if ($comment->isApprovedStatus())
<div class="comment media">
    <div class="media-left">
        <a class="avatar avatar-lg" href="javascript:void(0)">
            <img src="{{ $comment->getAvatarSrc() }}">
        </a>
    </div>
    <div class="comment-body media-body">
        <a class="comment-author" href="javascript:void(0)">{{ $comment->getUserId() }}</a>
        <div class="comment-meta">
            <span class="date">{{ $comment->getHumanCreatedAt() }}</span>
        </div>
        <div class="comment-content">
            @if ($comment->isRemovedStatus())
                <p class="orange-600">ВЫДАЛЕНЫ</p>
            @else
                <p>{!! $comment->getText() !!}</p>
            @endif
        </div>
        <div class="comment-actions">
        @if ($comment->isOwner() &&  ! $comment->isRemovedStatus())
            <button data-toggle="tooltip" data-original-title="Выдаліць" type="button" class="btn btn-icon btn-default btn-outline removeComment" data-id="{{ $comment->getId() }}" data-url="{{ route('workshop::subtitles::removeComment', [
                'subtitle' => $subtitle->getId()
            ]) }}">
                <i class="icon wb-trash" aria-hidden="true"></i>
            </button>
        @endif
        @if ( ! $comment->isOwner())
            <button data-toggle="tooltip" data-original-title="Адказаць" type="button" class="btn btn-icon btn-default btn-outline replyComment" data-user="{{ $comment->getUserId() }}">
                <i class="icon wb-reply" aria-hidden="true"></i>
            </button>
        @endif
        </div>
    </div>
</div>
@endif
@endforeach

<form id="add-subtitle-comment" action="{{ route('workshop::subtitles::addComment', [
    'subtitle' => $subtitle->getId()
    ]) }}" method="post">
    <div class="form-group">
        <textarea id="addCommentText" class="form-control" maxlength="500" name="text" rows="3"></textarea>
        <input type="hidden" name="replyTo" id="addCommentReplyTo">
    </div>
    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary">Дадаць</button>
    </div>
</form>

<script>
    (function(document, window, $) {
        'use strict';

        initSubtitleComments();

    })(document, window, jQuery);
</script>

@else

<div class="well red-600">
    НАЖАЛЬ, ВАМ НЕЛЬГА ПРАГЛЯДАЦЬ КАМЕНТАРЫІ
</div>

@endif