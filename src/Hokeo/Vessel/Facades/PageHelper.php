<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class PageHelper extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\PageHelper';
	}
}