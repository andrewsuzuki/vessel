<?php

Route::group(array('prefix' => Config::get('vessel::vessel.uri', 'vessel')), function()
{
	Route::group(array('before' => 'vessel_auth'), function()
	{
		Route::get('/', array('as' => 'vessel', 'uses' => 'Hokeo\Vessel\BackController@getHome'));
		Route::get('pages', array('as' => 'vessel.pages', 'uses' => 'Hokeo\Vessel\BackController@getPages'));
		Route::get('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\Vessel\BackController@getPagesNew'));
		Route::post('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\Vessel\BackController@postPagesNew'));
		Route::get('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\Vessel\BackController@getPagesEdit'));
		Route::post('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\Vessel\BackController@postPagesEdit'));
		Route::get('logout', array('as' => 'vessel.logout', 'uses' => 'Hokeo\Vessel\BackController@getLogout'));
	});

	Route::group(array('before' => 'vessel_guest'), function()
	{
		Route::get('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\Vessel\BackController@getLogin'));
		Route::post('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\Vessel\BackController@postLogin'));
	});

	Route::any("{all}", array("as" => "vessel.dne", "uses" => "Hokeo\Vessel\BackController@getDne"))
	->where("all", ".*");
});

Route::any("{all}", array("as" => "vessel.front.page", "uses" => "Hokeo\Vessel\FrontController@getPage"))
->where("all", ".*");