<?php

namespace Modules\Users\Http\Controllers;

use Modules\Users\Entities\User;
use Modules\Users\Http\Requests\RemoveProfileRequest;
use Modules\Users\Http\Requests\UpdateProfileRequest;
use Pingpong\Modules\Routing\Controller;
use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Release;
use Modules\Projects\Entities\Subtitle;
use Lang;

class UsersController extends Controller
{
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
     * @param string $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($userId = 'all')
    {
        $usersRep = $this->dm->getRepository(User::class);
        $releasesRep = $this->dm->getRepository(Release::class);
        $owners = array_column($releasesRep->getOwners([\Auth::id()])->toArray(), 'userId');
        $usersBasicInfo = $usersRep->getAllBasic(false)->toArray();
        $usersBasicInfo = array_sort($usersBasicInfo, function($userBasicInfo) {
            return $userBasicInfo['st'] =! User::ACTIVE_STATUS;
        });
        $users = array_keys($usersBasicInfo);
        if ('all' != $userId &&  ! in_array($userId, $users)) {
            abort(404);
        }
        $users = array_diff(array_unique(array_merge($owners, $users)), [\Auth::id()]);
        return view('users::list', compact('users', 'userId'));
    }

    public function geo()
    {

    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewProfile(User $user)
	{
        $releaseRep = $this->dm->getRepository(Release::class);
        $subtitleRep = $this->dm->getRepository(Subtitle::class);
        $ownsCount = $releaseRep->countByOwner($user->getId());
        $participatesCount = $releaseRep->countByMember($user->getId());
        $subsCount = $subtitleRep->countTranslationsByUser($user->getId());
        return view('users::my-profile', compact('user', 'ownsCount', 'participatesCount', 'subsCount'));
	}

    /**
     * @param User $user
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user, UpdateProfileRequest $request)
    {
        // update base info
        $user->setAuthPassword($request->getPassword())
            ->setName($request->getName())
            ->setEmail($request->getEmail())
        ;
        // update address info
        $user->getAddress()->setCity($request->getCity())->setCountry($request->getCountry());
        // update avatar
        $user->getAvatar()->setRowData($request->getAvatar())->import();
        // update social
        foreach (config('users.socialNetworks') as $type => $network) {
            $user->removeSocialProfile($type);
            if ($link = $request->input($type)) {
                $user->addSocialProfile($type, $link);
            }
        }
        // persist
        $this->dm->persist($user);
        $this->dm->flush($user);
        // response
        return response()->json(['status' => 'ok', 'response' => $this->getUserUpdatedMessage()]);
    }

    /**
     * @param User $user
     * @param RemoveProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(User $user, RemoveProfileRequest $request)
    {
        $user->setInactiveStatus();
        $this->dm->persist($user);
        $this->dm->flush();
        \Auth::logout();
        return response()->json([
            'status' => 'ok',
            'response' => route(config('users.redirects.logout'))
        ]);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshMyProfilePanel(User $user)
    {
        $view = view('users::panels.my-profile.info', compact('user'));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshAvatarPanel(User $user)
    {
        $view = view('users::panels.my-profile.avatar', compact('user'));
        return response()->json(['status' => 'ok', 'response' => $view->render()]);
    }

    /**
     * @return string
     */
    protected function getUserUpdatedMessage()
    {
        return Lang::has('users::users.userUpdated')
            ? Lang::get('users::users.userUpdated')
            : 'The changes have saved'
        ;
    }
}