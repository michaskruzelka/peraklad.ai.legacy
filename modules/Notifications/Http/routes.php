<?php

Route::group(['prefix' => 'notifications', 'namespace' => 'Modules\Notifications\Http\Controllers'], function()
{
	Route::get('/', 'NotificationsController@index');
});