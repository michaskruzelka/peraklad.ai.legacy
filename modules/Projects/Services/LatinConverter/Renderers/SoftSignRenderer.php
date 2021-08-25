<?php

namespace Modules\Projects\Services\LatinConverter\Renderers;

class SoftSignRenderer extends DefaultRenderer
{
    /**
     * @var string
     */
    protected $actionNames = ['RemoveAction'];

    /**
     * @var array
     */
    protected $letters = ['Ь', 'ь'];
}
