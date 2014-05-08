<?php namespace Hokeo\MarkdownFormatter;

use Hokeo\Vessel\Pluggable;
use Hokeo\Vessel\Facades\FormatterManager;

class MarkdownFormatter extends Pluggable {

	/**
	 * Return plugin information
	 * 
	 * @return array
	 */
	public function pluginInfo()
	{
		return [
			'title'  => 'Markdown Formatter',
			'author' => 'Hokeo',
		];
	}

	public function boot()
	{

	}

	public function register()
	{
		FormatterManager::register('Hokeo\\MarkdownFormatter\\Formatter');
	}
}