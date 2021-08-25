<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', function () {
//
////    $theme = Theme::find(Theme::getCurrent());
////    var_dump($theme->getDescription());
//
//    $layout = Theme::config('layout');
//    return theme("layouts.{$layout}");
//
//    //return view('global::welcome');
//});

Route::get('/test', function () {
//    $workshopTheme = config('themes.workshop');
//    Theme::setCurrent($workshopTheme);
//    var_dump(Theme::asset('js/test.js'));exit;
//$theme = Theme::find(Theme::getCurrent());
//var_dump($theme->getDescription());
    $workshopTheme = Theme::getCurrent();
    return theme('test', compact('workshopTheme'));
});


/*
 * LaravelTips classes
 */
Route::resource('demo', 'DemoController');

