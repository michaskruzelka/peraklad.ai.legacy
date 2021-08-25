<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ReleaseHistoryComplete
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class ReleaseHistoryComplete extends ReleaseHistory
{
    public function getType()
    {
        return 'complete';
    }
}