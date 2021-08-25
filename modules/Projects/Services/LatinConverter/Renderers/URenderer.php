<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class URenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['VowelPalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['У', 'у'];
}
