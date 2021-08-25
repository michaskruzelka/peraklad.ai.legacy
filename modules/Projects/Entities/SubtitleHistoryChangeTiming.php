<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryUpload
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryChangeTiming extends SubtitleHistory
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'timing';
    }

    /**
     * @return string
     */
    public function represent()
    {
        return " змяніў таймінг: <i>{$this->getInfo()->getTiming()}</i>";
    }
}