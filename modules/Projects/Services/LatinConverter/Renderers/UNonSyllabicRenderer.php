<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class UNonSyllabicRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ў', 'ў'];
}
