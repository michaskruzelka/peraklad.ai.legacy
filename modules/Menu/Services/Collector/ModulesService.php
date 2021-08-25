<?php

namespace Modules\Menu\Services\Collector;

use Modules\Menu\Contracts\CollectorContract;
use Pingpong\Modules\Module;

class ModulesService implements CollectorContract
{
    /**
     * @param string $area
     *
     * @return array
     */
    public function collect($area)
    {
        $items = [];
        foreach(\Module::enabled() as $module) {
            $moduleItems = $this->getModuleItems($module, $area);
            if (is_null($moduleItems)) {
                continue;
            }
            $items = array_merge($items, $moduleItems);
        }
        $items = array_sort($items, function($item) {
            return  (int) $item->getProperty('sort');
        });
        return $items;
    }

    /**
     * @param Module $module
     * @param string $area
     * @return mixed (array|null)
     */
    protected function getModuleItems(Module $module, $area)
    {
        $items = config("{$module->getName()}.menu.{$area}");
        if (is_null($items)) {
            return null;
        }
        $items = array_map(array($this, 'convertToEntity'), $items);
        return $items;
    }

    /**
     * @param array $config
     * @return \Modules\Menu\Models\Item $item
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function convertToEntity(array $config)
    {
        $item = app()->build('Modules\Menu\Models\Item', array($config));
        return $item;
    }
}