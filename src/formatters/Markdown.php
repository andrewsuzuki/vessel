<?php

namespace Hokeo\Vessel\Formatter;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;
use Hokeo\Vessel\Facades\Vessel;
use Hokeo\Vessel\Facades\Asset;
use Hokeo\Vessel\Facades\FormatterManager;

class Markdown extends \cebe\markdown\Markdown implements FormatterInterface
{
	/**
	 * Return name of formatter
	 * 
	 * @return string
	 */
	public function fmName()
	{
		return 'Markdown';
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
		return View::make('vessel::editor.Markdown.editor')->with(array('content' => $raw))->render();
	}

	/**
	 * Process editor interface submission
	 * 
	 * @return array Raw content, and null (for made=raw)
	 */
	public function fmProcess()
	{
		$raw  = Input::get('content');
		$made = FormatterManager::compileBlade($this->parse(FormatterManager::phpEntities($raw)));
		return array($raw, $made);
	}

	/**
	 * Sets up formatter for editing interface
	 * 
	 * @return void
	 */
	public function fmSetup()
	{
		Asset::js(asset('packages/hokeo/vessel/editor/Markdown/EpicEditor/js/epiceditor.min.js'), 'epic-editor');
		Asset::js(asset('packages/hokeo/vessel/editor/Markdown/EpicEditor/js/epiceditor-init.js'), 'epic-editor-init');
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