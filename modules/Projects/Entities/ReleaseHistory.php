<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class ReleaseHistory
 * @package Modules\Projects\Entities
 * @ODM\MappedSuperclass
 * @ODM\HasLifecycleCallbacks
 */
abstract class ReleaseHistory
{
    /**
     * @var string
     * @ODM\Field(type="string", name="us")
     */
    private $userId;

    /**
     * @ODM\Field(type="date", name="ca")
     */
    private $createdAt;

    /**
     * @var mixed
     * @ODM\Field(type="raw")
     */
    private $info;

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @ODM\PrePersist
     */
    public function generateUserId()
    {
        if ( ! $this->getUserId()) {
            $this->setUserId(\Auth::id());
        }
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return $this
     */
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

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param $info
     * @return $this
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return string
     */
    abstract public function getType();
}