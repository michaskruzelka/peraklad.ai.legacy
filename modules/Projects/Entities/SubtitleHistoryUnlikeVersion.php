<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryUnlikeVersion
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryUnlikeVersion extends SubtitleHistory
{
    public function getType()
    {
        return 'unlikeVer';
    }

    public function represent()
    {
        return " больш не падабаецца версія перакладу: <i>{$this->getInfo()->getTranslation()}</i>";
    }
}