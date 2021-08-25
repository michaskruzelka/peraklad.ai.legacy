<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryUpload
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistorySaveTranslation extends SubtitleHistory
{
    public function getType()
    {
        return 'save';
    }

    public function represent()
    {
        return ' захаваў пераклад.';
    }
}