<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller {

	public function getHome()
	{
		//dd(Config::get('vessel::vessel.uri'));
		return 'Hey, this is Vessel home!';
	}

}