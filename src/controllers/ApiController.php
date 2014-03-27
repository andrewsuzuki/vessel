<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller {

	public function flashinput()
	{
		Input::flash();
		return Response::trip(true);
	}

}