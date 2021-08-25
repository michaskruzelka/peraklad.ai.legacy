<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class DRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Д', 'д'];
}
