<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document
 */
class ProjectSeries extends Project
{
    /**
     * @ODM\EmbedMany(strategy="addToSet", targetDocument="ProjectEpisode")
     */
    private $episodes;

    public function __construct()
    {
        parent::__construct();
        $this->episodes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'series';
    }

    /**
     * @return ArrayCollection
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * @param ArrayCollection $episodes
     * @return $this
     */
    public function setEpisodes(ArrayCollection $episodes)
    {
        $this->episodes = $episodes;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function importEpisodes(array $data)
    {
        // handle existing episodes
        foreach ($this->getEpisodes() as $episode) {
            if ( ! $episode->belongsToYou()) {
                continue;
            }
            $episodeId = $episode->getId();
            $existingEpisodes = array_filter($data, function($item) use($episodeId) {
                return $item['id'] == $episodeId;
            });
            if (empty($existingEpisodes)) {
                $this->removeEpisode($episode);
            } else {
                $existingEpisode = array_pop($existingEpisodes);
                $episode->import($existingEpisode);
            }
        }
        // handle new episodes
        $newEpisodes = array_filter($data, function($item) {
            return  ! $item['id'];
        });
        foreach ($newEpisodes as $item) {
            $episode = app()->build(ProjectEpisode::class);
            $episode->import($item);
            $this->addEpisode($episode);
        }
        return $this;
    }

    /**
     * @param ProjectEpisode $episode
     * @return $this
     */
    public function addEpisode(ProjectEpisode $episode)
    {
        $this->episodes->add($episode);
        return $this;
    }

    /**
     * @param ProjectEpisode $episode
     * @return $this
     * @throws \Exception
     */
    public function removeEpisode(ProjectEpisode $episode)
    {
        if ($episode->getReleases()->count() > 0) {
            throw new \Exception(
                "Немагчыма выдаліць эпізод {$episode->getInfo()->getTranslatedTitle()}, бо ён мае рэліз"
            );
        }
        $this->episodes->removeElement($episode);
        return $this;
    }
}