<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleTimeRange
 * @package Modules\Projects\Entities
 * @ODM\EmbeddedDocument
 */
class SubtitleTimeRange
{
    /**
     * @ODM\Field(type="string", name="bl")
     */
    private $bottomLine;

    /**
     * @ODM\Field(type="string", name="tl")
     */
    private $topLine;

    /**
     * @param bool $convert
     * @return mixed
     */
    public function getBottomLine($convert = false)
    {
        $bottomLine = $this->bottomLine;
        if ($convert) {
            $bottomLine = $this->convertToMilliseconds($bottomLine);
        }
        return $bottomLine;
    }

    /**
     * @param mixed $bottomLine
     * @return $this
     */
    public function setBottomLine($bottomLine)
    {
        $this->bottomLine = $bottomLine;
        return $this;
    }

    /**
     * @param bool $convert
     * @return mixed
     */
    public function getTopLine($convert = false)
    {
        $topLine = $this->topLine;
        if ($convert) {
            $topLine = $this->convertToMilliseconds($topLine);
        }
        return $topLine;
    }

    /**
     * @param mixed $topLine
     * @return $this
     */
    public function setTopLine($topLine)
    {
        $this->topLine = $topLine;
        return $this;
    }

    /**
     * @param $time
     * @return mixed
     */
    public static function convertToMilliseconds($time)
    {
        sscanf($time, "%d:%d:%d,%d", $hours, $minutes, $seconds, $millis);
        $millis = ($hours*60*60 + $minutes*60 + $seconds) * 1000 + $millis;
        return $millis;
    }

    /**
     * @return string
     */
    public function subRipRepresent()
    {
        return $this->getBottomLine() . ' --> ' .$this->getTopLine();
    }
}