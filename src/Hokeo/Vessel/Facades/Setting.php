<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Setting extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\Setting';
	}
}