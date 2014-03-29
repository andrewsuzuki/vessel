<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class Theme extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'vessel.theme';
	}
}