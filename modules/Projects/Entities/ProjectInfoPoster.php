<?php

namespace Modules\Projects\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Projects\Jobs\DeletePoster;

/**
 * @ODM\EmbeddedDocument
 */
class ProjectInfoPoster
{
    use DispatchesJobs;

    /**
     * @var string
     * @ODM\Field(type="string", name="mm")
     */
    private $mime;

    /**
     * @var string
     * @ODM\Field(type="string", name="fn")
     */
    private $fileName;

    /**
     * @var mixed
     */
    private $rowData;

    /**
     * @return mixed
     */
    public function getRowData()
    {
        return $this->rowData;
    }

    /**
     * @param mixed $rowData
     * @return $this
     */
    public function setRowData($rowData)
    {
        $this->rowData = $rowData;
        return $this;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param string $mime
     * @return $this
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @throws \Exception
     * @return $this
     */
    public function import()
    {
        if ( ! $data = $this->getRowData()) {
            throw new \Exception('The row data is empty');
        }
        if ($data == $this->getSrc()) {
            return $this;
        }

        if ($this->getFileName()) {
            $this->deleteFile();
        }
        $img = \Image::make($data);
        $this->setMime($img->mime());
        $fileName = $this->generateFileName();
        $img->resize($this->getWidth(), $this->getHeight())
            ->save($this->getPath() . $fileName)
        ;
        $this->setFileName($fileName);
        $img->destroy();
        return $this;
    }

    /**
     * @return $this
     */
    public function deleteFile()
    {
        $job = new DeletePoster($this);
        $this->dispatch($job);
        return $this;
    }

    /**
     * @return string
     */
    public function getSrc()
    {
        if ($this->getFileName()) {
            return \Module::asset("projects:img/posters/{$this->getFileName()}");
        }
        return $this->getDefaultSrc();
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return config('projects.poster.dimensions.width');
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return config('projects.poster.dimensions.height');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return \Module::assetPath('projects/img/posters/');
    }

    /**
     * @return bool
     */
    public function isFileDefault()
    {
        return $this->getSrc() == $this->getDefaultSrc();
    }

    /**
     * @return string
     */
    public function getDefaultSrc()
    {
        return \Module::asset('projects:img/movie-placeholder.jpg');
    }

    /**
     * @return mixed
     */
    public function getExt()
    {
        return last(explode('/', $this->getMime()));
    }

    /**
     * @return string
     */
    public function generateFileName()
    {
        return md5( (string) $this->getRowData() . microtime()) . '.' . $this->getExt();
    }

    /**
     * @param $data
     * @return bool
     */
    public static function isDataUrl($data)
    {
        if (!is_string($data)) {
            return false;
        }
        $pattern = "/^data:(?:image\/[a-zA-Z\-\.]+)(?:charset=\".+\")?;base64,(?P<data>.+)$/";
        preg_match($pattern, $data, $matches);
        if (is_array($matches) && array_key_exists('data', $matches)) {
            return true;
        }
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    public static function isUrl($data)
    {
        return (bool) filter_var($data, FILTER_VALIDATE_URL);
    }
}