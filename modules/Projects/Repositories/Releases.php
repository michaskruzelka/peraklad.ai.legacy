<?php

namespace Modules\Projects\Repositories;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Modules\Projects\Entities\Project;
use MongoCode;
use Modules\Users\Entities\User;
use Modules\Users\Entities\UserAvatar;
use Modules\Projects\Entities\ReleaseMember;

class Releases extends DocumentRepository
{
    /**
     * Uses a text index on info.tt and info.ot fields
     * Default language - russian since it is supported by MongoDB
     * Language override field - lang
     * db.getCollection('releases').createIndex({
     *      "mon": "text",
     *      "mtn": "text"
     *      }, {
     *      weights: {
     *          "mtn": 10,
     *          "mon": 5
     *      },
     *      default_language: "russian",
     *      language_override: "lang"
     *  })
     * @param null|string $userId
     * @param int $page
     * @param string $state
     * @param string $search
     * @param string $year
     * @param string $lang
     * @param bool $exclude
     * @param string $in
     * @param string $mode
     * @return mixed
     */
    public function getByUserId(
        $userId = null,
        $page = 1,
        $state = 'all',
        $search = null,
        $year = null,
        $lang = null,
        $exclude = false,
        $in = 'all',
        $mode = 'all'
    )
    {
        if ($page < 1) {
            $page = 1;
        }
        $limit = config('projects.releasesLimitPerPage');
        $qb = $this->createQueryBuilder();
        if ( ! is_null($userId)) {
            if ($exclude) {
                $qb->field('owner.id')->notEqual($userId);
            } else {
                if ('owner' == $in) {
                    $qb->field('owner.id')->equals($userId);
                } elseif ('member' == $in) {
                    $qb->field('members.id')->equals($userId);
                    $qb->field('owner.id')->notEqual($userId);
                }
            }
        }
        if ($mode != 'all') {
            $qb->field('mode')->equals($mode);
        }
        if ($state != 'all') {
            $states =  (array) $state;
            if (in_array('fa', $states)) {
                $states[] = 'de';
            }
            $qb->field('state')->in($states);
        }
        if ( ! is_null($search) && $search) {
            $qb->text($search);
        }
        if ( ! is_null($year) && $year) {
            $releasesIds = $this->getDocumentManager()
                ->getRepository(Project::class)
                ->getReleasesIdsByYear($year)
            ;
            $qb->field('_id')->in($releasesIds);
        }
        if ( ! is_null($lang) && $lang) {
            $releasesIds = $this->getDocumentManager()
                ->getRepository(Project::class)
                ->getReleasesIdsByLang($lang)
            ;
            $qb->field('_id')->in($releasesIds);
        }
        return $qb->sort('ua', 'desc')
            ->limit($limit)
            ->skip(($page-1)*$limit)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param null $userId
     * @param bool $exclude
     * @param string $in
     * @param string $mode
     * @return array
     * @throws \Exception
     */
    public function getAllIdsByUserId($userId = null, $exclude = false, $in = 'all', $mode = 'all')
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [];
        if ('all' != $mode) {
            $pipeline[] = ['$match' => ['mode' => $mode]];
        }
        if ( ! is_null($userId)) {
            if ($exclude) {
                $pipeline[] = ['$match' => ['owner.id' => ['$ne' => $userId]]];
            } else {
                if ('owner' == $in) {
                    $pipeline[] = ['$match' => ['owner.id' => $userId]];
                } elseif ('member' == $in) {
                    $pipeline[] = ['$match' => [
                        'members.id' => $userId,
                        'owner.id' => ['$ne' => $userId]
                    ]];
                } else {
                    throw new \Exception('Unsupported $in value: ' . $in);
                }
            }
        }
        $pipeline[] =
            ['$group' => [
                '_id' => null,
                'ids' => ['$addToSet' => '$_id']
            ]
        ];
        return $collection->aggregate($pipeline)->getSingleResult()['ids'];
    }

