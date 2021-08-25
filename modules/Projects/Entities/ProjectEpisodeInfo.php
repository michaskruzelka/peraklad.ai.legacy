<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class ProjectEpisodeInfo
{
    /**
     * @var string
     * @ODM\Field(type="string", name="tt")
     */
    private $translatedTitle;

    /**
     * @var string
     * @ODM\Field(type="string", name="ot")
     */
    private $originalTitle;

    /**
     * @var string
     * @ODM\Field(type="string", name="imdb")
     */
    private $imdbId;

    /**
     * @var int
     * @ODM\Field(type="int")
     */
    private $year;

    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getTranslatedTitle()
    {
        return $this->translatedTitle;
    }

    /**
     * @param string $translatedTitle
     * @return $this
     */
    public function setTranslatedTitle($translatedTitle)
    {
        $this->translatedTitle = $translatedTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalTitle()
    {
        return $this->originalTitle;
    }

    /**
     * @param string $originalTitle
     * @return $this
     */
    public function setOriginalTitle($originalTitle)
    {
        $this->originalTitle = $originalTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * @param string $imdbId
     * @return $this
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }
}