<?php

if (!class_exists('VesselNotFoundException'))
{
	class VesselNotFoundException extends \Exception {}
}

App::error(function(VesselNotFoundException $exception)
{
	if (Auth::check())
	{
		View::share('title', '404 Not Found');
		return Response::view('vessel::errors.VesselNotFound');
	}
	else
	{
		return 'An unknown error occurred.';
	}
});