<?php

namespace Modules\Users\Contracts;

interface GenderDetector
{
    /**
     * @param $name
     * @return string|null
     */
    public function detect($name);
}