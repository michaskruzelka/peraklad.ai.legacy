<?php

Route::group([
    'prefix' => 'workshop/projects',
    'namespace' => 'Modules\Projects\Http\Controllers',
    'middleware' => 'auth',
    'as' => 'workshop::projects::'
], function()
{

	Route::get('/list/me/{in?}/{mode?}/{state?}/{page?}', 'ReleasesController@viewMyList')
        ->name('my')
        ->where([
            'page' => '([1-9]+)([0-9]*)',
            'state' => '(un|de|fa|co|all){1}',
            'in' => '(owner|member|all)',
            'mode' => '(all|private|public)'
        ])
    ;
	Route::get('/list/{userId?}/{in?}/{mode?}/{state?}/{page?}',
        ['uses' => 'ReleasesController@viewList'])
        ->name('list')
        ->where([
            'page' => '([1-9]+)([0-9]*)',
            'state' => '(un|de|fa|co|all){1}',
            'in' => '(owner|member|all)',
            'mode' => '(all|private|public)'
        ])
    ;

    Route::get('/new', 'ProjectsController@create')->name('new');
	Route::post('/store', ['before' => 'csrf', 'uses' => 'ProjectsController@store'])->name('store');
	Route::put('/update', ['before' => 'csrf', 'uses' => 'ProjectsController@update'])->name('update');
	Route::get('/edit/{project}', 'ProjectsController@edit')->name('edit');
	Route::post('/destroy', 'ProjectsController@destroy')->name('destroy');

    Route::get('/refreshMyProjectsList', ['middleware' => 'ajax', 'uses' => 'ProjectsController@refreshMyProjectsList'])->name('refreshMyProjectsList');
	Route::get('/refreshCreateForm', ['middleware' => 'ajax', 'uses' => 'ProjectsController@refreshCreateForm'])->name('refreshCreateForm');
	Route::get('/refreshUpdateForm/{project}', ['middleware' => 'ajax', 'uses' => 'ProjectsController@refreshCreateForm'])->name('refreshUpdateForm');
	Route::get('/refreshUploadPoster', ['middleware' => 'ajax', 'uses' => 'ProjectsController@refreshUploadPoster'])->name('refreshUploadPoster');
	Route::post('/searchImdb', ['before' => 'csrf', 'uses' => 'ProjectsController@searchImdb'])->name('searchImdb');
	Route::post('/searchByKeywords', ['before' => 'csrf', 'uses' => 'ProjectsController@searchByKeywords'])->name('keysearch');

});

