<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryUpload
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryChangeTranslation extends SubtitleHistory
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'translate';
    }

    /**
     * @return string
     */
    public function represent()
    {
        $translation = $this->getInfo()->getTranslation() ? htmlentities($this->getInfo()->getTranslation()) : '- ';
        return " змяніў пераклад: <i>{$translation}</i>";
    }
}