<?php namespace Hokeo\MarkdownFormatter;

use cebe\markdown\Markdown;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Hokeo\Vessel\FormatterInterface;
use Hokeo\Vessel\Facades\Vessel;
use Hokeo\Vessel\Facades\Asset;
use Hokeo\Vessel\Facades\FormatterManager;

class MarkdownParser extends Markdown
{
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