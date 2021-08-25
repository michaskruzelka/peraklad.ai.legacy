<?php

namespace Modules\Users\Entities;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Modules\Users\Contracts\GenderDetector;
use Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Users\Jobs\DeleteAvatar;

/**
 * @ODM\EmbeddedDocument
 */
class UserAvatar
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
     * @var GenderDetector
     */
    private $genderDetector;

    public function __construct()
    {
        $this->genderDetector = app()->make(GenderDetector::class);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function randomPick($name)
    {
        $gender = $this->genderDetector->detect($name);
        $path = 'public/modules/users/avatars/' . $gender;
        $avatars = collect(Storage::files($path));
        if ($avatars->count() > 0) {
            $avatarFullPath = $avatars->random();
            $avatar = last(explode('/', $avatarFullPath));
            $this->setFileName($gender . '/' . $avatar);
            $img = \Image::make($this->getSrc());
            $this->setMime($img->mime());
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return \Module::assetPath('users/avatars/');
    }

    /**
     * @return string|false
     */
    public function getSrc()
    {
        if ($this->getFileName()) {
            return self::getSrcByFilename($this->getFileName());
        }
        return false;
    }

    /**
     * @param $filename
     * @return bool|string
     */
    public static function getSrcByFilename($filename)
    {
        if ($filename) {
            return \Module::asset("users:avatars/{$filename}");
        }
        return false;
    }

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
        $job = new DeleteAvatar($this);
        $this->dispatch($job);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return config('users.avatar.dimensions.width');
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return config('users.avatar.dimensions.height');
    }

    /**
     * @return bool
     */
    public function isFileDefault()
    {
        return 'custom' !== current(explode('/', $this->getFileName()));
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
        return 'custom/' . md5( (string) $this->getRowData() . microtime()) . '.' . $this->getExt();
    }
}