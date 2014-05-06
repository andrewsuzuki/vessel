<?php

Route::filter('vessel_auth', function()
{
	if (Auth::guest()) return Redirect::guest('vessel/login');
});

Route::filter('vessel_guest', function()
{
	if (Auth::check()) return Redirect::route('vessel');
});

Route::filter('registration_enabled', function()
{
	$settings = Andrewsuzuki\Perm\Facades\Perm::load('vessel.site');
	if (!$settings->registration) return Redirect::guest('vessel/login');
});

$vessel_permission_filters = array(
	'pages_manage'    => null,
	'pages_view'      => null,
	'pages_create'    => null,
	'pages_edit'      => null,
	'pages_delete'    => null,
	'blocks_manage'   => null,
	'blocks_view'     => null,
	'blocks_create'   => null,
	'blocks_edit'     => null,
	'blocks_delete'   => null,
	'users_manage'    => null,
	'users_view'      => null,
	'users_create'    => null,
	'users_edit'      => null,
	'users_delete'    => null,
	'media_manage'    => null,
	'media_upload'    => null,
	'settings_manage' => null,
);

foreach ($vessel_permission_filters as $name => $route)
{
	Route::filter('p.'.$name, function() use ($name, $route)
	{
		if (!can($name)) return Redirect::route(($route) ? $route : 'vessel');
	});
}
