<?php

Route::filter('vessel_auth', function()
{
	if (Auth::guest())
	{
		return Redirect::guest('vessel/login');
	}
});

Route::filter('vessel_guest', function()
{
	if (Auth::check())
	{
		return Redirect::route('vessel');
	}
});
