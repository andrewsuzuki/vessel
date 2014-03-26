<?php

namespace Hokeo\Vessel\Formatter;

use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;

class Markdown implements FormatterInterface
{
	public function useAssets()
	{

	}

	public function getEditorHtml()
	{
		return View::make('vessel::editor.Markdown.editor')->render();
	}

	public function render($string)
	{
		return 'dis is md compiled';
	}
}