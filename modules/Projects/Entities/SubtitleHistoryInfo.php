<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistoryInfo
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleHistoryInfo
{
    /**
     * @var string
     * @ODM\Field(type="string", name="tr")
     */
    private $translation;

    /**
     * @var string
     * @ODM\Field(type="string", name="ti")
     */
    private $timing;

    /**
     * @var mixed
     * @ODM\Field(type="raw", name="raw")
     */
    private $rawData;

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param $rawData
     * @return $this
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @param string $translation
     * @return $this
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
        return $this;
    }

    /**
     * @return string
     */
    public function getTiming()
    {
        return $this->timing;
    }

    /**
     * @param string $timing
     * @return $this
     */
    public function setTiming($timing)
    {
        $this->timing = $timing;
        return $this;
    }
}