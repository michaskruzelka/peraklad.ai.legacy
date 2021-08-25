<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryRemoveVersion
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryRemoveVersion extends SubtitleHistory
{
    public function getType()
    {
        return 'delVer';
    }

    public function represent()
    {
        return ' выдаліў сваю версію перакладу.';
    }
}