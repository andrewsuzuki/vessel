<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\Menu';
	}
}