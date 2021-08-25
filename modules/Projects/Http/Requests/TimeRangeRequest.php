<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;
use Modules\Projects\Entities\Subtitle;

class TimeRangeRequest extends Request
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
            'bottomLine' => ['required', 'regex:/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d,)(\d{3})?$/'],
            'topLine' => ['required', 'regex:/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d,)(\d{3})?$/']
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

    /**
     * @return string
     */
    public function getBottomLine()
    {
        return $this->input('bottomLine');
    }

    /**
     * @return string
     */
    public function getTopLine()
    {
        return $this->input('topLine');
    }
}
