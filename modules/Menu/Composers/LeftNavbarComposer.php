<?php

namespace Modules\Menu\Composers;

use Illuminate\View\View;
use App\Traits\ComposerAdvanced as Advanced;
use Modules\Menu\Contracts\CollectorContract;
use Modules\Menu\Models\Item;

/**
 * Class ModulesComposer
 * @package Modules\Search\Composers
 */
class LeftNavbarComposer
{
    /*
     * For experimental purposes only
     */
    use Advanced;

    /**
     * @var CollectorContract
     */
    protected $collector;

    /**
     * LeftNavbarComposer constructor.
     * @param CollectorContract $collector
     */
    public function __construct(CollectorContract $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return $this;
     */
    public function compose(View $view)
    {
        $viewsMap = config('menu.views.left_navbar');
        $this->plug($view, $viewsMap);
        return $this;
    }

    /**
     * @param array $config
     * @return View
     */
    protected function composeWorkshopLeftTop(array $config)
    {
        $items = $this->collector->collect('workshop-left-top-desktop');
        $categories = [];
        foreach ($items as $item) {
            $category = $item->getCategory();
            if (is_null($category)) {
                continue;
            }
            array_push($categories, $category);
        }
        $categories = array_unique($categories);
        return view($config['view'], compact('items', 'categories'));
    }
}