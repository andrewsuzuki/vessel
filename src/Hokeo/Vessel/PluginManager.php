<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Andrewsuzuki\Perm\Perm;

class PluginManager {

	protected $app;
	
	protected $filesystem;

	protected $perm;

	protected $plugins_path;

	protected $registered;

	public function __construct(
		Application $app,
		Filesystem $file,
		Perm $perm)
	{
		$this->app    = $app;
		$this->file   = $file;
		$this->perm   = $perm;

		$this->plugins_path = base_path().'/plugins';
		$this->registered = array();
	}

	/**
	 * Returns base plugins directory path
	 * 
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->plugins_path;
	}

	/**
	 * Get available plugins
	 * 
	 * @return array
	 */
	public function getAvailable()
	{
		$available = array();

		$vendors = $this->file->directories($this->getBasePath());

		foreach ($vendors as $vendor)
		{
			$plugins = $this->file->directories($vendor);

			foreach ($plugins as $plugin)
			{
				if ($this->file->exists($plugin.DIRECTORY_SEPARATOR.'Plugin.php'))
				{
					// add to available (namespace prefix => vendor/plugin subdirectory)
					$vsub = basename($vendor);
					$psub = basename($plugin);
					$namespace = ucfirst(strtolower($vsub)).'\\'.ucfirst(strtolower($psub)).'\\';
					$available[$namespace] = $vsub.DIRECTORY_SEPARATOR.$psub;
				}
			}
		}

		return $available;
	}

	/**
	 * Register all enabled plugins
	 * 
	 * @return void
	 */
	public function registerEnabled()
	{
		$enabled = $this->perm->load('vessel.plugins')->get('enabled');
		if (!is_array($enabled)) return;

		foreach ($enabled as $namespace)
			$this->register($namespace);
	}

	/**
	 * Register a plugin in a given autoloaded plugin namespace
	 * 
	 * @return boolean If plugin was registered
	 */
	public function register($namespace)
	{
		$class = $namespace.'Plugin';

		// register Plugin as a service provider if it exists and extends Pluggable
		if (class_exists($class) && get_parent_class($class) == 'Hokeo\\Vessel\\Pluggable')
		{
			$this->app->register($class);
			$this->registered[$namespace] = $namespace;
			return true;
		}

		return false;
	}

	/**
	 * Get registered plugins
	 * 
	 * @return array Array of registered plugin namespace prefixes
	 */
	public function getRegistered()
	{
		return $this->registered;
	}
}