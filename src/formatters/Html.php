<?php

namespace Hokeo\Vessel\Formatter;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;
use Hokeo\Vessel\Facades\Vessel;
use Hokeo\Vessel\Facades\Asset;
use Hokeo\Vessel\Facades\FormatterManager;

class Html implements FormatterInterface
{
	/**
	 * Return name of formatter
	 * 
	 * @return string
	 */
	public function fmName()
	{
		return 'Html';
	}

	/**
	 * Gets formattable content types
	 * 
	 * @return array
	 */
	public function fmFor()
	{
		return array('page', 'block');
	}

	/**
	 * Returns editor interface html
	 * 
	 * @return string
	 */
	public function fmInterface($raw, $made)
	{
		return View::make('vessel::editor.Html.editor')->with(array('content' => $raw))->render();
	}

	/**
	 * Process editor interface submission
	 * 
	 * @return array Raw content, and null (for made=raw)
	 */
	public function fmProcess()
	{
		$raw = Input::get('content');
		$made = FormatterManager::compileBlade(FormatterManager::phpEntities($raw));
		return array($raw, $made);
	}

	/**
	 * Sets up formatter for editing interface
	 * 
	 * @return void
	 */
	public function fmSetup()
	{
		Asset::js('//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js', 'ace');
		Asset::js(asset('packages/hokeo/vessel/editor/Html/Ace/js/ace-init.js'), 'ace-init');
	}

	/**
	 * Uses formatter (front end)
	 * 
	 * @param  string $raw  Raw saved content
	 * @param  string $made Made saved content
	 * @return string       Content to display
	 */
	public function fmUse($raw, $made)
	{
		return Vessel::returnEval($made);
	}
}