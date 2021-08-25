<?php

namespace Modules\Projects\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Modules\Projects\Services\SlugGenerator;
use Auth;
use Lang;

/**
 * @ODM\Document(collection="projects", repositoryClass="Modules\Projects\Repositories\Projects")
 * @ODM\InheritanceType("SINGLE_COLLECTION")
 * @ODM\DiscriminatorField("type")
 * @ODM\DiscriminatorMap({"movie"="ProjectMovie", "series"="ProjectSeries", "episode"="ProjectEpisode"})
 * @ODM\HasLifecycleCallbacks
 */
class Project
{
    /**
     * @ODM\Id
     */
    protected $id;

    /**
     * @ODM\EmbedOne(targetDocument="ProjectInfo")
     */
    protected $info;

    /**
     * @ODM\EmbedOne(targetDocument="ProjectOwner")
     */
    protected $owner;

    /**
     * @ODM\ReferenceMany(targetDocument="Release", simple=true)
     */
    protected $releases;

    /**
     * @var SlugGenerator
     */
    protected $slugGenerator;

    /**
     * @var \DateTime
     * @ODM\Field(type="date", name="ca")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ODM\Field(type="date", name="ua")
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $url;

    public function __construct()
    {
        $this->info = app()->build('Modules\Projects\Entities\ProjectInfo');
        $this->owner = app()->build('Modules\Projects\Entities\ProjectOwner');
        $this->slugGenerator = app()->build('Modules\Projects\Services\SlugGenerator');
        $this->releases = new ArrayCollection();
        $this->activeReleases = new ArrayCollection();
    }

    /**
     * @return null
     */
    public function getType()
    {
        return null;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return ProjectInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param ProjectInfo $info
     * @return $this
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return \MongoId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \MongoId $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ProjectOwner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param ProjectOwner $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return array
     */
    public function getReleases()
    {
        return $this->releases;
    }

    /**
     * @param array $releases
     * @return $this
     */
    public function setReleases(array $releases)
    {
        $this->releases = $releases;
        return $this;
    }

    /**
     * @param Release $release
     * @return $this
     */
    public function addRelease(Release $release)
    {
        $this->releases->add($release);
        return $this;
    }

    /**
     * @ODM\PreRemove
     */
    public function checkReleases()
    {
        if ($this->getReleases()->count() > 0) {
            throw new \Exception($this->getDeleteConstraintMessage());
        }
    }

    /**
     * @ODM\PostRemove
     */
    public function deletePoster()
    {
        $this->getInfo()->getPoster()->deleteFile();
    }

    /**
     * @ODM\PrePersist
     */
    public function generateCreatedAt()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @ODM\PreUpdate
     */
    public function generateUpdatedAt()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ODM\PrePersist
     */
    public function generateSlug()
    {
        $slug = $this->slugGenerator->generate($this);
        $this->getInfo()->setSlug($slug);
    }

    /**
     * @ODM\PrePersist
     */
    public function importOwner()
    {
        $userId = Auth::id();
        $this->getOwner()->setUserId($userId);
    }

    /**
     * @ODM\PostLoad
     */
    public function correctRating()
    {
        if ( ! $this->getInfo()->getImdbRating()) {
            $this->getInfo()->setImdbRating(null);
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ( ! $this->url && $this->getInfo()->getSlug()) {
            $this->url = self::url($this->getInfo()->getSlug());
        }
        return $this->url;
    }

    /**
     * @param string $slug
     * @return string
     */
    public static function url($slug)
    {
        return route('workshop::projects::edit', ['project' => $slug]);
    }

    /**
     * @return bool
     */
    public function belongsToYou()
    {
        return Auth::id() === $this->getOwner()->getUserId();
    }

    /**
     * Get the constraint error message.
     *
     * @return string
     */
    protected function getDeleteConstraintMessage()
    {
        return Lang::has('projects::project.delete')
            ? Lang::get('projects::project.delete', ['project' => $this->getInfo()->getTranslatedTitle()])
            : 'It is impossible to remove the project '
            . $this->getInfo()->getTranslatedTitle() . ' since it has releases'
        ;
    }
}