<?php

namespace Modules\Projects\Http\Requests;

use App\Http\Requests\Request;

class ImdbSearchRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'title' => 'max:100',
            'id' => 'max:20',
        ];
    }
}
