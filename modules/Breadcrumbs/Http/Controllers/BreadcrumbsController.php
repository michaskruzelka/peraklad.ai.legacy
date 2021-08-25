<?php namespace Modules\Breadcrumbs\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class BreadcrumbsController extends Controller {
	
	public function index()
	{
		return view('Breadcrumbs::index');
	}
	
}