<?php

namespace Modules\Projects\Repositories;

use Captioning\File as CaptionFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Modules\Projects\Entities\Release;

class Subtitles extends DocumentRepository
{
    /**
     * @param array $captions
     * @param Release $release
     * @return ArrayCollection
     */
    public function importOriginal(array $captions, Release $release)
    {
        return $this->import($captions, $release, 'importOriginal');
    }

    /**
     * @param array $captions
     * @param Release $release
     * @return ArrayCollection
     */
    public function importTranslated(array $captions, Release $release)
    {
        return $this->import($captions, $release, 'importTranslated');
    }

    /**
     * @param array $captions
     * @param Release $release
     * @return ArrayCollection
     */
    protected function import(array $captions, Release $release, $methodName)
    {
        $subtitles = [];
        $subtitlesResult = new ArrayCollection();
        $number = 1;

        foreach ($captions as $caption) {
            $subtitle = app()->build($this->getDocumentName())
                ->$methodName($caption, $release, $number)
            ;
            $subtitlesResult->add($subtitle);

            $history = [];
            foreach ($subtitle->getHistory() as $event) {

                $info = [];
                if ($translation = $event->getInfo()->getTranslation()) {
                    $info['tr'] = $translation;
                }

                $history[] = [
                    'type' => $event->getType(),
                    'us' => $event->getUserId(),
                    'ca' => new \MongoDate($event->getCreatedAt()->getTimestamp()),
                    'info' => $info
                ];
            }

            $subtitles[] = [
                'n' => $subtitle->getNumber(),
                'release' => new \MongoId($subtitle->getRelease()->getId()),
                'ot' => $subtitle->getOriginalText(),
                'tt' => $subtitle->getTranslatedText(),
                'st' => $subtitle->getStatus(),
                'tr' => [
                    'bl' => $subtitle->getTimeRange()->getBottomLine(),
                    'tl' => $subtitle->getTimeRange()->getTopLine()
                ],
                'hist' => $history
            ];

            $number++;
        }

        $this->getDocumentManager()
            ->getDocumentCollection($this->getDocumentName())
            ->batchInsert($subtitles)
        ;

        return $subtitlesResult;
    }

    /**
     * @param mixed (array|Cursor|ArrayCollection) $collection
     * @return CaptionFile
     */
    public function export($collection)
    {
        $file = app()->make(CaptionFile::class);
        $file->setLineEnding(CaptionFile::WINDOWS_LINE_ENDING);
        foreach ($collection as $subtitle) {
            $subtitle->export($file);
        }
        $file->build();
        return $file;
    }

    /**
     * @param Release $release
     * @return array|bool
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function removeByRelease(Release $release)
    {
        return $this->getDocumentManager()
            ->getDocumentCollection($this->getDocumentName())
            ->remove(['release' => new \MongoId($release->getId())])
        ;
    }

    /**
     * @param string $releaseId
     * @param string $status
     * @param int $n
     * @param string $search
     * @return object
     */
    public function getByNumber($releaseId, $status, $n, $search)
    {
        $filter = [
            'release' => new \MongoId($releaseId)
        ];
        if ($n) {
            $filter['n'] = $n;
            $sort = [];
        } else {
            $sort = ['n' => 1];
        }
        if ($status != 'all') {
            $filter['st'] = $status;
        }
        if (strlen($search) > 0) {
            $filter['$or'] = [];
            foreach (explode(' ', $search) as $searchItem) {
                $regex = new \MongoRegex("/\b{$searchItem}/i");
                array_push($filter['$or'], ['ot' => ['$regex' => $regex]]);
                array_push($filter['$or'], ['tt' => ['$regex' => $regex]]);
            }
        }
        return current($this->findBy($filter, $sort, 1));
    }

