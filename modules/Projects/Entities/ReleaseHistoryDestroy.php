<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ReleaseHistoryDestroy
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class ReleaseHistoryDestroy extends ReleaseHistory
{
    public function getType()
    {
        return 'destroy';
    }
}