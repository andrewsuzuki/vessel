<?php namespace Hokeo\PlainFormatter;

use Hokeo\Vessel\Pluggable;
use Hokeo\Vessel\Facades\FormatterManager;

class PlainFormatter extends Pluggable {

	/**
	 * Return plugin information
	 * 
	 * @return array
	 */
	public function pluginInfo()
	{
		return [
			'title'  => 'Plain Formatter',
			'author' => 'Hokeo',
		];
	}

	public function boot()
	{

	}

	public function register()
	{
		FormatterManager::register('Hokeo\\PlainFormatter\\Formatter');
	}
}