<?php

Route::group(['prefix' => 'breadcrumbs', 'namespace' => 'Modules\Breadcrumbs\Http\Controllers'], function()
{
	Route::get('/', 'BreadcrumbsController@index');
});