    /**
     * @param string $releaseId
     * @param string $status
     * @param int $n
     * @param string $search
     * @return array
     */
    public function getBunchByNumber($releaseId, $status, $n, $search)
    {
        $n =  (int) $n;
        $limitPerPage = config('projects.subtitlesLimitPerPage');
        $limit = (($n-1) % $limitPerPage) + 1;

        $filter = [
            'release' => new \MongoId($releaseId),
            'n' => ['$lt' => $n]
        ];
        if ('all' != $status) {
            $filter['st'] = $status;
        }
        if (strlen($search) > 0) {
            $orFilter = [];
            foreach (explode(' ', $search) as $searchItem) {
                $regex = new \MongoRegex("/\b{$searchItem}/i");
                array_push($orFilter, ['ot' => ['$regex' => $regex]]);
                array_push($orFilter, ['tt' => ['$regex' => $regex]]);
            }
            $filter['$or'] = $orFilter;
        }
        $prevSubs = $this->findBy($filter, ['n' => -1], $limit);

        $limit = $limitPerPage - count($prevSubs) + 2;
        $filter = [
            'release' => new \MongoId($releaseId),
            'n' => ['$gte' => $n]
        ];
        if ('all' != $status) {
            $filter['st'] = $status;
        }
        if (strlen($search) > 0) {
            $filter['$or'] = $orFilter;
        }
        $nextSubs = $this->findBy($filter, ['n' => 1], $limit);
        $subs = array_merge($prevSubs, $nextSubs);
        $subs = array_sort($subs, function($sub, $key) {
            // Modules\Projects\Entities\Subtitle $sub
            return $sub->getNumber();
        });

        return $subs;
    }

