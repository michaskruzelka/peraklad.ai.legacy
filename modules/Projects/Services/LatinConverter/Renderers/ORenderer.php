<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class ORenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['VowelPalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['О', 'о'];
}
