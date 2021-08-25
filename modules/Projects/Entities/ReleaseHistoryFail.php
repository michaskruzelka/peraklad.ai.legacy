<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ReleaseHistoryFail
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class ReleaseHistoryFail extends ReleaseHistory
{
    public function getType()
    {
        return 'fail';
    }
}