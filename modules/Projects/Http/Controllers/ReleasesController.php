<?php

namespace Modules\Projects\Http\Controllers;

use App\Http\Requests;
use Modules\Projects\Http\Requests\ApproveMemberRequest;
use Modules\Projects\Http\Requests\CompleteReleaseRequest;
use Modules\Projects\Http\Requests\DownloadSubRipRequest;
use Modules\Projects\Http\Requests\HandleMemberRequest;
use Modules\Projects\Http\Requests\RegenerateSubRipRequest;
use Modules\Projects\Http\Requests\SaveReleaseFileRequest;
use Modules\Projects\Http\Requests\SearchReleaseRequest;
use Modules\Projects\Http\Requests\DestroyReleaseRequest;
use Modules\Projects\Http\Requests\StoreReleaseRequest;
use Modules\Projects\Http\Requests\UpdateReleaseRequest;
use Modules\Projects\Http\Requests\OpensubtitlesSearchRequest;
use Modules\Projects\Contracts\SubtitlesApi;
use Modules\Projects\Entities\Project;
use Modules\Projects\Entities\Release;
use Modules\Projects\Entities\Subtitle;
use Modules\Projects\Entities\ReleaseMember;
use Modules\Users\Entities\User;
use Modules\Users\Entities\UserAvatar;
use Pingpong\Modules\Routing\Controller;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use App\Traits\ControllerJson;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Projects\Jobs\DestroyRelease;
use Lang;

class ReleasesController extends Controller
{
    use ControllerJson;
    use DispatchesJobs;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * ReleasesController constructor.
     * @param LaravelDocumentManager $ldm
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    /**
     * @return \Illuminate\View\View
     * @param string $in
     * @param string $mode
     * @param int $page
     * @param mixed $currentState
     * @param SearchReleaseRequest $request
     */
    public function viewMyList(
        SearchReleaseRequest $request,
        $in = 'owner',
        $mode = 'all',
        $currentState = 'all',
        $page = 1
    )
    {
        $userId = $request->getUserId();
        return $this->viewList($request, $userId, $in, $mode, $currentState, $page);
    }

    /**
     * @param SearchReleaseRequest $request
     * @param string $in
     * @param string $mode
     * @param string $usId
     * @param string $currentState
     * @param int $page
     * @return \Illuminate\View\View
     */
    public function viewList(
        SearchReleaseRequest $request,
        $usId = 'all',
        $in = 'all',
        $mode = 'all',
        $currentState = 'all',
        $page = 1
    )
    {
        $excludeUsersFlag = $request->getExcludeUsersFlag();
        $userId = $request->getUserId();
        $filteringMode = $request->getMode();
        $releasesRep = $this->dm->getRepository(Release::class);
        $owners = array_column($releasesRep->getOwners([\Auth::id()])->toArray(), 'userId');
        $usersRep = $this->dm->getRepository(User::class);
        $usersBasicInfo = $usersRep->getAllBasic(false)->toArray();
        $users = array_keys($usersBasicInfo);
        if ( ! in_array($userId, $users)) {
            abort(404);
        }
        $users = array_diff(array_unique(array_merge($owners, $users)), [\Auth::id()]);
        if ('all' == $in && 'all' != $usId) {
            $in = 'owner';
        }
        $notFilteredReleasesIds = $releasesRep->getAllIdsByUserId(
            $userId,
            $excludeUsersFlag,
            $in,
            $filteringMode
        );
        if ( ! is_null($notFilteredReleasesIds)) {
            $projectsRep = $this->dm->getRepository(Project::class);
            $years = $projectsRep->getYearsListByReleases($notFilteredReleasesIds);
            $langs = $projectsRep->getLangsListByReleases($notFilteredReleasesIds);
            $releases = $releasesRep->getByUserId(
                $userId,
                $page,
                $currentState,
                $request->getSearch(),
                $request->getYear(),
                $request->getLang(),
                $excludeUsersFlag,
                $in,
                $filteringMode
            );
            $releaseMembers = $releasesRep->getMembersInfo($releases->toArray(), $usersBasicInfo);
        } else {
            $releases = collect([]);
        }
        $statesInfo = $releasesRep->getStatesInfo(
            $userId,
            $request->getSearch(),
            $request->getYear(),
            $request->getLang(),
            $excludeUsersFlag,
            $in,
            $filteringMode
        );
        $title = $request->generateTitle();
        $routeName = $usId == \Auth::id() ? 'workshop::projects::my' : 'workshop::projects::list';
        $routeBaseParams = $usId == \Auth::id() ? [] : ['userId' => $usId];
        return view('projects::workshop.list', compact(
            'releases',
            'statesInfo',
            'releaseMembers',
            'page',
            'title',
            'years',
            'langs',
            'usId',
            'users',
            'routeName',
            'routeBaseParams',
            'in',
            'mode'
        ));
    }

