<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class MRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['М', 'м'];
}
