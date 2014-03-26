<?php namespace Hokeo\Vessel;

use Illuminate\Support\Facades\Facade;

class VesselFacade extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'hokeo.vessel.vessel';
	}
}