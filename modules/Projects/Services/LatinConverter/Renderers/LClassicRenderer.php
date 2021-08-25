<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class LClassicRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SolidAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Л', 'л'];
}
