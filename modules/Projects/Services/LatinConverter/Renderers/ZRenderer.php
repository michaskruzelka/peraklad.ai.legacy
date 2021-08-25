<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class ZRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['PalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['З', 'з'];
}
