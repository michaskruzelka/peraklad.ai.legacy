<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryAddVersion
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryAddVersion extends SubtitleHistory
{
    public function getType()
    {
        return 'addVer';
    }

    public function represent()
    {
        return ' дадаў сваю версію перакладу.';
    }
}