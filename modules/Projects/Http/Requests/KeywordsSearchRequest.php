<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;

class KeywordsSearchRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ( ! $this->ajax()) {
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
        return [
            'key' => 'min:3'
        ];
    }
}
