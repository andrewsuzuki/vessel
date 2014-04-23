<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class MenuManager extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\MenuManager';
	}
}