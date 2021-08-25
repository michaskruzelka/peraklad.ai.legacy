<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="languages", repositoryClass="Modules\Projects\Repositories\Languages")
 */
class Language
{
    /**
     * @var string iso639-3b language code
     * @ODM\Id(strategy="NONE", type="string")
     */
    private $id;

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
     * @ODM\Field(type="boolean", name="sub")
     */
    private $isSubable;

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
    public function getId()
    {
        return $this->id;
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

    /**
     * @param boolean $isSubable
     * @return $this
     */
    public function setIsSubable($isSubable)
    {
        $this->isSubable = $isSubable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsSubable()
    {
        return $this->isSubable;
    }
}