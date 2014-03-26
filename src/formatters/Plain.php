<?php

namespace Hokeo\Vessel\Formatter;

use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;

class Plain implements FormatterInterface
{
	public function useAssets()
	{
		//
	}
	
	public function getEditorHtml()
	{
		return View::make('vessel::editor.Plain.editor')->render();
	}

	public function render($string)
	{
		return false;
	}
}