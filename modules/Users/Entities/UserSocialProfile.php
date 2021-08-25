<?php

namespace Modules\Users\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class UserSocialProfile
{
    const VKONTAKTE_PROFILE = 'vk';
    const FACEBOOK_PROFILE = 'fb';
    const TWITTER_PROFILE = 'tw';
    const LINKEDIN_PROFILE = 'ln';
    const SKYPE_PROFILE = 's';

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    private $type;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    private $link;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     * @throws \Exception
     */
    public function setType($type)
    {
        if ( ! in_array($type, $this->getSupportedTypes())) {
            throw new \Exception("Unsupported profile type: {$type}");
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVkontakte()
    {
        return self::VKONTAKTE_PROFILE == $this->getType();
    }

    /**
     * @return bool
     */
    public function isFacebook()
    {
        return self::FACEBOOK_PROFILE == $this->getType();
    }

    /**
     * @return bool
     */
    public function isTwitter()
    {
        return self::TWITTER_PROFILE == $this->getType();
    }

    /**
     * @return bool
     */
    public function isLinkedin()
    {
        return self::LINKEDIN_PROFILE == $this->getType();
    }

    /**
     * @return bool
     */
    public function isSkype()
    {
        return self::SKYPE_PROFILE == $this->getType();
    }

    /**
     * @return array
     */
    public function getSupportedTypes()
    {
        return [
            self::VKONTAKTE_PROFILE,
            self::TWITTER_PROFILE,
            self::SKYPE_PROFILE,
            self::LINKEDIN_PROFILE,
            self::FACEBOOK_PROFILE
        ];
    }
}