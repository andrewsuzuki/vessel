<?php namespace Hokeo\Vessel\Facades;

use Illuminate\Support\Facades\Facade;

class FormatterManager extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'Hokeo\\Vessel\\FormatterManager';
	}
}