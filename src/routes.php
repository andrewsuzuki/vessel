<?php

Route::group(array('prefix' => Config::get('vessel::vessel.uri')), function()
{
	Route::get('/', array('as' => 'vessel', 'uses' => 'Hokeo\Vessel\HomeController@getHome'));
});