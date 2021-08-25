<?php

namespace Modules\Users\Extended\MewsCaptcha;

use Mews\Captcha\Captcha as MewsCaptcha;

class Captcha extends MewsCaptcha
{
    /**
     * Captcha check
     *
     * @param $value
     * @return bool
     */
    public function check($value)
    {
        if ( ! $this->session->has('captcha'))
        {
            return false;
        }

        $key = $this->session->get('captcha.key');

        if ( ! $this->session->get('captcha.sensitive'))
        {
            $value = $this->str->lower($value);
        }

        if ( ! \Request::ajax()) {
            $this->session->remove('captcha');
        }

        return $this->hasher->check($value, $key);
    }
}