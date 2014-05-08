<?php namespace Hokeo\PlainFormatter;

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
		return 'Plain';
	}

	/**
	 * Gets formattable content types
	 * 
	 * @return array
	 */
	public function forTypes()
	{
		return array('page', 'block');
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
		return View::make('vessel::editor.Plain.editor')->with(array('content' => $raw))->render();
	}

	/**
	 * Process editor interface submission
	 * 
	 * @return array Raw content, and null (for made=raw)
	 */
	public function submit()
	{
		$raw  = Input::get('content');
		$made = Blade::compileString(FormatterManager::phpEntities($raw));
		return array($raw, $made);
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
		return Vessel::returnEval($made);
	}
}