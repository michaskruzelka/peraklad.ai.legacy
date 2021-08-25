<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class HRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Г', 'г'];
}
