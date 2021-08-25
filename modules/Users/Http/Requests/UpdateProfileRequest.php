<?php

namespace Modules\Users\Http\Requests;

use App\Http\Requests\Request;
use Modules\Users\Entities\User;

class UpdateProfileRequest extends Request
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
            'name' => 'required|max:30',
            'email' => 'required|max:30|email',
            'password' => 'required|max:20|min:5',
            'avatar' => 'required|max:1000000',
            'country' => 'max:40',
            'city' => 'max:40',
            'vk' => 'max:200|url',
            'fb' => 'max:200|url',
            'tw' => 'max:200|url',
            'ln' => 'max:200|url',
            's' => 'max:100'
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

    /**
     * @return string
     */
    public function getPassword()
    {
        return trim($this->input('password'));
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return trim($this->input('email'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return trim($this->input('name'));
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->input('avatar');
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return trim($this->input('city'));
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return trim($this->input('country'));
    }
}
