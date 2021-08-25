<?php

namespace Modules\Users\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Users\Jobs\GenerateCoordinates;
use Crypt;

/**
 * @ODM\Document(collection="users", repositoryClass="Modules\Users\Repositories\Users")
 * @ODM\HasLifecycleCallbacks
 */
class User implements UserContract
{
    use DispatchesJobs;

    const ACTIVE_STATUS = 'on';
    const INACTIVE_STATUS = 'off';

    /**
     * @var string
     * @ODM\Id(strategy="NONE", type="string")
     */
    private $id;

    /**
     * @var string
     * @ODM\Field(type="bin", name="pw")
     */
    private $password;

    /**
     * @var string
     * @ODM\Field(type="string", name="em")
     */
    private $email;

    /**
     * @var string
     * @ODM\Field(type="string", name="na")
     */
    private $name;

    /**
     * @var \DateTime
     * @ODM\Field(type="date", name="ca")
     */
    private $createdAt;

    /**
     * @var UserAvatar
     * @ODM\EmbedOne(targetDocument="UserAvatar")
     */
    private $avatar;

    /**
     * @var UserAddress
     * @ODM\EmbedOne(targetDocument="UserAddress")
     */
    private $address;

    /**
     * @var array
     * @ODM\EmbedMany(targetDocument="UserSocialProfile")
     */
    private $socialProfiles;

    /**
     * @var string
     * @ODM\Field(type="string", name="st")
     */
    private $status;

    /**
     * @var string
     * @ODM\Field(type="string", name="to")
     */
    private $token;

