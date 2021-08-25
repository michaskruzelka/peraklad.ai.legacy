<?php

namespace Modules\Projects\Services\LatinConverter\Actions;

class SolidAction implements Action
{
    use DefaultAction;

    /**
     * @var array
     */
    protected $pairs = [
        'л' => 'ł',
        'Л' => 'Ł'
    ];

    /**
     * @param string $text
     * @return string
     */
    public function act($text)
    {
        return preg_replace("/({$this->search}+)([аоыэу\\W]+)/u", "{$this->getReplace()}$2", $text);
    }
}