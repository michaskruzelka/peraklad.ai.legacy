<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class ReleaseOwner
{
    /**
     * @var string
     * @ODM\Field(type="string", name="id")
     */
    private $userId;

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
     * Deprecated: instead use $this->getUserId()
     * @return string
     */
    public function getUsername()
    {
        return $this->getUserId();
    }

    /**
     * Deprecated: instead use $this->setUserId()
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        return $this->setUserId($username);
    }
}