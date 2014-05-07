<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Hooker extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\Hooker';
	}
}