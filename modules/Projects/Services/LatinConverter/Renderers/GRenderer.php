<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class GRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ґ', 'ґ'];
}
