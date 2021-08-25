<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class CRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['PalatalizationAction', 'SimpleReplaceAction'];

    /**
     * @var array
     */
    protected $letters = ['Ц', 'ц'];
}