    /**
     * @param $releaseId
     * @param $n
     * @return mixed
     */
    public function getPrevTiming($releaseId, $n)
    {
        return $this->createQueryBuilder()
            ->hydrate(false)
            ->select('tr')
            ->exclude('_id')
            ->field('release')
            ->equals(new \MongoId($releaseId))
            ->field('n')
            ->lt($n)
            ->sort('n', -1)
            ->limit(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @param $releaseId
     * @param $n
     * @return mixed
     */
    public function getNextTiming($releaseId, $n)
    {
        return $this->createQueryBuilder()
            ->hydrate(false)
            ->select('tr')
            ->exclude('_id')
            ->field('release')
            ->equals(new \MongoId($releaseId))
            ->field('n')
            ->gt($n)
            ->sort('n', 1)
            ->limit(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @param $releaseId
     * @return float
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getReadiness($releaseId)
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['release' => new \MongoId($releaseId)]],
            ['$group' => [
                '_id' => '$release',
                'total' => ['$sum' => 1],
                'saved' => ['$sum' => ['$cond' => [['$eq' => ['$st', 'sa']], 1, 0]]],
                'underway' => ['$sum' => ['$cond' => [['$eq' => ['$st', 'un']], 0.5, 0]]]
            ]],
            ['$project' => [
                '_id' => 0,
                'percent' => ['$multiply' => [['$sum' => [
                    ['$divide' => ['$saved', '$total']],
                    ['$divide' => ['$underway', '$total']]
                ]], 100]]
            ]]
        ];
        $readiness = $collection->aggregate($pipeline);
        return $readiness->current()['percent'];
    }

    /**
     * @param \MongoId $releaseId
     * @return mixed
     */
    public function getByRelease($releaseId)
    {
        return $this->createQueryBuilder()
            ->select(['tr', 'tt', 'st'])
            ->field('release')
            ->equals($releaseId)
            ->field('st')
            ->equals('sa')
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param \MongoId $releaseId
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function countByStatuses(\MongoId $releaseId)
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['release' => new \MongoId($releaseId)]],
            ['$group' => [
                '_id' => '$release',
                'saved' => ['$sum' => ['$cond' => [['$eq' => ['$st', 'sa']], 1, 0]]],
                'underway' => ['$sum' => ['$cond' => [['$eq' => ['$st', 'un']], 1, 0]]],
                'clean' => ['$sum' => ['$cond' => [['$eq' => ['$st', 'cl']], 1, 0]]]
            ]]
        ];
        $readiness = $collection->aggregate($pipeline);
        return $readiness->current();
    }

    /**
     * @param Release $release
     * @param $period
     * @return \Doctrine\MongoDB\Iterator
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     * @throws \Exception
     */
    public function getTrends(Release $release, $period)
    {
        switch ($period) {
            case 'day':
                $format = '%Y-%m-%d';
                break;
            case 'week':
                $format = '%w';
                break;
            default:
                throw new \Exception('Unsupported period');
        }
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $match1 = ['$match' => ['release' => $release->getId(true)]];
        $unwind = ['$unwind' => '$hist'];
        $match2 = ['$match' => ['hist.type' => 'save']];
        $group1 = ['$group' => [
            '_id' => '$_id',
            'ca' => ['$last' => '$hist.ca'],
            'us' => ['$last' => '$hist.us']
        ]];
        $project1 = ['$project' => [
            'period' => ['$dateToString' => ['format' => $format, 'date' => '$ca']],
            'user' => '$us',
            '_id' => 0
        ]];
        $group2 = ['$group' => [
            '_id' => ['period' => '$period', 'user' => '$user'],
            'translations' => ['$sum' => 1]
        ]];
        $group3 = ['$group' => [
            '_id' => '$_id.period',
            'users' => ['$push' => '$_id.user'],
            'translations' => ['$push' => '$translations']
        ]];
        $project2 = ['$project' => [
            '_id' => 0,
            'period' => '$_id',
            'users' => 1,
            'translations' => 1,
            'translationsSum' => ['$sum' => '$translations']
        ]];
        $pipeline = [$match1, $unwind, $match2, $group1, $project1, $group2, $group3, $project2];
        $periodTrends = $collection->aggregate($pipeline);
        $periodTrends = iterator_to_array($periodTrends);
        if ('day' == $period) {
            return $this->formatDayTrends($periodTrends, $release);
        }
        return $this->formatWeekTrends($periodTrends, $release);
    }

    /**
     * @param array $periodTrends
     * @param Release $release
     * @return array
     */
    protected function formatWeekTrends(array $periodTrends, Release $release)
    {
        $finalTrends = [];
        foreach (config('projects.statPeriodsWeek') as $dayNum => $dayName) {
            $periodTrend = current(array_filter($periodTrends, function ($_trend) use ($dayNum) {
                return $_trend['period'] == $dayNum;
            }));
            $trend = [
                'date' => $dayName,
                'sum' => empty($periodTrend['translationsSum']) ? 0 : $periodTrend['translationsSum']
            ];
            foreach ($release->getConfirmedMembers() as $member) {
                if ( ! empty($periodTrend)
                    && ($key = array_search($member->getUserId(), $periodTrend['users'])) !== false
                ) {
                    $translations = $periodTrend['translations'][$key];
                } else {
                    $translations = 0;
                }
                $trend[$member->getUserId()] = $translations;
            }
            array_push($finalTrends, $trend);
        }
        return $finalTrends;
    }

    /**
     * @param array $periodTrends
     * @param Release $release
     * @return array
     */
    protected function formatDayTrends(array $periodTrends, Release $release)
    {
        $startDate = $release->getStartDate();
        $endDate = $release->getEndDate();
        $oneDayInterval = new \DateInterval('P1D');
        $finalTrends = [];
        $format = 'Y-m-d';
        while ($startDate->format($format) <= $endDate->format($format)) {
            $periodTrend = current(array_filter($periodTrends, function ($_trend) use ($startDate, $format) {
                return $_trend['period'] == $startDate->format($format);
            }));
            $trend = [
                'date' => $startDate->format($format),
                'sum' => empty($periodTrend['translationsSum']) ? 0 : $periodTrend['translationsSum']
            ];
            foreach ($release->getConfirmedMembers() as $member) {
                if ( ! empty($periodTrend)
                    && ($key = array_search($member->getUserId(), $periodTrend['users'])) !== false
                ) {
                    $translations = $periodTrend['translations'][$key];
                } else {
                    $translations = 0;
                }
                $trend[$member->getUserId()] = $translations;
            }
            array_push($finalTrends, $trend);
            $startDate->add($oneDayInterval);
        }
        return $finalTrends;
    }

    /**
     * @param string $userId
     * @return int
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function countTranslationsByUser($userId)
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$unwind' => '$hist'],
            ['$match' => ['hist.type' => 'save']],
            ['$group' => [
                '_id' => '$_id',
                'ca' => ['$last' => '$hist.ca'],
                'us' => ['$last' => '$hist.us']
            ]],
            ['$match' => ['us' => $userId]],
            ['$group' => ['_id' => null, 'count' => ['$sum' => 1]]]
        ];
        return  (int) $collection->aggregate($pipeline)->getSingleResult()['count'];
    }
}