<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Vessel extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\Vessel';
	}
}