<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class SubtitleHistory
 * @package Modules\Projects\Entities
 * @ODM\MappedSuperclass
 * @ODM\HasLifecycleCallbacks
 */
abstract class SubtitleHistory
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
     * @var SubtitleHistoryInfo
     * @ODM\EmbedOne(targetDocument="SubtitleHistoryInfo")
     */
    private $info;

    public function __construct()
    {
        $this->info = app()->build(SubtitleHistoryInfo::class);
    }

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
     * @return SubtitleHistoryInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param SubtitleHistoryInfo $info
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

    /**
     * @return string
     */
    abstract public function represent();
}