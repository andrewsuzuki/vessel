<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class Vessel {

	protected $app;

	protected $config;

	protected $filesystem;

	protected $dirs = array('plugins', 'themes');

	public function __construct(Application $app, Repository $config, Filesystem $filesystem)
	{
		$this->app        = $app;
		$this->config     = $config;
		$this->filesystem = $filesystem;

		$this->checkDirs();
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
	 */
	public function checkDirs()
	{
		foreach ($this->dirs as $dir)
		{
			if (!$this->filesystem->isDirectory(base_path().'/'.$dir))
			{
				$this->filesystem->makeDirectory(base_path().'/'.$dir, 0777, true);
			}
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
			$this->config->set('app.timezone', $timezone);
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