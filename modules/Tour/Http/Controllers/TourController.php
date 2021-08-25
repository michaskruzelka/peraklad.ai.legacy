<?php namespace Modules\Tour\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class TourController extends Controller {
	
	public function index()
	{
		return view('Tour::index');
	}
	
}