<?php namespace Hokeo\HtmlFormatter;

use Hokeo\Vessel\Pluggable;
use Hokeo\Vessel\Facades\FormatterManager;

class HtmlFormatter extends Pluggable {

	/**
	 * Return plugin information
	 * 
	 * @return array
	 */
	public function pluginInfo()
	{
		return [
			'title'  => 'HTML Formatter',
			'author' => 'Hokeo',
		];
	}

	public function boot()
	{

	}

	public function register()
	{
		FormatterManager::register('Hokeo\\HtmlFormatter\\Formatter');
	}
}