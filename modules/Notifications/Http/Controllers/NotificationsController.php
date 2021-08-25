<?php namespace Modules\Notifications\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class NotificationsController extends Controller {
	
	public function index()
	{
		return view('Notifications::index');
	}
	
}