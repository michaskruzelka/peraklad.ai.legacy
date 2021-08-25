<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class IRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['І', 'і'];
}
