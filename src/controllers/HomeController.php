<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class HomeController extends Controller {

	public function getHome()
	{
		return View::make('vessel::home');
	}

}