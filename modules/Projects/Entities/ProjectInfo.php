<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class ProjectInfo
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
     * @var float|null
     * @ODM\Field(type="float", name="rat")
     */
    private $imdbRating;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    private $plot;

    /**
     * @var ProjectInfoPoster
     * @ODM\EmbedOne(targetDocument="ProjectInfoPoster")
     */
    private $poster;

    /**
     * @var int
     * @ODM\Field(type="int")
     */
    private $year;

    /**
     * @var ProjectInfoLanguage
     * @ODM\EmbedOne(targetDocument="ProjectInfoLanguage")
     */
    private $language;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    private $slug;

    /**
     * @var array
     */
    private $garbage = [];

    public function __construct()
    {
        $this->language = app()->build('Modules\Projects\Entities\ProjectInfoLanguage');
        $this->poster = app()->build('Modules\Projects\Entities\ProjectInfoPoster');
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
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
     * @return float
     */
    public function getImdbRating()
    {
        return $this->imdbRating;
    }

    /**
     * @param float|null $imdbRating
     * @return $this
     */
    public function setImdbRating($imdbRating)
    {
        $this->imdbRating = $imdbRating;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlot()
    {
        return $this->plot;
    }

    /**
     * @param string $plot
     * @return $this
     */
    public function setPlot($plot)
    {
        $this->plot = $plot;
        return $this;
    }

    /**
     * @return ProjectInfoPoster
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param ProjectInfoPoster $poster
     * @return $this
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;
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

    /**
     * @return ProjectInfoLanguage
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param ProjectInfoLanguage $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return array
     */
    public function getGarbage()
    {
        return $this->garbage;
    }

    /**
     * @param $garbage
     * @return $this
     */
    public function setGarbage(array $garbage)
    {
        $this->garbage = $garbage;
        return $this;
    }

    /**
     * @return boolean
     */
    public function import()
    {
        $importer = app()->make('Modules\Projects\Contracts\ImdbImporter');
        return $importer->import($this);
    }

    /**
     * @param Language $language
     * @return $this
     */
    public function importLanguage(Language $language)
    {
        $this->getLanguage()->setIso6393b($language->getId())
            ->setNativeName($language->getNativeName())
            ->setEnglishName($language->getEnglishName())
            ->setBelName($language->getBelName())
        ;
        return $this;
    }
}