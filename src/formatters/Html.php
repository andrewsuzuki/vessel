<?php

namespace Hokeo\Vessel\Formatter;

use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;
use Hokeo\Vessel\Facades\Asset;

class Html implements FormatterInterface
{
	public function getName() { return 'Html'; }

	public function isCompiled() { return false; }

	public function useAssets()	{
		Asset::js('//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js', 'ace');
		Asset::js(asset('packages/hokeo/vessel/editor/Html/Ace/js/ace-init.js'), 'ace-init');
	}

	public function getEditorHtml($content = null)
	{
		return View::make('vessel::editor.Html.editor')->with(compact('content'))->render();
	}

	public function render($string)
	{
		return $string;
	}
}