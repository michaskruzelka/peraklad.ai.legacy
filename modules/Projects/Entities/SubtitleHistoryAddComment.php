<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryAddComment
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryAddComment extends SubtitleHistory
{
    public function getType()
    {
        return 'addCom';
    }

    public function represent()
    {
        return ' дадаў каментарый.';
    }
}