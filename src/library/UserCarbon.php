<?php namespace Hokeo\Vessel;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UserCarbon extends Carbon {

	public function user()
	{
		$format = 'M j, Y, h:i:s';

		if (Auth::check())
		{
			if (Auth::user()->date_format)
			{
				$format = Auth::user()->date_format;
			}
		}	
		else
		{
			$format = Config::get('vessel::vessel.date_format', $format);
		}
		
		return $this->format($format);
	}

}