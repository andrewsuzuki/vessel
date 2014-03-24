<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class FrontController extends Controller {

	public function getPage()
	{
		return 'This is a page.';
	}

}