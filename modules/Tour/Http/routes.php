<?php

Route::group([
    'prefix' => 'workshop/tour',
    'as' => 'workshop-tour::',
    'namespace' => 'Modules\Tour\Http\Controllers'
], function()
{
	Route::get('/', ['as' => 'index', 'uses' => 'TourController@index']);
});