<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Modules\Users\Entities\UserAvatar;
use App\Traits\SmartDate;

/**
 * @ODM\EmbeddedDocument
 */
class SubtitleVersion
{
    use SmartDate;

    const PENDING_STATUS = 'pe';
    const APPROVED_STATUS = 'ap';
    const REMOVED_STATUS = 're';

    /**
     * @var string
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\Field(type="string", name="ui")
     */
    private $userId;

    /**
     * @ODM\Field(type="string", name="t")
     */
    private $text;

    /**
     * @ODM\Field(type="date", name="ca")
     */
    private $createdAt;

    /**
     * @ODM\Field(type="string", name="st")
     */
    private $status;

    /**
     * @var array
     * @ODM\Field(type="collection")
     */
    private $likes = [];

    /**
     * @var string
     */
    private $avatar;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @return int
     */
    public function getLikesCount()
    {
        return count($this->likes);
    }

    /**
     * @return bool
     */
    public function doYouLike()
    {
        return in_array(\Auth::id(), $this->likes);
    }

    /**
     * @param $likes
     * @return $this
     */
    public function setLikes($likes)
    {
        $this->likes = (array) $likes;
        return $this;
    }

    /**
     * @param $userId
     * @return $this
     */
    public function addLike($userId)
    {
        array_push($this->likes, $userId);
        $this->likes = array_unique($this->likes);
        return $this;
    }

    /**
     * @param $userId
     * @return $this
     */
    public function removeLike($userId)
    {
        $this->likes = array_diff($this->likes, [$userId]);
        return $this;
    }

    /**
     * @return $this
     */
    public function like()
    {
        return $this->addLike(\Auth::id());
    }

    /**
     * @return $this
     */
    public function unlike()
    {
        return $this->removeLike(\Auth::id());
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     * @return $this
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return string
     */
    public function getAvatarSrc()
    {
        return UserAvatar::getSrcByFilename($this->getAvatar());
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->getUserId();
    }

    /**
     * @param mixed $username
     * @return $this
     */
    public function setUsername($username)
    {
        return $this->setUserId($username);
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
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
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
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
        return $this;
    }

    /**
     * @return string
     */
    public function getHumanCreatedAt()
    {
        return $this->smartRepresent($this->getCreatedAt());
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return SubtitleComment
     */
    public function setApprovedStatus()
    {
        return $this->setStatus(self::APPROVED_STATUS);
    }

    /**
     * @return SubtitleComment
     */
    public function setRemovedStatus()
    {
        return $this->setStatus(self::REMOVED_STATUS);
    }

    /**
     * @return SubtitleVersion
     */
    public function setPendingStatus()
    {
        return $this->setStatus(self::PENDING_STATUS);
    }

    /**
     * @return bool
     */
    public function isApprovedStatus()
    {
        return self::APPROVED_STATUS == $this->getStatus();
    }

    /**
     * @return bool
     */
    public function isRemovedStatus()
    {
        return self::REMOVED_STATUS == $this->getStatus();
    }

    /**
     * @return bool
     */
    public function isOwner()
    {
        return $this->getUserId() == \Auth::id();
    }
}