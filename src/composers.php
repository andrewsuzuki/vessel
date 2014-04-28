<?php

/*
|--------------------------------------------------------------------------
| View Composers and Creators
|--------------------------------------------------------------------------
*/

View::creator('vessel::layout', function($view)
{
	$css = Hokeo\Vessel\Facades\Asset::make('css');
	$js = Hokeo\Vessel\Facades\Asset::make('js');

	\Hokeo\Vessel\Facades\MenuManager::backMenu();
	$mainmenu = \Hokeo\Vessel\Facades\MenuManager::handler('back.main')->render();

	$notifications = Krucas\Notification\Facades\Notification::showAll();

	$view->with(compact('css', 'js', 'mainmenu', 'notifications'));
});