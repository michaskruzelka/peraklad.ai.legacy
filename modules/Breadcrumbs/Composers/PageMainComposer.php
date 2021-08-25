<?php

namespace Modules\Breadcrumbs\Composers;

use Illuminate\View\View;
use App\Traits\ComposerAdvanced as Advanced;

/**
 * Class ModulesComposer
 * @package Modules\Search\Composers
 */
class PageMainComposer
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
        $viewsMap = config('breadcrumbs.views.page_main');
        $this->plug($view, $viewsMap);
        return $this;
    }

    /**
     * @param array $config
     * @return View
     */
    protected function composeWorkshopMainActions(array $config)
    {
        return view($config['view']);
    }
}