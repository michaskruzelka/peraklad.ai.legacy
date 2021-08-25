<?php

namespace Modules\Projects\Repositories;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Modules\Projects\Entities\Release;
use Auth;

class Projects extends DocumentRepository
{
    /**
     * @param string $slug
     * @return object
     */
    public function findOneBySlug($slug)
    {
        return $this->findOneBy(['info.slug' => $slug]);
    }

    /**
     * @param string $slug
     * @return int
     */
    public function checkBySlug($slug)
    {
        return $this->createQueryBuilder()
            ->select('info.slug')
            ->field('info.slug')
            ->equals($slug)
            ->getQuery()
            ->execute()
            ->count()
        ;
    }

    /**
     * Uses a text index on info.tt and info.ot fields
     * Default language - russian since it is supported by MongoDB
     * Language override field - lang
     * db.getCollection('projects').createIndex({
     *      "info.tt": "text",
     *      "info.ot": "text"
     *      }, {
     *      weights: {
     *          "info.tt": 10,
     *          "info.ot": 5
     *      },
     *      default_language: "russian",
     *      language_override: "lang"
     *  })
     *
     * @param string $key
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function search($key, $page = 0, $limit = 10)
    {
        return $this->createQueryBuilder()
            ->select(['info.ot', 'info.tt', 'info.plot', 'info.year', 'info.poster.fn', 'info.slug'])
            ->limit($limit)
            ->skip($page*$limit)
            ->text($key)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function searchCount($key)
    {
        return $this->createQueryBuilder()
            ->count()
            ->text($key)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * Here is the 1st update:
     * db.projects.update({}, {
     *    $pull: { "releases": ObjectId("...")}
     * },{ multi: true });
     * Here is the 2nd update:
     * db.projects.update({"episodes.releases": { $type: 7}}, {
     *    $pull: { "episodes.$.releases": ObjectId("...")}
     * },{ multi: true });
     *
     * Please, bear in mind the following thing:
     * The positional $ operator ("eisodes.$.releases") acts as a placeholder
     * for the *first* element that matches the query document.
     * Read more:
     * https://docs.mongodb.com/manual/reference/operator/update/positional/
     *
     * @param Release $release
     * @return $this
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function pullRelease(Release $release)
    {
        $releaseId = new \MongoId($release->getId());
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $filter = ['$pull' => ['releases' => $releaseId]];
        $collection->update([], $filter, ["multi" => true]);
        $filter = ['$pull' => ['episodes.$.releases' => $releaseId]];
        $collection->update(['episodes.releases' => ['$type' => 7]], $filter, ["multi" => true]);
        return $this;
    }

    /**
     * @param Release $release
     * @return object
     */
    public function getByRelease(Release $release)
    {
        return $this->findOneBy(['releases' => $release->getId()]);
    }

    /**
     * @param null $userId
     * @return array
     */
    public function getByUserId($userId = null)
    {
        if (is_null($userId)) {
            $userId = Auth::id();
        }
        return $this->findBy(['owner.id' => new \MongoId($userId)]);
    }

    /**
     * @param array $releasesIds
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getLangsListByReleases($releasesIds = [])
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['releases' => ['$in' => $releasesIds]]],
            ['$group' => ['_id' => '$info.language', 'count' => ['$sum' => 1]]],
            ['$sort' => ['count' => -1]],
            ['$limit' => 5],
            ['$project' => ['_id' => 0, 'count' => 1, 'lang' => '$_id']]
        ];
        $langs = $collection->aggregate($pipeline)->toArray();
        return array_column($langs, 'lang', 'count');
    }

    /**
     * @param $lang
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getReleasesIdsByLang($lang)
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['info.language.iso' => $lang]],
            ['$unwind' => '$releases'],
            ['$project' => ['_id' => 0, 'releaseId' => '$releases']]
        ];
        $releasesIds = $collection->aggregate($pipeline)->toArray();
        $releasesIds = array_column($releasesIds, 'releaseId');
        return $releasesIds;
    }

    /**
     * @param array $releasesIds
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getYearsListByReleases($releasesIds = [])
    {
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['type' => 'series', 'episodes.releases' => ['$in' => $releasesIds]]],
            ['$unwind' => '$episodes'],
            ['$group' => ['_id' => '$episodes.info.year', 'count' => ['$sum' => 1]]],
            ['$sort' => ['count' => -1]],
            ['$limit' => 5],
            ['$project' => ['_id' => 0, 'count' => 1, 'year' => '$_id']]
        ];
        $episodesYears = $collection->aggregate($pipeline)->toArray();
        $pipeline = [
            ['$match' => ['type' => 'movie', 'releases' => ['$in' => $releasesIds]]],
            ['$group' => ['_id' => '$info.year', 'count' => ['$sum' => 1]]],
            ['$sort' => ['count' => -1]],
            ['$limit' => 5],
            ['$project' => ['_id' => 0, 'count' => 1, 'year' => '$_id']]
        ];
        $years = $collection->aggregate($pipeline)->toArray();
        $years = array_column($years, 'count', 'year');
        $episodesYears = array_column($episodesYears, 'count', 'year');
        foreach ($episodesYears as $year => $count) {
            if ( ! isset($years[$year])) {
                $years[$year] = 0;
            }
            $years[$year] += $count;
        }
        arsort($years);
        return array_slice($years, 0, 5, true);
    }

    /**
     * @param $year
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getReleasesIdsByYear($year)
    {
        $year =  (int) $year;
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => ['type' => 'series']],
            ['$unwind' => '$episodes'],
            ['$match' => ['episodes.info.year' => $year, 'episodes.releases' => ['$exists' => true]]],
            ['$unwind' => '$releases'],
            ['$project' => ['_id' => 0, 'releaseId' => '$episodes.releases']]
        ];
        $episodesReleasesIds = $collection->aggregate($pipeline)->toArray();
        $episodesReleasesIds = array_column(array_column($episodesReleasesIds, 'releaseId'), 0);
        $pipeline = [
            ['$match' => ['type' => 'movie', 'info.year' => $year]],
            ['$unwind' => '$releases'],
            ['$match' => ['releases' => ['$nin' => $episodesReleasesIds]]],
            ['$project' => ['_id' => 0, 'releaseId' => '$releases']]
        ];
        $releasesIds = $collection->aggregate($pipeline)->toArray();
        $releasesIds = array_column($releasesIds, 'releaseId');
        $releasesIds = array_merge($episodesReleasesIds, $releasesIds);
        return $releasesIds;
    }
}