    public function __construct()
    {
        $this->avatar = app()->build(UserAvatar::class);
        $this->address = app()->build(UserAddress::class);
        $this->socialProfiles = new ArrayCollection();
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
     * @return User
     */
    public function setActiveStatus()
    {
        return $this->setStatus(self::ACTIVE_STATUS);
    }

    /**
     * @return User
     */
    public function setInactiveStatus()
    {
        return $this->setStatus(self::INACTIVE_STATUS);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return self::ACTIVE_STATUS == $this->getStatus();
    }

    /**
     * @return UserAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param UserAddress $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->{$this->getRememberTokenName()} = $token;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return UserAvatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param UserAvatar $avatar
     * @return $this
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSocialProfiles()
    {
        return $this->socialProfiles;
    }

    /**
     * @return UserSocialProfile|false
     */
    public function getVkontakteProfile()
    {
        foreach ($this->getSocialProfiles() as $profile) {
            if ($profile->isVkontakte()) {
                return $profile;
            }
        }
        return false;
    }

    /**
     * @return UserSocialProfile|false
     */
    public function getFacebookProfile()
    {
        foreach ($this->getSocialProfiles() as $profile) {
            if ($profile->isFacebook()) {
                return $profile;
            }
        }
        return false;
    }

    /**
     * @return UserSocialProfile|false
     */
    public function getTwitterProfile()
    {
        foreach ($this->getSocialProfiles() as $profile) {
            if ($profile->isTwitter()) {
                return $profile;
            }
        }
        return false;
    }

    /**
     * @return UserSocialProfile|false
     */
    public function getLinkedinProfile()
    {
        return current(array_filter($this->getSocialProfiles()->toArray(), function ($profile) {
            return $profile->isLinkedin();
        }));
    }

    /**
     * @return UserSocialProfile|false
     */
    public function getSkypeProfile()
    {
        return current(array_filter($this->getSocialProfiles()->toArray(), function ($profile) {
            return $profile->isSkype();
        }));
    }

    /**
     * @param $type
     * @return false|UserSocialProfile
     * @throws \Exception
     */
    public function getSocialProfile($type)
    {
        switch ($type) {
            case UserSocialProfile::FACEBOOK_PROFILE:
                return $this->getFacebookProfile();
            case UserSocialProfile::LINKEDIN_PROFILE:
                return $this->getLinkedinProfile();
            case UserSocialProfile::SKYPE_PROFILE:
                return $this->getSkypeProfile();
            case UserSocialProfile::TWITTER_PROFILE:
                return $this->getTwitterProfile();
            case UserSocialProfile::VKONTAKTE_PROFILE:
                return $this->getVkontakteProfile();
            default:
                throw new \Exception("Unsupported profile type: {$type}");
        }
    }

    /**
     * @param $type
     * @param string $link
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return $this
     */
    public function addSocialProfile($type, $link)
    {
        $profile = app()->build(UserSocialProfile::class);
        $profile->setType($type)->setLink($link);
        $this->socialProfiles->add($profile);
        return $this;
    }

    /**
     * @param string $link
     * @return User
     */
    public function addVkontakteSocialProfile($link)
    {
        return $this->addSocialProfile(UserSocialProfile::VKONTAKTE_PROFILE, $link);
    }

    /**
     * @param $link
     * @return User
     */
    public function addFacebookSocialProfile($link)
    {
        return $this->addSocialProfile(UserSocialProfile::FACEBOOK_PROFILE, $link);
    }

    /**
     * @param $link
     * @return User
     */
    public function addTwitterSocialProfile($link)
    {
        return $this->addSocialProfile(UserSocialProfile::TWITTER_PROFILE, $link);
    }

    /**
     * @param $link
     * @return User
     */
    public function addLinkedinSocialProfile($link)
    {
        return $this->addSocialProfile(UserSocialProfile::LINKEDIN_PROFILE, $link);
    }

    /**
     * @param $link
     * @return User
     */
    public function addSkypeSocialProfile($link)
    {
        return $this->addSocialProfile(UserSocialProfile::SKYPE_PROFILE, $link);
    }

    /**
     * @param mixed $social
     * @return $this
     */
    public function setSocialProfiles($social)
    {
        $this->socialProfiles = $social;
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function removeSocialProfile($type)
    {
        if ($profile = $this->getSocialProfile($type)) {
            $this->socialProfiles->removeElement($profile);
        }
        return $this;
    }

    /**
     * @return User
     */
    public function removeVkontakteSocialProfile()
    {
        return $this->removeSocialProfile(UserSocialProfile::VKONTAKTE_PROFILE);
    }

    /**
     * @return User
     */
    public function removeFacebookSocialProfile()
    {
        return $this->removeSocialProfile(UserSocialProfile::FACEBOOK_PROFILE);
    }

    /**
     * @return User
     */
    public function removeTwitterSocialProfile()
    {
        return $this->removeSocialProfile(UserSocialProfile::TWITTER_PROFILE);
    }

    /**
     * @return User
     */
    public function removeLinkedinSocialProfile()
    {
        return $this->removeSocialProfile(UserSocialProfile::LINKEDIN_PROFILE);
    }

    /**
     * @return User
     */
    public function removeSkypeSocialProfile()
    {
        return $this->removeSocialProfile(UserSocialProfile::SKYPE_PROFILE);
    }

    /**
     * @ODM\PrePersist
     */
    public function pickAvatar()
    {
        $this->getAvatar()->randomPick($this->getName());
    }

    /**
     * @ODM\PrePersist
     */
    public function generateCreatedAt()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * Get the password for the user.
     *
     * @return string|null
     */
    public function getAuthPassword()
    {
        try {
            return Crypt::decrypt($this->getPassword());
        } catch (DecryptException $e) {
            \Log::warning('Could not decrypt the password of user - ' . $this->getId());
            return null;
        }
    }

    /**
     * @param string $password
     * @return User
     */
    public function setAuthPassword($password)
    {
        $password = Crypt::encrypt($password);
        return $this->setPassword($password);
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->getToken();
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return $this
     */
    public function setRememberToken($value)
    {
        return $this->setToken($value);
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'token';
    }

    /**
     * @return bool
     */
    public function isYou()
    {
        return $this->getId() == \Auth::id();
    }

    /**
     * @ODM\PreUpdate
     */
    public function preGenerateLocation()
    {
        if ($this->getAddress()->getIsChanged()
            &&  ! $this->getAddress()->getAddressString()
        ) {
            $this->getAddress()->getLocation()
                ->setLongitude(null)
                ->setLatitude(null)
            ;
        }
    }

    /**
     * @ODM\PostUpdate
     */
    public function postGenerateLocation()
    {
        if ($this->getAddress()->getIsChanged()) {
            if ($address = $this->getAddress()->getAddressString()) {
                $job = (new GenerateCoordinates($this->getId(), $address))->delay(5);
                $this->dispatch($job);
            }
        }
    }

    /**
     * @ODM\PrePersist
     */
    public function activate()
    {
        $this->setActiveStatus();
    }
}