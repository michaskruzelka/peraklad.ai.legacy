<?php

namespace Modules\Projects\Services\LatinConverter\Actions;

interface Action
{
    public function act($text);

    public function setSearch($search);
}