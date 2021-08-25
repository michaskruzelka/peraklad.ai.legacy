<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ReleaseHistoryUnderway
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class ReleaseHistoryUnderway extends ReleaseHistory
{
    public function getType()
    {
        return 'underway';
    }
}