Route::group([
    'prefix' => 'workshop/releases',
    'namespace' => 'Modules\Projects\Http\Controllers',
    'middleware' => 'auth',
    'as' => 'workshop::releases::'
], function()
{
    Route::get('/refreshReleasesForm/{project}', [
        'middleware' => 'ajax',
        'uses' => 'ReleasesController@refreshReleasesForm'
    ])->name('refreshReleasesForm');

    Route::post('/store', [
        'before' => 'csrf',
        'uses' => 'ReleasesController@store'
    ])->name('store');

	Route::post('/searchOpensubtitles', [
        'before' => 'csrf',
        'uses' => 'ReleasesController@searchOpensubtitles'
    ])->name('searchOpensubtitles');

	Route::post('/destroy', [
        'before' => 'csrf',
        'uses' => 'ReleasesController@destroy'
    ])->name('destroy');

	Route::post('/restore', [
			'before' => 'csrf',
			'uses' => 'ReleasesController@restore'
	])->name('restore');

    Route::post('/update', [
        'before' => 'csrf',
        'uses' => 'ReleasesController@update'
    ])->name('update');

	Route::post('/removeMember/{release}', [
        'before' => 'csrf',
        'uses' => 'ReleasesController@removeMember'
    ])->name('removeMember');

    Route::post('/addMember/{release}', [
        'before' => 'csrf',
        'uses' => 'ReleasesController@addMember'
    ])->name('addMember');

    Route::post('/approveMember/{release}', [
        'before' => 'csrf',
        'uses' => 'ReleasesController@approveMember'
    ])->name('approveMember');

    Route::get('/refreshReleasesList/{userId?}/{in?}/{mode?}/{state?}/{page?}', [
        'middleware' => 'ajax',
        'uses' => 'ReleasesController@refreshReleasesList'
    ])
        ->name('refreshReleasesList')
        ->where([
            'page' => '([1-9]+)([0-9]*)',
            'state' => '(un|de|fa|co|all){1}',
            'in' => '(owner|member|all)',
            'mode' => '(all|private|public)'
        ])
    ;

    Route::get('/complete/{release}', 'ReleasesController@complete')->name('complete');
    Route::get('/backToEdit/{release}', 'ReleasesController@backToEdit')->name('backToEdit');

    Route::group([
        'prefix' => 'statistic',
        'as' => 'statistic::'
    ], function() {

        Route::get('view/{release}', [
            'middleware' => 'ajax',
            'uses' => 'StatisticController@view'
        ])->name('view');

        Route::get('trend/{release}/{period}', [
            'middleware' => 'ajax',
            'uses' => 'StatisticController@trend'
        ])->name('trend')->where(['period' => '(day|week)']);

    });

    Route::group([
        'prefix' => 'subrip',
        'as' => 'subrip::'
    ], function() {

        Route::get('view/{release}/{format?}', 'ReleasesController@subRipView')
            ->name('view')
            ->where(['format' => '(la|cy){1}'])
        ;

        Route::get('refreshSubRipPanel/{release}/{format?}', [
            'middleware' => 'ajax',
            'uses' => 'ReleasesController@refreshSubRipPanel'
        ])->name('refreshSubRipPanel')->where(['format' => '(la|cy){1}']);

        Route::post('save/{release}/{format}', [
            'before' => 'csrf',
            'uses' => 'ReleasesController@subRipSave'
        ])->name('save')->where(['format' => '(la|cy){1}']);

        Route::get('regenerate/{release}/{format}', [
            'middleware' => 'ajax',
            'uses' => 'ReleasesController@subRipRegenerate'
        ])->name('regenerate')->where(['format' => '(la|cy){1}']);

        Route::post('download/{release}/{format}', [
            'before' => 'csrf',
            'middleware' => 'projects.download',
            'uses' => 'ReleasesController@subRipDownload'
        ])->name('download')->where(['format' => '(la|cy){1}']);

    });

});

Route::group([
    'prefix' => 'workshop/subtitles',
    'namespace' => 'Modules\Projects\Http\Controllers',
    'middleware' => 'auth',
    'as' => 'workshop::subtitles::'
], function() {

    Route::get('view/{releaseId}/{status?}/{n?}', 'SubtitlesController@view')
        ->name('view')
        ->where(['n' => '\d*', 'status' => '(un|cl|sa|all){1}'])
    ;

    Route::get('/refreshTranslationPanel/{releaseId}/{status?}/{n?}', [
        'middleware' => 'ajax',
        'uses' => 'SubtitlesController@refreshTranslationPanel'
    ])->name('refreshTranslationPanel')->where(['n' => '\d*', 'status' => '(un|cl|sa|all){1}']);

    Route::post('/underSave/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@underSave'
    ])->name('underSave');

    Route::post('/save/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@save'
    ])->name('save');

    Route::post('/edit/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@edit'
    ])->name('edit');

    Route::post('/updateTiming/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@updateTiming'
    ])->name('updateTiming');

    Route::post('/addComment/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@addComment'
    ])->name('addComment');

    Route::post('/removeComment/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@removeComment'
    ])->name('removeComment');

    Route::get('/refreshCommentsPanel/{subtitle}', [
        'middleware' => 'ajax',
        'uses' => 'SubtitlesController@refreshCommentsPanel'
    ])->name('refreshCommentsPanel');

    Route::get('/refreshVersionsPanel/{subtitle}', [
        'middleware' => 'ajax',
        'uses' => 'SubtitlesController@refreshVersionsPanel'
    ])->name('refreshVersionsPanel');

    Route::post('/addVersion/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@addVersion'
    ])->name('addVersion');

    Route::post('/removeVersion/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@removeVersion'
    ])->name('removeVersion');

    Route::post('/approveVersion/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@approveVersion'
    ])->name('approveVersion');

    Route::post('/likeVersion/{subtitle}', [
        'before' => 'csrf',
        'uses' => 'SubtitlesController@likeVersion'
    ])->name('likeVersion');

});