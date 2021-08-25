<?php

namespace Modules\Projects\Services\LatinConverter\Actions;

class SimpleReplaceAction implements Action
{
    use DefaultAction;

    /**
     * @var array
     */
    protected $pairs = [
        'А' => 'A',
        'а' => 'а',
        'Б' => 'B',
        'б' => 'b',
        'В' => 'V',
        'в' => 'v',
        'г' => 'h',
        'Г' => 'H',
        'д' => 'd',
        'Д' => 'D',
        'ж' => 'ž',
        'Ж' => 'Ž',
        'з' => 'z',
        'З' => 'Z',
        'і' => 'i',
        'І' => 'I',
        'й' => 'j',
        'Й' => 'J',
        'к' => 'k',
        'К' => 'K',
        'л' => 'l',
        'Л' => 'L',
        'м' => 'm',
        'М' => 'M',
        'н' => 'n',
        'Н' => 'N',
        'о' => 'o',
        'О' => 'O',
        'п' => 'p',
        'П' => 'P',
        'р' => 'r',
        'Р' => 'R',
        'с' => 's',
        'С' => 'S',
        'т' => 't',
        'Т' => 'T',
        'у' => 'u',
        'У' => 'U',
        'ў' => 'ŭ',
        'Ў' => 'Ǔ',
        'ф' => 'f',
        'Ф' => 'F',
        'х' => 'ch',
        'Х' => 'Ch',
        'ц' => 'c',
        'Ц' => 'C',
        'ч' => 'č',
        'Ч' => 'Č',
        'ш' => 'š',
        'Ш' => 'Š',
        '’' => '’',
        'ы' => 'y',
        'Ы' => 'Y',
        'э' => 'e',
        'Э' => 'E',
        'ґ' => 'g',
        'Ґ' => 'G'
    ];

    /**
     * @param string $text
     * @return string
     */
    public function act($text)
    {
        return str_replace($this->search, $this->getReplace(), $text);
    }
}