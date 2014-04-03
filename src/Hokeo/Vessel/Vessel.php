<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;

class Vessel {

	protected $app;

	protected $storage_path;

	protected $dirs = array('', '/');

	public function __construct(Application $app)
	{
		$this->app  = $app;

		$this->storage_path = storage_path().'/vessel';
		$this->checkStoragePath();
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
	 * Checks if all vessel storage directories exist, and makes them if not.
	 */
	public function checkStoragePath()
	{
		foreach ($this->dirs as $path)
		{
			if (!is_dir($this->storage_path.$path))
			{
				mkdir($this->storage_path.$path, 0777, true);
			}
		}
	}

	/**
	 * Get absolute path to path relative to app/storage/vessel
	 * 
	 * @param  string $path
	 * @return string
	 */
	public function path($path = '')
	{
		if (in_array($path, $this->dirs))
		{
			return $this->storage_path.$path;
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