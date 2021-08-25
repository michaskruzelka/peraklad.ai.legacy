<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryUpload
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryUpload extends SubtitleHistory
{
    public function getType()
    {
        return 'upload';
    }

    public function represent()
    {
        return ' дадаў субтытр.';
    }
}