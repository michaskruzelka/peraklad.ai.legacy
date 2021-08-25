<?php

namespace Modules\Search\Composers;

use Illuminate\View\View;
use App\Traits\ComposerAdvanced as Advanced;

/**
 * Class ModulesComposer
 * Experimental composer
 * @package Modules\Search\Composers
 */
class HeaderNavbarComposer
{
    use Advanced;

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return $this;
     */
    public function compose(View $view)
    {
        $viewsMap = config('search.views.header_navbar');
        $this->plug($view, $viewsMap);
        return $this;
    }

    /**
     * @param array $config
     * @return View
     */
    protected function composeWorkshopToogle(array $config)
    {
        return view($config['view']);
    }

    /**
     * @param array $config
     * @return View
     */
    protected function composeWorkshopForm(array $config)
    {
        return view($config['view']);
    }
}