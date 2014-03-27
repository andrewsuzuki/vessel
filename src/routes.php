<?php

Route::group(array('prefix' => Config::get('vessel::vessel.uri', 'vessel')), function()
{
	Route::get('test', function()
	{
		$test = '
		
		@if(true == true)

		testy <?php echo 4+5; ?>

		@endif

		';
		
		$compiled = \Illuminate\Support\Facades\Blade::compileString($test);

		try
		{
			return eval("?>" . $compiled);
		}
		catch (\Exception $e)
		{
			exit('umm');
		}
	});

	Route::group(array('before' => 'vessel_auth'), function()
	{
		Route::get('/', array('as' => 'vessel', 'uses' => 'Hokeo\Vessel\BackController@getHome'));
		Route::get('logout', array('as' => 'vessel.logout', 'uses' => 'Hokeo\Vessel\BackController@getLogout'));

		Route::get('pages', array('as' => 'vessel.pages', 'uses' => 'Hokeo\Vessel\PageController@getPages'));
		Route::get('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\Vessel\PageController@getPagesNew'));
		Route::post('pages/new', array('as' => 'vessel.pages.new', 'uses' => 'Hokeo\Vessel\PageController@postPagesNew'));
		Route::get('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\Vessel\PageController@getPagesEdit'));
		Route::post('pages/edit/{id}', array('as' => 'vessel.pages.edit', 'uses' => 'Hokeo\Vessel\PageController@postPagesEdit'));
	});

	Route::group(array('before' => 'vessel_guest'), function()
	{
		Route::get('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\Vessel\BackController@getLogin'));
		Route::post('login', array('as' => 'vessel.login', 'uses' => 'Hokeo\Vessel\BackController@postLogin'));
	});

	Route::post('api/flashinput', array('as' => 'vessel.api.flashinput', 'uses' => 'Hokeo\Vessel\ApiController@flashinput'));

	Route::any("{all}", array("as" => "vessel.dne", "uses" => "Hokeo\Vessel\BackController@getDne"))
	->where("all", ".*");
});

Route::any("{all}", array("as" => "vessel.front.page", "uses" => "Hokeo\Vessel\FrontController@getPage"))
->where("all", ".*");