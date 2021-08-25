<?php

namespace Modules\Projects\Services\LatinConverter\Actions;

class VowelPalatalizationAction implements Action
{
    use DefaultAction;

    protected $palatalizedPairs = [
        'а' => 'я',
        'А' => 'Я',
        'э' => 'е',
        'Э' => 'Е',
        'о' => 'ё',
        'О' => 'Ё',
        'у' => 'ю',
        'У' => 'Ю'
    ];

    /**
     * @var array
     */
    protected $pairs = [
        'я' => [
            'i' => 'ia',
            'j' => 'ja',
            'l' => 'a'
        ],
        'Я' => [
            'j' => 'Ja',
            'i' => '',
            'l' => 'A'
        ],
        'е' => [
            'i' => 'ie',
            'j' => 'je',
            'l' => 'e'
        ],
        'Е' => [
            'j' => 'Je',
            'i' => '',
            'l' => 'E'
        ],
        'ё' => [
            'i' => 'io',
            'j' => 'jo',
            'l' => 'o'
        ],
        'Ё' => [
            'j' => 'Jo',
            'i' => '',
            'l' => 'O'
        ],
        'ю' => [
            'i' => 'iu',
            'j' => 'ju',
            'l' => 'u'
        ],
        'Ю' => [
            'j' => 'Ju',
            'i' => '',
            'l' => 'U'
        ]
    ];

    /**
     * @param string $text
     * @return string
     */
    public function act($text)
    {
        $pattern = "/([бвгзклмнпсфхцБВГЗКЛМНПСФХЦ]{1})({$this->getPalatalizedVowel()})/u";
        $replacement = "$1{$this->getReplace()['i']}";
        $text = preg_replace($pattern, $replacement, $text);
        $pattern = "/(l{1})({$this->getPalatalizedVowel()})/i";
        $replacement = "$1{$this->getReplace()['l']}";
        $text = preg_replace($pattern, $replacement, $text);
        return str_replace($this->getPalatalizedVowel(), $this->getReplace()['j'], $text);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getReplace()
    {
        if ( ! isset($this->pairs[$this->getPalatalizedVowel()])) {
            throw new \Exception('The search letter is not correct.');
        }
        return $this->pairs[$this->getPalatalizedVowel()];
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getPalatalizedVowel()
    {
        if ( ! isset($this->palatalizedPairs[$this->search])) {
            throw new \Exception('The search letter is not correct.');
        }
        return $this->palatalizedPairs[$this->search];
    }
}