<?php

namespace Modules\Projects\Services\LatinConverter\Actions;

class PalatalizationAction implements Action
{
    use DefaultAction;

    /**
     * @var array
     */
    protected $pairs = [
        'ц' => 'ć',
        'Ц' => 'Ć',
        'н' => 'ń',
        'Н' => 'Ń',
        'з' => 'ź',
        'З' => 'Ź',
        'л' => 'ĺ',
        'Л' => 'Ĺ',
        'с' => 'ś',
        'С' => 'Ś'
    ];

    /**
     * @param string $text
     * @return string
     */
    public function act($text)
    {
        return str_replace($this->search . 'ь', $this->getReplace(), $text);
    }
}