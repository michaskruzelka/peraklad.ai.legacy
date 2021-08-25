<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class KRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['К', 'к'];
}
