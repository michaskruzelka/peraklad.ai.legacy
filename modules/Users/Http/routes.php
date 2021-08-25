<?php

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Modules\Users\Http\Controllers',
    'as' => 'users::auth::'
], function() {
    Route::get('/login', 'AuthController@login')->name('login');
    Route::post('/login', ['before' => 'csrf', 'uses' => 'AuthController@doLogin'])->name('doLogin');
    Route::get('/register', 'AuthController@register')->name('register');
    Route::post('/register', ['before' => 'csrf', 'uses' => 'AuthController@doRegister'])->name('doRegister');
	Route::get('/forgot', 'AuthController@forgot')->name('forgot');
	Route::get('/logout', 'AuthController@logout')->name('logout');
	Route::post('/recover', 'AuthController@recover')->name('recover');
});

Route::group([
    'prefix' => 'workshop/users',
    'middleware' => 'auth',
    'namespace' => 'Modules\Users\Http\Controllers',
    'as' => 'workshop::users::'
], function() {
    Route::get('/view/{userId?}', ['uses' => 'UsersController@view'])->name('view');
    Route::get('/geo', ['uses' => 'UsersController@geo'])->name('geo');
    Route::get('/profile/{user}', [
        'middleware' => 'own',
        'uses' => 'UsersController@viewProfile'
    ])->name('profile');
    Route::get('/refreshMyProfilePanel/{user}', [
        'middleware' => 'ajax',
        'uses' => 'UsersController@refreshMyProfilePanel'
    ])->name('refreshMyProfilePanel');
    Route::get('/refreshAvatarPanel/{user}', [
        'middleware' => 'ajax',
        'uses' => 'UsersController@refreshAvatarPanel'
    ])->name('refreshAvatarPanel');
    Route::post('/update/{user}', 'UsersController@update')->name('update');
    Route::get('/remove/{user}', [
        'middleware' => 'ajax',
        'uses' => 'UsersController@remove'
    ])->name('remove');
});