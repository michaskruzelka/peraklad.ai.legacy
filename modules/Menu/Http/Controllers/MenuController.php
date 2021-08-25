<?php namespace Modules\Menu\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class MenuController extends Controller {
	
	public function index()
	{
		return view('Menu::index');
	}
	
}