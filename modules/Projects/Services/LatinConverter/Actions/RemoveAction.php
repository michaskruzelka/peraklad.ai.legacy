<?php

namespace Modules\Projects\Services\LatinConverter\Actions;

class RemoveAction implements Action
{
    use DefaultAction;

    /**
     * @param string $text
     * @return string
     */
    public function act($text)
    {
        return str_replace($this->search, '', $text);
    }
}