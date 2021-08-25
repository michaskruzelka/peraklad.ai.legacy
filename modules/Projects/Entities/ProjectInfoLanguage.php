<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class ProjectInfoLanguage
{
    /**
     * @var string
     * @ODM\Field(type="string", name="iso")
     */
    private $iso6393b;

    /**
     * @ODM\Field(type="bin", name="na")
     */
    private $nativeName;

    /**
     * @ODM\Field(type="string", name="en")
     */
    private $englishName;

    /**
     * @ODM\Field(type="string", name="be")
     */
    private $belName;

    /**
     * @param string $iso6393b
     * @return $this
     */
    public function setIso6393b($iso6393b)
    {
        $this->iso6393b = $iso6393b;
        return $this;
    }

    /**
     * @return string
     */
    public function getIso6393b()
    {
        return $this->iso6393b;
    }

    /**
     * @param string $nativeName
     * @return $this
     */
    public function setNativeName($nativeName)
    {
        $this->nativeName = $nativeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getNativeName()
    {
        return $this->nativeName;
    }

    /**
     * @param string $englishName
     * @return $this
     */
    public function setEnglishName($englishName)
    {
        $this->englishName = $englishName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnglishName()
    {
        return $this->englishName;
    }

    /**
     * @param string $belName
     * @return $this
     */
    public function setBelName($belName)
    {
        $this->belName = $belName;
        return $this;
    }

    /**
     * @return string
     */
    public function getBelName()
    {
        return $this->belName;
    }
}