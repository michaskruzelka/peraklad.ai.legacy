<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Modules\Projects\Entities\Subtitle;

class SaveSubtitleTranslationRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( ! $this->getSubtitle()->isEditable()) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'content' => 'max:500'
        ];
        return $rules;
    }

    /**
     * @return Subtitle
     */
    public function getSubtitle()
    {
        return $this->route('subtitle');
    }
}