    /**
     * @param SearchReleaseRequest $request
     * @param string $usId
     * @param string $in
     * @param string $mode
     * @param string $currentState
     * @param int $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function refreshReleasesList(
        SearchReleaseRequest $request,
        $usId = 'all',
        $in = 'all',
        $mode = 'all',
        $currentState = 'all',
        $page = 1
    )
    {
        $excludeUsersFlag = $request->getExcludeUsersFlag();
        $userId = $request->getUserId();
        $filteringMode = $request->getMode();
        $releasesRep = $this->dm->getRepository(Release::class);

        if ('all' == $in && 'all' != $usId) {
            $in = 'owner';
        }

        $releases = $releasesRep->getByUserId(
            $userId,
            $page,
            $currentState,
            $request->getSearch(),
            $request->getYear(),
            $request->getLang(),
            $excludeUsersFlag,
            $in,
            $filteringMode
        );
        $usersRep = $this->dm->getRepository(User::class);
        $usersBasicInfo = $usersRep->getAllBasic(false)->toArray();
        $releaseMembers = $releasesRep->getMembersInfo($releases->toArray(), $usersBasicInfo);

        $routeName = $usId == \Auth::id() ? 'workshop::projects::my' : 'workshop::projects::list';
        $routeBaseParams = $usId == \Auth::id() ? [] : ['userId' => $usId];
        $view = view('projects::workshop.panels.projects-list', compact(
            'releases',
            'releaseMembers',
            'page',
            'usId',
            'routeName',
            'routeBaseParams',
            'mode'
        ));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param Release $release
     * @param HandleMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeMember(Release $release, HandleMemberRequest $request)
    {
        foreach ($release->getMembers() as $member) {
            if ($member->getUserId() == $request->getMemberId()) {
                $release->removeMember($member);
                break;
            }
        }
        $user = $request->getUser();
        $this->dm->persist($release);
        $this->dm->flush();
        $response = $member->getState() == ReleaseMember::CONFIRMED_STATE
            ? $this->getMemberRemovedMessage($user->getUsername())
            : $this->getMemberCancelledMessage($user->getUsername())
        ;
        $result = [
            'status' => 'ok',
            'response' => $response
        ];
        return response()->json($result);
    }

    /**
     * @param Release $release
     * @param ApproveMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveMember(Release $release, ApproveMemberRequest $request)
    {
        $exists = false;
        foreach ($release->getMembers() as $member) {
            if ($member->getUserId() == $request->getMemberId()) {
                $member->setConfirmedState();
                $exists = true;
                break;
            }
        }
        $user = $request->getUser();
        if ( ! $exists) {
            $result = [
                'status' => 'fail',
                'response' => $this->getMemberDoesNotExistMessage($user->getUsername())
            ];
        } else {
            $this->dm->persist($release);
            $this->dm->flush();
            $result = [
                'status' => 'ok',
                'response' => $this->getMemberApprovedMessage($user->getUsername())
            ];
        }
        return response()->json($result);
    }

    /**
     * @param Release $release
     * @param HandleMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    public function addMember(Release $release, HandleMemberRequest $request)
    {
        $user = $request->getUser();
        foreach ($release->getMembers() as $member) {
            if ($member->getUserId() == $request->getMemberId()) {
                $result = [
                    'status' => 'fail',
                    'response' => $this->getMemberAddedDuplicatedMessage($user->getUsername())
                ];
                return response()->json($result);
            }
        }
        $release->importMember($user);
        $this->dm->persist($release);
        $this->dm->flush();
        $response = $release->belongsToYou()
            ? $this->getMemberAddedMessage($user->getUsername())
            : $this->getMemberRequestedMessage()
        ;
        $result = [
            'status' => 'ok',
            'response' => $response
        ];
        return response()->json($result);
    }

    /**
     * @param Release $release
     * @param StoreReleaseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Release $release, StoreReleaseRequest $request)
    {
        try {
            $captions = $request->getCaptions();
            $project = $request->getProject();
            $episode = $request->getEpisode();
        } catch (\Exception $e) {
            \Log::warning($e->getMessage());
            $result = ['status' => 'fail', 'response' => $e->getMessage()];
            return response()->json($result);
        }

        $release->setMovieOriginalName($request->getOriginalName())
            ->setMovieTranslatedName($request->getTranslatedName())
            ->setProjectSlug($project->getInfo()->getSlug())
            ->setOrthography($request->input('orthography'))
            ->setRipName($request->input('new_rip_name'))
            ->setIsGenerated(false)
            ->setUnderwayState()
        ;
        if ( !! $request->input('private_mode')) {
            $release->setPrivateMode();
        } else {
            $release->setPublicMode();
        }
        $this->dm->persist($release);

        $subtitleRepository = $this->dm->getRepository(Subtitle::class);
        if ( !! $request->input('isTranslated')) {
            $subtitles = $subtitleRepository->importTranslated($captions, $release);
            $captionFile = $subtitleRepository->export($subtitles);
            $release->generateFiles($captionFile->getFileContent())
                ->setCompletedState()
                ->setReadiness('100%')
            ;
        } else {
            $subtitleRepository->importOriginal($captions, $release);
        }

        $project->addRelease($release);
        if ($episode) {
            $episode->addRelease($release);
        }
        $this->dm->persist($release);
        $this->dm->persist($project);
        $this->dm->flush();

        $result = ['status' => 'ok', 'response' => 'Субтытры паспяхова загружаныя'];
        return response()->json($result);
    }

    /**
     * @param UpdateReleaseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateReleaseRequest $request)
    {
        $release = $request->getRelease();
        if ($orthography = $request->getOrthography()) {
            $release->setOrthography($orthography);
        }
        if ($mode = $request->getMode()) {
            if ('true' === $mode) {
                $release->setPrivateMode();
            } else {
                $release->setPublicMode();
            }
        }
        if ($ripName = $request->getRipName()) {
            $release->setRipName($ripName);
        }
        if ($episodeId = $request->getEpisodeId()) {
            $episodes = $request->getProject()->getEpisodes();
            foreach ($episodes as $episode) {
                $releases = $episode->getReleases();
                if ($episode->getId() != $episodeId
                    && $releases->contains($release)
                ) {
                    $releases->removeElement($release);
                }
                if ($episode->getId() == $episodeId) {
                    $releases->add($release);
                }
            }
        } else {
            $this->dm->persist($release);
        }
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => 'Змены захаваныя'];
        return response()->json($result);
    }

    /**
     * @param DestroyReleaseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DestroyReleaseRequest $request)
    {
        $release = $request->getRelease();
        $release->setFailedState();
        $this->dm->persist($release);
        $this->dm->flush();
        $job = (new DestroyRelease($release->getId()))->delay(600);
        $this->dispatch($job);
        $result = ['status' => 'ok', 'response' => 'Рэліз паспяхова выдалены'];
        return response()->json($result);
    }

    /**
     * @param DestroyReleaseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(DestroyReleaseRequest $request)
    {
        $release = $request->getRelease();
        if ($release->getState() != array_search('failed', config('projects.states'))) {
            $result = ['status' => 'fail', 'response' => 'Немагчыма аднавіць рэліз'];
        } else {
            $release->setUnderwayState();
            $this->dm->persist($release);
            $this->dm->flush();
            $result = ['status' => 'ok', 'response' => 'Рэліз паспяхова адноўлены'];
        }
        return response()->json($result);
    }

    /**
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshReleasesForm(Project $project)
    {
        $view = view('projects::workshop.panels.releases-form', compact('project'));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param OpensubtitlesSearchRequest $request
     * @param SubtitlesApi $api
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchOpensubtitles(OpensubtitlesSearchRequest $request, SubtitlesApi $api)
    {
        $project = $request->getProject();
        $episode = $request->getEpisode();
        $lang = $project->getInfo()->getLanguage()->getIso6393b();
        if ($episode) {
            $imdbId = $episode->getInfo()->getImdbId();
            $title = $episode->getInfo()->getOriginalTitle();
        } else {
            $imdbId = $project->getInfo()->getImdbId();
            $title = $project->getInfo()->getOriginalTitle();
        }
        if ( ! $releases = $api->search($lang, $imdbId, false)) {
            $releases = $api->search($lang, false, $title);
        }
        $releases = $this->utf8EncodeAll($releases);
        if (is_array($releases) &&  ! empty($releases)) {
            $result = ['status' => 'ok', 'response' => $releases];
        } else {
            $result = ['status' => 'fail', 'response' => ''];
        }
        return response()->json($result);
    }

    /**
     * @param Release $release
     * @param string $abc
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subRipView(Release $release, $abc = 'cy')
    {
        if ( ! $file = $release->getFile($abc)) {
            $subtitleRepository = $this->dm->getRepository(Subtitle::class);
            $subtitles = $subtitleRepository->getByRelease($release->getId(true));
            $captionFile = $subtitleRepository->export($subtitles);
            $charsets =  (array) config('projects.charsets')[$abc];
            if ($captionFile->getCuesCount() > 0) {
                $release->generateFile($charsets, $abc, $captionFile->getFileContent());
                $this->dm->persist($release);
                $this->dm->flush();
                $file = $release->getFile($abc);
            }
        }
        $charsets = (array) config('projects.downloadableCharsets')[$abc];
        $newLineFormats =  (array) config('projects.newLineFormats');
        return view('projects::workshop.subrip.view', compact('release', 'abc', 'file', 'charsets', 'newLineFormats'));
    }

    /**
     * @param Release $release
     * @param string $abc
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshSubRipPanel(Release $release, $abc = 'cy')
    {
        if ( ! $file = $release->getFile($abc)) {
            $subtitleRepository = $this->dm->getRepository(Subtitle::class);
            $subtitles = $subtitleRepository->getByRelease($release->getId(true));
            $captionFile = $subtitleRepository->export($subtitles);
            $charsets =  (array) config('projects.charsets')[$abc];
            if ($captionFile->getCuesCount() > 0) {
                $release->generateFile($charsets, $abc, $captionFile->getFileContent());
                $this->dm->persist($release);
                $this->dm->flush();
                $file = $release->getFile($abc);
            }
        }
        $view = view('projects::workshop.panels.subrip.file', compact('release', 'abc', 'file'));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param SaveReleaseFileRequest $request
     * @param Release $release
     * @param $abc
     * @return \Illuminate\Http\JsonResponse
     */
    public function subRipSave(SaveReleaseFileRequest $request, Release $release, $abc)
    {
        $fileContent = $request->getData();
        $releaseFile = $release->getFile($abc);
        if ( ! $releaseFile) {
            $charsets =  (array) config('projects.charsets')[$abc];
            $release->generateFile($charsets, $abc, $fileContent);
        } else {
            $releaseFile->setData($fileContent);
        }

        $this->dm->persist($release);
        $this->dm->flush();
        $result = ['status' => 'ok', 'response' => $this->getSubRipSavedMessage()];
        return response()->json($result);
    }

