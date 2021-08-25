<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth;

/**
 * @ODM\EmbeddedDocument
 */
class ReleaseMember
{
    const CONFIRMED_STATE = 'co';
    const PENDING_STATE = 'pe';

    /**
     * @var string
     * @ODM\Field(type="string", name="id")
     */
    private $userId;

    /**
     * @var string
     * @ODM\Field(type="string", name="st")
     */
    private $state;

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return $this
     */
    public function setConfirmedState()
    {
        return $this->setState(self::CONFIRMED_STATE);
    }

    /**
     * @return $this
     */
    public function setPendingState()
    {
        return $this->setState(self::PENDING_STATE);
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return self::CONFIRMED_STATE == $this->getState();
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

    /**
     * @return bool
     */
    public function isYou()
    {
        return $this->getUserId() == Auth::id();
    }
}