<?php

namespace Modules\Projects\Http\Controllers;

use App\Http\Requests;
use Modules\Projects\Entities\SubtitleVersion;
use Modules\Projects\Http\Requests\ApproveSubtitleVersionRequest;
use Modules\Projects\Http\Requests\LikeSubtitleVersionRequest;
use Modules\Projects\Http\Requests\RemoveSubtitleCommentRequest;
use Modules\Projects\Http\Requests\AddSubtitleVersionRequest;
use Modules\Projects\Http\Requests\RemoveSubtitleVersionRequest;
use Modules\Users\Entities\User;
use Modules\Projects\Entities\Subtitle;
use Modules\Projects\Entities\SubtitleComment;
use Modules\Projects\Entities\SubtitleHistoryChangeTiming;
use Modules\Projects\Entities\SubtitleTimeRange;
use Modules\Projects\Http\Requests\AddCommentRequest;
use Modules\Projects\Http\Requests\SaveSubtitleTranslationRequest;
use Modules\Projects\Http\Requests\TimeRangeRequest;
use Modules\Projects\Http\Requests\ViewSubtitleRequest;
use Pingpong\Modules\Routing\Controller;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Lang;

class SubtitlesController extends Controller
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * ProjectsController constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    /**
     * @param ViewSubtitleRequest $request
     * @param string $releaseId
     * @param string $status
     * @param int $number
     * @return \Illuminate\View\View
     */
    public function view(ViewSubtitleRequest $request, $releaseId, $status = 'all', $number = 1)
    {
        $subtitle = $request->getSubtitle();
        if ( ! $subtitle) {
            flash()->warning('Нічога не знойдзена');
            return redirect()->back();
        }
        $number = $subtitle->getNumber();
        $subsRepository = $this->dm->getRepository(Subtitle::class);
        $subtitles = $subsRepository->getBunchByNumber(
            $releaseId, $status, $number, $request->getSearch()
        );
        $subtitles = array_values($subtitles);
        $currentKey = array_search($subtitle, $subtitles);
        $prevSubtitle = isset($subtitles[$currentKey-1]) ? $subtitles[$currentKey-1] : false;
        $nextSubtitle = isset($subtitles[$currentKey+1]) ? $subtitles[$currentKey+1] : false;

        $prevTiming = $subsRepository->getPrevTiming($releaseId, $number);
        $nextTiming = $subsRepository->getNextTiming($releaseId, $number);

        $minRange =  ! is_null($prevTiming)
            ? max(
                SubtitleTimeRange::convertToMilliseconds($prevTiming['tr']['tl']),
                ($subtitle->getTimeRange()->getBottomLine(true)-2000),
                0
            )
            : max(($subtitle->getTimeRange()->getBottomLine(true)-2000), 0)
        ;

        $maxRange =  ! is_null($nextTiming)
            ? min(
                SubtitleTimeRange::convertToMilliseconds($nextTiming['tr']['bl']),
                ($subtitle->getTimeRange()->getTopLine(true)+2000)
            )
            : $subtitle->getTimeRange()->getTopLine(true)
        ;

        $limitPerPage = config('projects.subtitlesLimitPerPage');
        // Current position may me between 1 and $limitPerPage
        $currentPos = (($subtitle->getNumber()-1) % $limitPerPage) + 1;
        $prevGroupItem = false;
        if ($currentPos == $currentKey) {
            $prevGroupItem = array_shift($subtitles);
        }
        $nextGroupItem = false;
        if (isset($subtitles[$limitPerPage])) {
            $nextGroupItem = $subtitles[$limitPerPage];
            unset($subtitles[$limitPerPage]);
        }
        while (count($subtitles) > $limitPerPage) {
            array_pop($subtitles);
        }

        // retrieve base users info (avatar, etc)
        $userIds = array_unique(array_merge(
            array_column($subtitle->getComments()->getMongoData(), 'ui'),
            array_column($subtitle->getVersions()->getMongoData(), 'ui')
        ));
        $usersData = $this->dm->getRepository(User::class)->getAllBasic(false, $userIds)->toArray();
        foreach ($subtitle->getComments() as $comment) {
            $comment->setAvatar($usersData[$comment->getUserId()]['avatar']['fn']);
        }
        foreach ($subtitle->getVersions() as $version) {
            $version->setAvatar($usersData[$version->getUserId()]['avatar']['fn']);
        }

        return view('projects::workshop.subtitles.view', compact(
            'subtitle', 'status', 'number', 'subtitles', 'nextSubtitle',
            'prevSubtitle', 'minRange', 'maxRange', 'prevGroupItem', 'nextGroupItem'
        ));
    }

    /**
     * @param ViewSubtitleRequest $request
     * @param string $releaseId
     * @param string $status
     * @param int $number
     * @return \Illuminate\View\View
     */
    public function refreshTranslationPanel(ViewSubtitleRequest $request, $releaseId, $status = 'all', $number = 1)
    {
        $subtitle = $request->getSubtitle();
        $number = $subtitle->getNumber();
        if ( ! $subtitle) {
            flash()->warning('Нічога не знойдзена');
            return redirect()->back();
        }
        $subtitles = $this->dm->getRepository(Subtitle::class)->getBunchByNumber(
            $releaseId, $status, $number, $request->getSearch()
        );
        $subtitles = array_values($subtitles);
        $currentKey = array_search($subtitle, $subtitles);
        $prevSubtitle = isset($subtitles[$currentKey-1]) ? $subtitles[$currentKey-1] : false;
        $nextSubtitle = isset($subtitles[$currentKey+1]) ? $subtitles[$currentKey+1] : false;
        $view = view('projects::workshop.panels.subtitles.translation', compact(
            'subtitle', 'status', 'number', 'subtitles', 'nextSubtitle', 'prevSubtitle'
        ));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshCommentsPanel(Subtitle $subtitle)
    {
        // retrieve base users info (avatar, etc)
        $userIds = array_column($subtitle->getComments()->getMongoData(), 'ui');
        $usersData = $this->dm->getRepository(User::class)->getAllBasic(false, $userIds)->toArray();
        foreach ($subtitle->getComments() as $comment) {
            $comment->setAvatar($usersData[$comment->getUserId()]['avatar']['fn']);
        }
        $view = view('projects::workshop.panels.subtitles.comments', compact('subtitle'));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshVersionsPanel(Subtitle $subtitle)
    {
        // retrieve base users info (avatar, etc)
        $userIds = array_column($subtitle->getVersions()->getMongoData(), 'ui');
        $usersData = $this->dm->getRepository(User::class)->getAllBasic(false, $userIds)->toArray();
        foreach ($subtitle->getVersions() as $version) {
            $version->setAvatar($usersData[$version->getUserId()]['avatar']['fn']);
        }
        $view = view('projects::workshop.panels.subtitles.versions', compact('subtitle'));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param TimeRangeRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTiming(TimeRangeRequest $request, Subtitle $subtitle)
    {
        $timeRange = $subtitle->getTimeRange();
        $timeRange->setBottomLine($request->getBottomLine())
            ->setTopLine($request->getTopLine())
        ;
        $timingEvent = app()->build(SubtitleHistoryChangeTiming::class);
        $timingEvent->getInfo()->setTiming($timeRange->subRipRepresent());
        $subtitle->addHistoryEvent($timingEvent);
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $this->getTimingChangedMessage()];
        return response()->json($result);
    }

    /**
     * @param SaveSubtitleTranslationRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(SaveSubtitleTranslationRequest $request, Subtitle $subtitle)
    {
        $text = $subtitle->formatText($request->input('content'));
        if ($text) {
            $subtitle->setUnderwayStatus();
        } else {
            $subtitle->setCleanStatus();
        }
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => ''];
        return response()->json($result);
    }

    /**
     * @param SaveSubtitleTranslationRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function underSave(SaveSubtitleTranslationRequest $request, Subtitle $subtitle)
    {
        $text = $subtitle->formatText($request->input('content'));
        $subtitle->setTranslatedText($text);
        if ($text) {
            if ( ! $subtitle->isSaved()) {
                $subtitle->setUnderwayStatus();
            }
        } else {
            $subtitle->setCleanStatus();
        }
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => ''];
        return response()->json($result);
    }

    /**
     * @param SaveSubtitleTranslationRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(SaveSubtitleTranslationRequest $request, Subtitle $subtitle)
    {
        $originalText = $subtitle->formatText($subtitle->getTranslatedText());
        $text = $subtitle->formatText($request->input('content'));
        $subtitle->setTranslatedText($text);
        if ($originalText != $text) {
            $subtitle->setUnderwayStatus();
        }
        $subtitle->setSavedStatus();
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $this->getTranslationSavedMessage()];
        return response()->json($result);
    }

    /**
     * @param AddCommentRequest $request
     * @param Subtitle $subtitle
     * @param SubtitleComment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(AddCommentRequest $request, Subtitle $subtitle, SubtitleComment $comment)
    {
        $comment->setText($request->getText())
            ->setReplyTo($request->getReplyTo())
            ->setApprovedStatus()
            ->generateUserId()
            ->generateCreatedAt()
        ;

        $subtitle->addComment($comment);
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $this->getCommentAddedMessage()];
        return response()->json($result);
    }

    /**
     * @param RemoveSubtitleCommentRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeComment(RemoveSubtitleCommentRequest $request, Subtitle $subtitle)
    {
        $subtitle->removeComment($request->getComment());
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $this->getCommentRemovedMessage()];
        return response()->json($result);
    }

    /**
     * @param AddSubtitleVersionRequest $request
     * @param Subtitle $subtitle
     * @param SubtitleVersion $version
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVersion(AddSubtitleVersionRequest $request, Subtitle $subtitle, SubtitleVersion $version)
    {
        $version->setText($request->getText())
            ->setPendingStatus()
            ->generateUserId()
            ->generateCreatedAt()
        ;

        $subtitle->addVersion($version);
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $this->getVersionAddedMessage()];
        return response()->json($result);
    }

    /**
     * @param RemoveSubtitleVersionRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeVersion(RemoveSubtitleVersionRequest $request, Subtitle $subtitle)
    {
        $subtitle->removeVersion($request->getVersion());
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $this->getVersionRemovedMessage()];
        return response()->json($result);
    }

    /**
     * @param ApproveSubtitleVersionRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveVersion(ApproveSubtitleVersionRequest $request, Subtitle $subtitle)
    {
        if ($request->isChecked()) {
            foreach ($subtitle->getVersions() as $version) {
                if ($version->isApprovedStatus()) {
                    $subtitle->unapproveVersion($version);
                    break;
                }
            }
            $subtitle->approveVersion($request->getVersion());
            $response = $this->getVersionApprovedMessage();
        } else {
            $subtitle->unapproveVersion($request->getVersion());
            $response = $this->getVersionUnapprovedMessage();
        }
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $response];
        return response()->json($result);
    }

    /**
     * @param LikeSubtitleVersionRequest $request
     * @param Subtitle $subtitle
     * @return \Illuminate\Http\JsonResponse
     */
    public function likeVersion(LikeSubtitleVersionRequest $request, Subtitle $subtitle)
    {
        $version = $request->getVersion();
        if ($request->isChecked()) {
            $subtitle->likeVersion($version);
        } else {
            $subtitle->unlikeVersion($version);
        }
        $this->dm->persist($subtitle);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => ''];
        return response()->json($result);
    }

    /**
     * @return string
     */
    protected function getTranslationSavedMessage()
    {
        return Lang::has('projects::subtitle.translationSaved')
            ? Lang::get('projects::subtitle.translationSaved')
            : 'The translation has been saved'
        ;
    }

    /**
     * @return string
     */
    protected function getTimingChangedMessage()
    {
        return Lang::has('projects::subtitle.timingChanged')
            ? Lang::get('projects::subtitle.timingChanged')
            : 'The timing has been changed'
        ;
    }

    /**
     * @return string
     */
    protected function getCommentAddedMessage()
    {
        return Lang::has('projects::subtitle.commentAdded')
            ? Lang::get('projects::subtitle.commentAdded')
            : 'The comment has been added'
        ;
    }

    /**
     * @return string
     */
    protected function getCommentRemovedMessage()
    {
        return Lang::has('projects::subtitle.commentRemoved')
            ? Lang::get('projects::subtitle.commentRemoved')
            : 'The comment has been removed'
        ;
    }

    /**
     * @return string
     */
    protected function getVersionAddedMessage()
    {
        return Lang::has('projects::subtitle.versionAdded')
            ? Lang::get('projects::subtitle.versionAdded')
            : 'The version has been added'
        ;
    }

    /**
     * @return string
     */
    protected function getVersionRemovedMessage()
    {
        return Lang::has('projects::subtitle.versionRemoved')
            ? Lang::get('projects::subtitle.versionRemoved')
            : 'The version has been removed'
        ;
    }

    /**
     * @return string
     */
    protected function getVersionApprovedMessage()
    {
        return Lang::has('projects::subtitle.versionApproved')
            ? Lang::get('projects::subtitle.versionApproved')
            : 'The version has been approved'
        ;
    }

    /**
     * @return string
     */
    protected function getVersionUnapprovedMessage()
    {
        return Lang::has('projects::subtitle.versionUnapproved')
            ? Lang::get('projects::subtitle.versionUnapproved')
            : 'The version has been unapproved'
        ;
    }
}