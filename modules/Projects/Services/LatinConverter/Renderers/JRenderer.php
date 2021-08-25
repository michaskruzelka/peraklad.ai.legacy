<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class JRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Й', 'й'];
}