    /**
     * @param RegenerateSubRipRequest $request
     * @param Release $release
     * @param $abc
     * @return \Illuminate\Http\JsonResponse
     */
    public function subRipRegenerate(RegenerateSubRipRequest $request, Release $release, $abc)
    {
        // remove the old one
        if ($oldFile = $release->getFile($abc)) {
            $release->removeFile($oldFile);
        }
        // create a new one
        $subtitleRepository = $this->dm->getRepository(Subtitle::class);
        $subtitles = $subtitleRepository->getByRelease($release->getId(true));
        $captionFile = $subtitleRepository->export($subtitles);
        $charsets =  (array) config('projects.charsets')[$abc];
        if ($captionFile->getCuesCount() > 0) {
            $release->generateFile($charsets, $abc, $captionFile->getFileContent());
            $this->dm->persist($release);
            $this->dm->flush();
            $result = ['status' => 'ok', 'response' => $this->getSubRipRegeneratedMessage()];
        } else {
            $result = ['status' => 'fail', 'response' => $this->getSubRipRegeneratedEmptyMessage()];
        }
        return response()->json($result);
    }

    /**
     * @param DownloadSubRipRequest $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function subRipDownload(DownloadSubRipRequest $request)
    {
        $pathToFile = '/tmp/' . md5(microtime());
        $captionFile = $request->getCaptionFile();
        $captionFile->setEncoding($request->getCharset());
        $captionFile->setLineEnding($request->getNewLineFormat());
        $captionFile->save($pathToFile);
        return response()->download($pathToFile, $request->getFile()->getName());
    }

    /**
     * @param CompleteReleaseRequest $request
     * @param Release $release
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(CompleteReleaseRequest $request, Release $release)
    {
        $release->setCompletedState()
            ->setReadiness('100%')
        ;
        $this->dm->persist($release);
        $this->dm->flush();
        flash()->success('Праект завершаны');
        return back();
    }

    /**
     * @param CompleteReleaseRequest $request
     * @param Release $release
     * @return \Illuminate\Http\RedirectResponse
     */
    public function backToEdit(CompleteReleaseRequest $request, Release $release)
    {
        $readiness = $this->dm->getRepository(Subtitle::class)->getReadiness($release->getId());
        $readiness = round($readiness, 1) . '%';
        $release->setUnderwayState()
            ->setReadiness($readiness)
        ;
        $this->dm->persist($release);
        $this->dm->flush();
        flash()->success('Праект вернуты да рэжыму перакладання');
        return back();
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getMemberRemovedMessage($username)
    {
        return Lang::has('projects::release.memberRemoved')
            ? Lang::get('projects::release.memberRemoved', ['username' => $username])
            : 'The member has been successfully detached from the release'
        ;
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getMemberAddedMessage($username)
    {
        return Lang::has('projects::release.memberAdded')
            ? Lang::get('projects::release.memberAdded', ['username' => $username])
            : 'The member has been successfully added to the release'
        ;
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getMemberAddedDuplicatedMessage($username)
    {
        return Lang::has('projects::release.memberAddedDuplicated')
            ? Lang::get('projects::release.memberAddedDuplicated', ['username' => $username])
            : 'The member already exists in the release'
        ;
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getMemberApprovedMessage($username)
    {
        return Lang::has('projects::release.memberApproved')
            ? Lang::get('projects::release.memberApproved', ['username' => $username])
            : 'The member has been approved'
        ;
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getMemberCancelledMessage($username)
    {
        return Lang::has('projects::release.memberCancelled')
            ? Lang::get('projects::release.memberCancelled', ['username' => $username])
            : 'The application has been declined'
        ;
    }

    /**

     * @return string
     */
    protected function getMemberRequestedMessage()
    {
        return Lang::has('projects::release.memberRequested')
            ? Lang::get('projects::release.memberRequested')
            : 'The application for participation has been applied'
        ;
    }

    /**
     * @param string $username
     * @return string
     */
    protected function getMemberDoesNotExistMessage($username)
    {
        return Lang::has('projects::release.memberDoesNotExist')
            ? Lang::get('projects::release.memberDoesNotExist', ['username' => $username])
            : 'The member has been approved'
        ;
    }

    /**
     * @return string
     */
    protected function getSubRipSavedMessage()
    {
        return Lang::has('projects::release.subRipSaved')
            ? Lang::get('projects::release.subRipSaved')
            : 'The subrip file has been persisted'
        ;
    }

    /**
     * @return string
     */
    protected function getSubRipRegeneratedMessage()
    {
        return Lang::has('projects::release.subRipRegenerated')
            ? Lang::get('projects::release.subRipRegenerated')
            : 'The subrip file has been regenerated'
        ;
    }

    /**
     * @return string
     */
    protected function getSubRipRegeneratedEmptyMessage()
    {
        return Lang::has('projects::release.subRipRegeneratedEmpty')
            ? Lang::get('projects::release.subRipRegeneratedEmpty')
            : 'The subrip file is empty'
        ;
    }
}