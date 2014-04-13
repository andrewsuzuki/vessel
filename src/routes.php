<?php

Route::group(array('prefix' => Config::get('vessel::vessel.uri', 'vessel')), function()
{
	Route::group(array('before' => 'vessel_auth'), function()
	{
		Route::get('/', array('as' => 'vessel', 'uses' => 'Hokeo\\Vessel\\BackController@getHome'));
		Route::get('logout', array('as' => 'vessel.logout', 'uses' => 'Hokeo\\Vessel\\BackController@getLogout'));

		Route::get('pages', array('as' => 'vessel.pages', 'uses' => 'Hokeo\\Vessel\\PageController@getPages'));
		Route::get('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\\Vessel\\PageController@getPagesNew'));
		Route::post('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\\Vessel\\PageController@postPagesNew'));
		Route::get('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\\Vessel\\PageController@getPagesEdit'));
		Route::post('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\\Vessel\\PageController@postPagesEdit'));
		Route::get('pages/delete/{id}', array('as' => 'vessel.pages.delete', 'uses' => 'Hokeo\\Vessel\\PageController@getPagesDelete'));

		Route::get('pagehistory/delete/all/{id}', array('as' => 'vessel.pagehistory.delete.all', 'uses' => 'Hokeo\\Vessel\\PageController@getPageHistoryDeleteAll'));
		Route::get('pagehistory/delete/{id}', array('as' => 'vessel.pagehistory.delete', 'uses' => 'Hokeo\\Vessel\\PageController@getPageHistoryDelete'));

		Route::get('blocks', array('as' => 'vessel.blocks', 'uses' => 'Hokeo\\Vessel\\BlockController@getBlocks'));
		Route::get('blocks/new', array('as' => 'vessel.blocks.new', 'uses' => 'Hokeo\\Vessel\\BlockController@getBlockNew'));
		Route::post('blocks/new', array('as' => 'vessel.blocks.new', 'uses' => 'Hokeo\\Vessel\\BlockController@postBlockNew'));
		Route::get('blocks/edit/{id}', array('as' => 'vessel.blocks.edit', 'uses' => 'Hokeo\\Vessel\\BlockController@getBlockEdit'));
		Route::post('blocks/edit/{id}', array('as' => 'vessel.blocks.edit', 'uses' => 'Hokeo\\Vessel\\BlockController@postBlockEdit'));
		Route::get('blocks/delete/{id}', array('as' => 'vessel.blocks.delete', 'uses' => 'Hokeo\\Vessel\\BlockController@getBlockDelete'));

		Route::get('settings', array('as' => 'vessel.settings', 'uses' => 'Hokeo\\Vessel\\SettingController@getSettings'));
		Route::post('settings', array('as' => 'vessel.settings', 'uses' => 'Hokeo\\Vessel\\SettingController@postSettings'));

		Route::get('me', array('as' => 'vessel.me', 'uses' => 'Hokeo\\Vessel\\UserController@getMe'));
		Route::post('me', array('as' => 'vessel.me', 'uses' => 'Hokeo\\Vessel\\UserController@postMe'));
	});

	Route::group(array('before' => 'vessel_guest'), function()
	{
		Route::get('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\\Vessel\\BackController@getLogin'));
		Route::post('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\\Vessel\\BackController@postLogin'));
	});

	Route::post('api/flashinput', array('as' => 'vessel.api.flashinput', 'uses' => 'Hokeo\\Vessel\\ApiController@flashinput'));
});

Route::any("{all}", array("as" => "vessel.front.page", "uses" => "Hokeo\\Vessel\\FrontController@getPage"))
->where("all", ".*");