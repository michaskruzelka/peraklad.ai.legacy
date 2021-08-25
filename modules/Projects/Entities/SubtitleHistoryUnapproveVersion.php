<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryUnapproveVersion
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryUnapproveVersion extends SubtitleHistory
{
    public function getType()
    {
        return 'unAppVer';
    }

    public function represent()
    {
        return " адхіліў выбраную версію перакладу: <i>{$this->getInfo()->getTranslation()}</i>";
    }
}