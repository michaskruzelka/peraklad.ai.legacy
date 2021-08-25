<?php

namespace Modules\Users\Composers;

use Illuminate\View\View;
use App\Traits\ComposerAdvanced as Advanced;

/**
 * Class ModulesComposer
 * @package Modules\Search\Composers
 */
class HeaderNavbarComposer
{
    /*
     * For experimental purposes only
     */
    use Advanced;

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return $this;
     */
    public function compose(View $view)
    {
        $viewsMap = config('users.views.header_navbar');
        $this->plug($view, $viewsMap);
        return $this;
    }

    /**
     * @param array $config
     * @return View
     */
    protected function composeWorkshopTopMenu(array $config)
    {
        return view($config['view']);
    }
}