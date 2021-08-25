<?php

namespace Modules\Users\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 * @ODM\HasLifecycleCallbacks
 */
class UserAddress
{
    /**
     * @var string
     * @ODM\Field(type="string", name="co")
     */
    private $country;

    /**
     * @var string
     * @ODM\Field(type="string", name="ci")
     */
    private $city;

    /**
     * @var
     * @ODM\EmbedOne(targetDocument="UserAddressLocation")
     */
    private $loc;

    /**
     * @var bool
     */
    protected $isChanged;

    public function __construct()
    {
        $this->loc = app()->build(UserAddressLocation::class);
    }

    /**
     * @return mixed
     */
    public function getIsChanged()
    {
        return $this->isChanged;
    }

    /**
     * @param mixed $isChanged
     * @return $this
     */
    public function setIsChanged($isChanged)
    {
        $this->isChanged = $isChanged;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        if ($country != $this->country) {
            $this->setIsChanged(true);
        }
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        if ($city != $this->city) {
            $this->setIsChanged(true);
        }
        $this->city = $city;
        return $this;
    }

    /**
     * @return UserAddressLocation
     */
    public function getLocation()
    {
        return $this->loc;
    }

    /**
     * @param UserAddressLocation $loc
     * @return $this
     */
    public function setLocation($loc)
    {
        $this->loc = $loc;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddressString()
    {
        if ($this->getCity() || $this->getCountry()) {
            return implode(', ', [$this->getCity(), $this->getCountry()]);
        }
        return '';
    }
}