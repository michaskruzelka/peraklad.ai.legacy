<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Captioning\File as CaptionFile;

class SaveReleaseFileRequest extends Request
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
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->getRelease()->belongsToYou();
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'text' => 'required'
        ];
        return $rules;
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
    public function getData()
    {
        return $this->captionFile->getFileContent();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function initCaptions()
    {
        $content = $this->input('text');
        $this->captionFile->loadFromString($content);
        $this->captionFile->build();
        if ($this->captionFile->getCuesCount() == 0) {
            throw new \Exception('Некарэктны файл субтытраў');
        }
        return $this;
    }
}
