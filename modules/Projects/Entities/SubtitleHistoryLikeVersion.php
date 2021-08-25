<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryLikeVersion
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryLikeVersion extends SubtitleHistory
{
    public function getType()
    {
        return 'likeVer';
    }

    public function represent()
    {
        return " упадабаў версію перакладу: <i>{$this->getInfo()->getTranslation()}</i>";
    }
}