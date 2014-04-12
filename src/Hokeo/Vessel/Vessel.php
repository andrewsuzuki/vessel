<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Filesystem\Filesystem;

class Vessel {

	protected $app;

	protected $filesystem;

	protected $dirs = array('plugins', 'themes');

	public function __construct(Application $app, Filesystem $filesystem)
	{
		$this->app        = $app;
		$this->filesystem = $filesystem;

		$this->checkDirs();
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