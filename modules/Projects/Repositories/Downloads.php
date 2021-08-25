<?php

namespace Modules\Projects\Repositories;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Modules\Projects\Entities\ReleaseDownload;

class Downloads extends DocumentRepository
{
    /**
     * @param \MongoId $releaseId
     * @return mixed
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function countByAbc(\MongoId $releaseId)
    {
        $startDate = new \DateTime();
        $startDate->setTime(00, 00);
        $endDate = clone $startDate;
        $endDate->setTime(23, 59, 59);
        $start = new \MongoDate($startDate->getTimestamp());
        $end = new \MongoDate($endDate->getTimestamp());
        $collection = $this->getDocumentManager()->getDocumentCollection($this->getDocumentName());
        $pipeline = [
            ['$match' => [
                'release' => new \MongoId($releaseId),
                'ca' => [
                    '$gte' => $start,
                    '$lte' => $end
                ]
            ]],
            ['$group' => [
                '_id' => null,
                'cyrillic' => ['$sum' => ['$cond' => [['$eq' => ['$abc', ReleaseDownload::CYRILLIC_ABC]], 1, 0]]],
                'latin' => ['$sum' => ['$cond' => [['$eq' => ['$abc', ReleaseDownload::LATIN_ABC]], 1, 0]]]
            ]]
        ];
        $readiness = $collection->aggregate($pipeline);
        return $readiness->current();
    }
}