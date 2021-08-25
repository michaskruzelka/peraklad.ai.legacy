<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class VRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['В', 'в'];
}
