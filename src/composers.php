<?php

/*
|--------------------------------------------------------------------------
| View Composers and Creators
|--------------------------------------------------------------------------
*/

View::creator('vessel::layout', function($view)
{
	\Hokeo\Vessel\Facades\Formatter::useAssets();
	$css = Hokeo\Vessel\Facades\Asset::make('css');
	$js = Hokeo\Vessel\Facades\Asset::make('js');

	\Hokeo\Vessel\Facades\Menu::backMenu();
	$mainmenu = \Hokeo\Vessel\Facades\Menu::handler('vessel.back.menu.main')->render();

	$notifications = Krucas\Notification\Facades\Notification::showAll();

	$view->with(compact('css', 'js', 'mainmenu', 'notifications'));
});