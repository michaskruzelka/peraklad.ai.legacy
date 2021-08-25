<?php

namespace Modules\Menu\Contracts;

interface CollectorContract
{
    /**
     * @param string $area
     *
     * @return array
     */
    public function collect($area);
}