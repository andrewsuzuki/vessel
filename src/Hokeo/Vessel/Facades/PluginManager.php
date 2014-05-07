<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class PluginManager extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\PluginManager';
	}
}