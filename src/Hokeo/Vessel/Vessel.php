<?php namespace Hokeo\Vessel;

class Vessel {

	protected $storage_path;

	protected $dirs = array('/', '/pages', '/pages/compiled');

	public function __construct()
	{
		$this->storage_path = storage_path().'/vessel';
		$this->checkStoragePath();
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
	public function path($path)
	{
		if (in_array($path, $this->dirs))
		{
			return $this->storage_path.$path;
		}
	}
}