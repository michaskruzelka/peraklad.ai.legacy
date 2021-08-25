<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class RRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Р', 'р'];
}
