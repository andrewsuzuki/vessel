<?php namespace Hokeo\ImageFormatter;

use Hokeo\Vessel\Pluggable;
use Hokeo\Vessel\Facades\FormatterManager;

class ImageFormatter extends Pluggable {

	/**
	 * Return plugin information
	 * 
	 * @return array
	 */
	public function pluginInfo()
	{
		return [
			'title'  => 'Image Formatter',
			'author' => 'Hokeo',
		];
	}

	public function boot()
	{

	}

	public function register()
	{
		FormatterManager::register('Hokeo\\ImageFormatter\\Formatter');
	}
}