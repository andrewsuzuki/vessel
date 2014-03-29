<?php

namespace Hokeo\Vessel\Formatter;

use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;
use Hokeo\Vessel\Facades\Asset;

class Markdown extends \cebe\markdown\Markdown implements FormatterInterface
{
	public function getName() {	return 'Markdown'; }

	public function isCompiled() { return true; }

	public function useAssets()
	{
		Asset::js(asset('packages/hokeo/vessel/editor/Markdown/EpicEditor/js/epiceditor.min.js'), 'epic-editor');
		Asset::js(asset('packages/hokeo/vessel/editor/Markdown/EpicEditor/js/epiceditor-init.js'), 'epic-editor-init');
	}

	public function getEditorHtml($content = null)
	{
		return View::make('vessel::editor.Markdown.editor')->with(compact('content'))->render();
	}

	public function render($string)
	{
		return $this->parse($string);
	}

	// CEBE/MARKDOWN CODE BLOCK EXTENSION (code pre (```) blocks and code as-is (~~~) blocks)
	
	protected function identifyLine($lines, $current)
	{
		if (strncmp($lines[$current], '```', 3) === 0) {
			return 'fencedCode';
		}

		if (strncmp($lines[$current], '~~~', 3) === 0) {
			return 'asis';
		}

		return parent::identifyLine($lines, $current);
	}

	protected function consumeFencedCode($lines, $current)
	{
		$block = [
			'type'    => 'fencedCode',
			'content' => [],
		];

		$line = rtrim($lines[$current]);

		$fence = substr($line, 0, $pos = strrpos($line, '`') + 1);
		$language = substr($line, $pos);

		if (!empty($language))
			$block['language'] = $language;

		for ($i = $current + 1, $count = count($lines); $i < $count; $i++)
		{
			if (rtrim($line = $lines[$i]) !== $fence)
				$block['content'][] = $line;
			else
				break;
		}

		return [$block, $i];
	}

	protected function renderFencedCode($block)
	{
		return '<pre><code '.((isset($block['language'])) ? 'class="language-'.$block['language'].'"' : '').'>'.htmlspecialchars(implode("\n", $block['content'])."\n", ENT_NOQUOTES, 'UTF-8').'</code></pre>';
	}

	protected function consumeAsis($lines, $current)
	{
		// create block array
		$block = [
			'type'    => 'Asis',
			'content' => [],
		];

		$line = rtrim($lines[$current]);

		$fence = substr($line, 0, $pos = strrpos($line, '~') + 1);

		for ($i = $current + 1, $count = count($lines); $i < $count; $i++)
		{
			if (rtrim($line = $lines[$i]) !== $fence)
				$block['content'][] = $line;
			else
				break;
		}

		return [$block, $i];
	}

	protected function renderAsis($block)
	{
		return implode("\n", $block['content']);
	}

}