<?php namespace Hokeo\Vessel;

use Illuminate\Support\Facades\Facade;

class AssetFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'vessel.asset';
	}
}