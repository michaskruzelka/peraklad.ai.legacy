<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class TRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Т', 'т'];
}
