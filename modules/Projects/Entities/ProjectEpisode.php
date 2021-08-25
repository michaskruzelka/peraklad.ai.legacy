<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class ProjectEpisode extends Project
{
    /**
     * @var int
     * @ODM\Field(type="int")
     */
    protected $episode;

    /**
     * @var int
     * @ODM\Field(type="int")
     */
    protected $season;

    /**
     * @var ProjectEpisodeInfo
     * @ODM\EmbedOne(targetDocument="ProjectEpisodeInfo")
     */
    protected $info;

    public function __construct()
    {
        $this->info = app()->build('Modules\Projects\Entities\ProjectEpisodeInfo');
        $this->owner = app()->build('Modules\Projects\Entities\ProjectOwner');
    }

    /**
     * @return ProjectEpisodeInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param ProjectEpisodeInfo $info
     * @return $this
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return int
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * @param int $episode
     * @return $this
     */
    public function setEpisode($episode)
    {
        $this->episode = $episode;
        return $this;
    }

    /**
     * @return int
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param int $season
     * @return $this
     */
    public function setSeason($season)
    {
        $this->season = $season;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function import(array $data)
    {
        $this->setSeason($data['season'])
            ->setEpisode($data['episode'])
            ->getInfo()
            ->setOriginalTitle($data['originalTitle'])
            ->setTranslatedTitle($data['translatedTitle'])
            ->setYear($data['year'])
            ->setImdbId($data['imdbId'])
        ;
        return $this;
    }

    /**
     * @ODM\PrePersist
     */
    public function generateSlug()
    {
        return;
    }

    /**
     * @ODM\PostLoad
     */
    public function correctRating()
    {
        return;
    }

    /**
     * @ODM\PostRemove
     */
    public function deletePoster()
    {
        //$this->getInfo()->getPoster()->deleteFile();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}