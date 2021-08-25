<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Modules\Projects\Services\LatinConverter;

/**
 * @ODM\EmbeddedDocument
 * @ODM\HasLifecycleCallbacks
 */
class ReleaseFile
{
    const CYRILLIC_ABC = 'cy';
    const LATIN_ABC = 'la';

    /**
     * @ODM\Field(type="string")
     */
    private $name;

    /**
     * @ODM\Field(type="date", name="genAt")
     */
    private $generatedAt;

    /**
     * @ODM\Field(type="string", name="abc")
     */
    private $alphabet;

    /**
     * @ODM\Field(type="string", name="cha")
     */
    private $charset;

    /**
     * @ODM\Field(type="bin")
     */
    private $data;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeneratedAt()
    {
        return $this->generatedAt;
    }

    /**
     * @param mixed $generatedAt
     * @return $this
     */
    public function setGeneratedAt($generatedAt)
    {
        $this->generatedAt = $generatedAt;
        return $this;
    }

    /**
     * @ODM\PrePersist
     */
    public function generateGeneratedAt()
    {
        $this->setGeneratedAt(new \DateTime());
    }

    /**
     * @return mixed
     */
    public function getAlphabet()
    {
        return $this->alphabet;
    }

    /**
     * @param mixed $alphabet
     * @return $this
     */
    public function setAlphabet($alphabet)
    {
        $this->alphabet = $alphabet;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCyrillic()
    {
        return $this->getAlphabet() == self::CYRILLIC_ABC;
    }

    /**
     * @return bool
     */
    public function isLatin()
    {
        return $this->getAlphabet() == self::LATIN_ABC;
    }

    /**
     * @return mixed
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param mixed $charset
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param string $data
     * @param string $orthography
     * @return string
     */
    public function handleData($data, $orthography)
    {
        if ('la' == $this->getAlphabet()) {
            $converter = app()->make(LatinConverter::class, [$orthography]);
            $data = $converter->convert($data);
        }
        //$data = $this->encode($data);
        return $data;
    }

    /**
     * @param string $data
     * @return string
     */
    public function encode($data)
    {
        if ( ! $this->getCharset()) {
            return $data;
        }
        return iconv('UTF-8', $this->getCharset(), $data);
    }

    /**
     * @param bool|false $data
     * @return bool|mixed|string
     */
    public function decode($data = false)
    {
        if ( ! $data) {
            $data = $this->getData();
        }
        if ( ! $this->getCharset()) {
            return $data;
        }
        return iconv($this->getCharset(), 'UTF-8', $data);
    }
}