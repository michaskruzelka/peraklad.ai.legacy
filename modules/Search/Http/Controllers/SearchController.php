<?php

namespace Modules\Search\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class SearchController extends Controller
{
	public function index()
	{
	    return view('search::index');
	}
}