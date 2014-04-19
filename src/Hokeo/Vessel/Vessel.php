<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class Vessel {

	protected $app;

	protected $config;

	protected $filesystem;

	public function __construct(Application $app, Repository $config, Filesystem $filesystem)
	{
		$this->app        = $app;
		$this->config     = $config;
		$this->filesystem = $filesystem;

		$this->checkDirs(array('plugins', 'themes', $this->config->get('vessel::upload_path', 'public/uploads')));
		$this->setTimezone();
	}

	/**
	 * Gets vessel version or version component
	 * 
	 * @param  string $type full|short|major|minor|patch
	 * @return string
	 */
	public function getVersion($type = 'full')
	{
		$available = array('full', 'short', 'major', 'minor', 'patch');
		if (in_array($type, $available)) return (string) $this->app->make('vessel.version.'.$type);
	}

	/**
	 * Checks if all required vessel directories exist, and makes them if not.
	 * 
	 * @param  array $dirs Directories to check, if characters 0 is NOT / it will append to base_path
	 * @return void
	 */
	public function checkDirs($dirs)
	{
		foreach ($dirs as $dir)
		{
			if (!is_string($dir)) throw new \Exception('Directory path must be string.'); // check dir is string
			if ($dir[0] != '/') $dir = base_path($dir); // prepend base_path if [0] != /
			if (!$this->filesystem->isDirectory($dir)) $this->filesystem->makeDirectory($dir, 0777, true); // if directory does not exist, make it
		}
	}

	/**
	 * Sets laravel timezone based on site setting
	 * 
	 * @return type description
	 */
	public function setTimezone()
	{
		if (($timezone = $this->config->get('vset::site.timezone')))
			$this->config->set('app.timezone', $timezone); // if a timezone is set, then rewrite laravel config value
	}

	/**
	 * Evaluate (execute) PHP code in string
	 * 
	 * @param  string $content String, possibly containing PHP
	 * @return string          Evaluated content
	 */
	public function returnEval($content)
	{
		ob_start();

		try
		{
			eval('?>'.$content);
		}
		catch (Exception $e)
		{
			ob_end_clean();
			throw $e;
		}

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}
}