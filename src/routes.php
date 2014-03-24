<?php

Route::group(array('prefix' => Config::get('vessel::vessel.uri', 'vessel')), function()
{
	Route::get('/', array('as' => 'vessel', 'uses' => 'Hokeo\Vessel\HomeController@getHome'));
});

Route::any("{all}", array("as" => "vessel/front/page", "uses" => "Hokeo\Vessel\FrontController@getPage"))
->where("all", ".*");