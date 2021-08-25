<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryApproveVersion
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryApproveVersion extends SubtitleHistory
{
    public function getType()
    {
        return 'appVer';
    }

    public function represent()
    {
        return " выбраў версію перакладу: <i>{$this->getInfo()->getTranslation()}</i>";
    }
}