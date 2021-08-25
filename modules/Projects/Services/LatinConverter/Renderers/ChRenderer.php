<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class ChRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Х', 'х'];
}