    /**
     * @param null $userId
     * @param string $search
     * @param string $year
     * @param string $lang
     * @param bool $exclude
     * @param string $in
     * @param string $mode
     * @return mixed
     */
    public function getMyCountGroupedByState(
        $userId = null,
        $search = null,
        $year = null,
        $lang = null,
        $exclude = false,
        $in = 'all',
        $mode = 'all'
    )
    {
        $reduceCode = new MongoCode(
            'function(current, total) { total.count++; }'
        );
        $finalizeCode = new MongoCode(
            'function(result) {
                result.count = NumberInt(result.count);
                if ("de" === result.state) {
                    result.state = "fa";
                }
            }'
        );
        $qb = $this->createQueryBuilder()
            ->group(['state' => 1], ['count' => 0])
            ->reduce($reduceCode)
            ->finalize($finalizeCode)
        ;
        if ('all' != $mode) {
            $qb->field('mode')->equals($mode);
        }
        if ( ! is_null($userId)) {
            if ($exclude) {
                $qb->field('owner.id')->notEqual($userId);
            } else {
                if ('owner' == $in) {
                    $qb->field('owner.id')->equals($userId);
                } elseif ('member' == $in) {
                    $qb->field('members.id')->equals($userId);
                    $qb->field('owner.id')->notEqual($userId);
                }
            }
        }
        if ( ! is_null($search) && $search) {
            $qb->text($search);
        }
        if ( ! is_null($year) && $year) {
            $releasesIds = $this->getDocumentManager()
                ->getRepository(Project::class)
                ->getReleasesIdsByYear($year)
            ;
            $qb->field('_id')->in($releasesIds);
        }
        if ( ! is_null($lang) && $lang) {
            $releasesIds = $this->getDocumentManager()
                ->getRepository(Project::class)
                ->getReleasesIdsByLang($lang)
            ;
            $qb->field('_id')->in($releasesIds);
        }
        return $qb->eagerCursor(true)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param null $userId
     * @param null $search
     * @param null $year
     * @param null $lang
     * @param bool|false $exclude
     * @param string $in
     * @param string $mode
     * @return mixed
     */
    public function getStatesInfo(
        $userId = null,
        $search = null,
        $year = null,
        $lang = null,
        $exclude = false,
        $in = 'all',
        $mode = 'all'
    )
    {
        $counts = $this->getMyCountGroupedByState($userId, $search, $year, $lang, $exclude, $in, $mode);
        $statesInfo = config('projects.statesDetailed');
        foreach ($counts as $value) {
            $count = $value['count'];
            $state = $value['state'];
            if ( ! isset($statesInfo[$state]['count'])) {
                $statesInfo[$state]['count'] = 0;
            }
            $statesInfo[$state]['count'] += $count;
        }
        return $statesInfo;
    }

    /**
     * @param array $excl
     * @return \Doctrine\MongoDB\Iterator
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getOwners($excl = [])
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [];
        if ( ! empty($excl)) {
            $pipeline[] = ['$match' => ['owner.id' => ['$nin' => $excl]]];
        }
        $pipeline[] = ['$group' => [
            '_id' => '$owner.id',
//            'username' => ['$last' => '$owner.un'],
            'count' => ['$sum' => 1]
        ]];
        $pipeline[] = ['$sort' => [
            'count' => -1
        ]];
//        $pipeline[] = ['$lookup' => [
//            'from' => 'users',
//            'localField' => '_id',
//            'foreignField' => '_id',
//            'as' => 'users'
//        ]];
//        $pipeline[] = ['$unwind' => '$users'];
        $pipeline[] = ['$project' => [
            '_id' => 0,
            'userId' => '$_id',
            'count' => 1,
            //'username' => 1,
            //'avatar' => '$users.avatar.fn'
        ]];
        return $collection->aggregate($pipeline);
    }

    /**
     * @param string $userId
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function countByOwner($userId)
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['owner.id' => $userId]],
            ['$group' => ['_id' => '$owner.id', 'count' => ['$sum' => 1]]]
        ];
        return  (int) $collection->aggregate($pipeline)->getSingleResult()['count'];
    }

    /**
     * @param string $userId
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function countByMember($userId)
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['owner.id' => ['$ne' =>  $userId], 'members.id' => $userId]],
            ['$group' => ['_id' => '$_id', 'count' => ['$sum' => 1]]]
        ];
        return  (int) $collection->aggregate($pipeline)->getSingleResult()['count'];
    }

    /**
     * @param array $releases
     * @param array $usersBasicInfo
     * @return array
     */
    public function getMembersInfo(array $releases, array $usersBasicInfo)
    {
        $releaseMembers = [
            'selected' => [],
            'selectable' => []
        ];
        array_walk($releases, function($release) use (&$releaseMembers, $usersBasicInfo) {
            $releaseId = $release->getId();
            $releaseMembers['selected'][$releaseId] = [];
            foreach ($release->getMembers() as $member) {
                $id = $member->getUserId();
                $memberInfo = [
                    'id' => $id,
                    'avatar' => UserAvatar::getSrcByFilename($usersBasicInfo[$id]['avatar']['fn']),
                    'state' => $member->getState(),
                    'removable' => $release->belongsToYou()
                        ?  ! $release->belongsToUser($id)
                        : $member->isYou(),
                    'acceptable' => $release->belongsToYou() &&  ! $release->belongsToUser($id)
                ];
                $releaseMembers['selected'][$releaseId][$id] = $memberInfo;
            }
            $releaseMembers['selectable'][$releaseId] = [];
            if ($release->isUnderway()) {
                foreach ($usersBasicInfo as $id => $userData) {
                    if ($userData['st'] == User::INACTIVE_STATUS
                        || ($release->belongsToYou() && $id == \Auth::id())
                        || ( ! $release->belongsToYou() && $id != \Auth::id())
                    ) {
                        continue;
                    }
                    $memberInfo = isset($releaseMembers['selected'][$releaseId][$id])
                        ? $releaseMembers['selected'][$releaseId][$id]
                        : [
                            'id' => $id,
                            'avatar' => UserAvatar::getSrcByFilename($userData['avatar']['fn']),
                            'state' => $release->belongsToYou()
                                ? ReleaseMember::CONFIRMED_STATE
                                : ReleaseMember::PENDING_STATE,
                            'acceptable' => $release->belongsToYou() &&  ! $release->belongsToUser($id),
                            'removable' => true
                        ]
                    ;
                    array_push($releaseMembers['selectable'][$releaseId], $memberInfo);
                }
            }
        });
        return $releaseMembers;
    }
}