<?php namespace Hokeo\ImageFormatter;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Hokeo\Vessel\FormatterInterface;
use Hokeo\Vessel\Facades\Vessel;
use Hokeo\Vessel\Facades\Asset;
use Hokeo\Vessel\Facades\FormatterManager;

class Formatter implements FormatterInterface
{
	/**
	 * Return name of formatter
	 * 
	 * @return string
	 */
	public function name()
	{
		return 'Image';
	}

	/**
	 * Gets formattable content types
	 * 
	 * @return array
	 */
	public function forTypes()
	{
		return array('block');
	}

	/**
	 * Sets up formatter for editing interface
	 * 
	 * @return void
	 */
	public function setupInterface()
	{

	}

	/**
	 * Returns editor interface html
	 * 
	 * @return string
	 */
	public function getInterface($raw, $made)
	{
		View::addNamespace('hokeo/imageformatter', __DIR__.'/views');
		return View::make('hokeo/imageformatter::editor')->with(array('image' => $raw))->render();
	}

	/**
	 * Process editor interface submission
	 * 
	 * @return array Raw content, and null (for made=raw)
	 */
	public function submit()
	{
		$raw  = Input::get('image');
		return array($raw, $raw);
	}

	/**
	 * Uses formatter (front end)
	 * 
	 * @param  string $raw  Raw saved content
	 * @param  string $made Made saved content
	 * @return string       Content to display
	 */
	public function make($raw, $made)
	{
		return '<img src="'.$made.'" alt="" />';
	}
}