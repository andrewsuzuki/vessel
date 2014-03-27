<?php

namespace Hokeo\Vessel\Formatter;

use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;

class Plain implements FormatterInterface
{
	public function getName()
	{
		return 'Plain';
	}

	public function useAssets() {}
	
	public function getEditorHtml($content = null)
	{
		return View::make('vessel::editor.Plain.editor')->with(compact('content'))->render();
	}

	public function isCompiled()
	{
		return false;
	}

	public function render($string)
	{
		return $string;
	}
}