<?php

namespace Modules\Users\Http\Requests;

use App\Http\Requests\Request;
use Modules\Users\Entities\User;

class RemoveProfileRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->getUser()->isYou();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [

        ];
        return $rules;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->route('user');
    }
}
