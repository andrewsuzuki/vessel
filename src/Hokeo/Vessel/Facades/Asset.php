<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Asset extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'vessel.asset';
	}
}