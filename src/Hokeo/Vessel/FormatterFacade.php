<?php namespace Hokeo\Vessel;

use Illuminate\Support\Facades\Facade;

class FormatterFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'hokeo.vessel.formatter';
	}
}