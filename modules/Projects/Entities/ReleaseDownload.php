<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ReleaseDownload
 * @package Modules\Projects\Entities
 * @ODM\Document(
 *     collection="downloads",
 *     repositoryClass="Modules\Projects\Repositories\Downloads"
 * )
 * @ODM\HasLifecycleCallbacks
 */
class ReleaseDownload
{
    const LATIN_ABC = 'la';
    const CYRILLIC_ABC = 'cy';

    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Release", simple=true)
     * @ODM\Index
     */
    private $release;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    private $abc;

    /**
     * @ODM\Field(type="date", name="ca")
     */
    private $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getAbc()
    {
        return $this->abc;
    }

    public function setAbc($abc)
    {
        $this->abc = $abc;
        return $this;
    }

    public function setCyrillicAbc()
    {
        return $this->setAbc(self::CYRILLIC_ABC);
    }

    public function setLatinAbc()
    {
        return $this->setAbc(self::LATIN_ABC);
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @ODM\PrePersist
     */
    public function generateCreatedAt()
    {
        $this->setCreatedAt(new \DateTime());
    }

    public function getRelease()
    {
        return $this->release;
    }

    public function setRelease($release)
    {
        $this->release = $release;
        return $this;
    }
}