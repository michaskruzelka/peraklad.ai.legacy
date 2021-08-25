<?php

Route::group(['prefix' => 'search', 'namespace' => 'Modules\Search\Http\Controllers'], function()
{
	Route::get('/', 'SearchController@index');
});

Route::get('/', ['as' => 'home', 'middleware' => 'auth', 'uses' => 'Modules\Search\Http\Controllers\SearchController@index']);