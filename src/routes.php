<?php

Route::group(array('prefix' => Config::get('vessel::vessel.uri', 'vessel')), function()
{
	Route::group(array('before' => 'vessel_auth'), function()
	{
		Route::get('/', array('as' => 'vessel', 'uses' => 'Hokeo\Vessel\BackController@getHome'));
		Route::get('logout', array('as' => 'vessel.logout', 'uses' => 'Hokeo\Vessel\BackController@getLogout'));

		Route::get('pages', array('as' => 'vessel.pages', 'uses' => 'Hokeo\Vessel\PageController@getPages'));
		Route::get('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\Vessel\PageController@getPagesNew'));
		Route::post('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\Vessel\PageController@postPagesNew'));
		Route::get('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\Vessel\PageController@getPagesEdit'));
		Route::post('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\Vessel\PageController@postPagesEdit'));
		Route::get('pages/delete/{id}', array('as' => 'vessel.pages.delete', 'uses' => 'Hokeo\Vessel\PageController@getPagesDelete'));

		Route::get('pagehistory/delete/all/{id}', array('as' => 'vessel.pagehistory.delete.all', 'uses' => 'Hokeo\Vessel\PageController@getPageHistoryDeleteAll'));
		Route::get('pagehistory/delete/{id}', array('as' => 'vessel.pagehistory.delete', 'uses' => 'Hokeo\Vessel\PageController@getPageHistoryDelete'));
	});

	Route::group(array('before' => 'vessel_guest'), function()
	{
		Route::get('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\Vessel\BackController@getLogin'));
		Route::post('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\Vessel\BackController@postLogin'));
	});

	Route::post('api/flashinput', array('as' => 'vessel.api.flashinput', 'uses' => 'Hokeo\Vessel\ApiController@flashinput'));
	// Route::get('api/test', array('as' => 'vessel.api.test', 'uses' => 'Hokeo\Vessel\ApiController@getDelete'));

	Route::any("{all}", array("as" => "vessel.dne", "uses" => "Hokeo\Vessel\BackController@getDne"))
	->where("all", ".*");
});

Route::any("{all}", array("as" => "vessel.front.page", "uses" => "Hokeo\Vessel\FrontController@getPage"))
->where("all", ".*");