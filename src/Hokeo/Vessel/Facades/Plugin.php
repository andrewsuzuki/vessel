<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Plugin extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'vessel.plugin';
	}
}