<?php namespace Hokeo\Vessel;

use Composer\Autoload\ClassLoader;

class PluginLoader extends ClassLoader {

	/**
	 * Adds plugin paths
	 * 
	 * @return void
	 */
	public function addPluginPaths()
	{
		$paths = array(
			base_path().'/plugins',
			VESSEL_DIR_SRC.'/nativeplugins',
		);

		$this->add(null, $paths, true); // add as psr-0
	}
}