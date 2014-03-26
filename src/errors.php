<?php

class VesselNotFoundException extends \Exception {}
App::error(function(VesselNotFoundException $exception)
{
	View::share('title', '404 Not Found');
    return Response::view('vessel::errors.VesselNotFound');
});