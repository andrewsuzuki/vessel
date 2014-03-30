<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Formatter extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\Formatter';
	}
}