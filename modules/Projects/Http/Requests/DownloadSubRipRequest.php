<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Captioning\File as CaptionFile;

class DownloadSubRipRequest extends Request
{
    /**
     * @var CaptionFile
     */
    protected $captionFile;

    /**
     * SaveReleaseFileRequest constructor.
     * @param CaptionFile $captionFile
     */
    public function __construct(CaptionFile $captionFile)
    {
        $this->captionFile = $captionFile;
    }

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validate()
    {
        parent::validate();
        try {
            $this->initCaptions();
        } catch (\Exception $e) {
            $validator = $this->getValidatorInstance();
            $validator->getMessageBag()->add('text', $e->getMessage());
            $this->failedValidation($validator);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->getRelease()->isDownloadable();
    }

    /**
     * @return array
     */
    public function rules()
    {
        $availableCharsets = implode(',', (array) config('projects.downloadableCharsets')[$this->getAbc()]);
        $availableNewLineFormats = implode(',', array_keys((array) config('projects.newLineFormats')));

        return [
            'charset' => "required|in:{$availableCharsets}",
            'nl' => "required|in:{$availableNewLineFormats}"
        ];
    }

    /**
     * @return \Illuminate\Routing\Route|object|string
     */
    public function getAbc()
    {
        return $this->route('format');
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->input('charset');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getNewLineFormat()
    {
        switch ($this->input('nl')) {
            case 'windows':
                $format = CaptionFile::WINDOWS_LINE_ENDING;
                break;
            case 'mac':
                $format = CaptionFile::MAC_LINE_ENDING;
                break;
            case 'unix':
                $format = CaptionFile::UNIX_LINE_ENDING;
                break;
            default:
                throw new \Exception('Unsupported new line format');
        }
        return $format;
    }

    /**
     * @return \Illuminate\Routing\Route|object|string
     */
    public function getRelease()
    {
        return $this->route('release');
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->getRelease()->getFile($this->getAbc());
    }

    /**
     * @return CaptionFile
     */
    public function getCaptionFile()
    {
        return $this->captionFile;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function initCaptions()
    {
        $file = $this->getFile();
        $this->captionFile->loadFromString($file->getData());
        if ($this->captionFile->getCuesCount() == 0) {
            throw new \Exception('Некарэктны файл субтытраў');
        }
        return $this;
    }
}
