<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class ApostropheRenderer extends DefaultRenderer
{
    /**
     * @param string $text
     * @return string
     */
    public function getText($text)
    {
        return preg_replace("/(['’]{1})([a-zа-яўі]+)/i", "$2", $text);
    }
}
