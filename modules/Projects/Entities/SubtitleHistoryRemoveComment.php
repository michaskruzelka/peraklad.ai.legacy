<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryRemoveComment
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryRemoveComment extends SubtitleHistory
{
    public function getType()
    {
        return 'delCom';
    }

    public function represent()
    {
        return ' выдаліў свой каментарый.';
    }
}