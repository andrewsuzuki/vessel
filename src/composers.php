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

	\Hokeo\Vessel\Facades\Vessel::backMenu();
	$mainmenu = \Menu\Menu::handler('vessel.menu.main')->render();

	$notifications = Krucas\Notification\Facades\Notification::showAll();

	$view->with(compact('css', 'js', 'mainmenu', 'notifications'));
});