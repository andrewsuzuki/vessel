<?php namespace Hokeo\Vessel;

use Illuminate\Support\Facades\Facade;

class ThemeFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'vessel.theme';
	}
}