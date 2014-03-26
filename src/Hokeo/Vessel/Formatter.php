<?php namespace Hokeo\Vessel;

class Formatter {

	protected $formatters = array();

	protected $set_formatter_name = null;

	public function __construct()
	{
		$this->formatters = $this->getAvailable();
	}

	public function __get($name)
	{
		if ($name == 'formatter')
		{
			if ($this->is_set())
			{
				return $this->get($this->set_formatter_name);
			}
			else
			{
				throw new \Exception('A formatter was not set.');
			}
		}
	}

	/**
	 * Gets formatters from formatters directory, and validates them as implementers of FormatterInterface
	 * 
	 * @return array
	 */
	public function getAvailable()
	{
		$seeds = scandir(__DIR__.'/../../formatters');

		$formatters = array();

		foreach ($seeds as $seed)
		{
			if (substr($seed, -4) == '.php')
			{
				$base = basename($seed, '.php');
				$class = 'Hokeo\\Vessel\\Formatter\\'.$base;

				// check that class exists and implements FormatterInterface
				if (class_exists($class) && in_array('Hokeo\Vessel\FormatterInterface', class_implements($class)))
				{
					$formatters[] = $base;

					\Illuminate\Support\Facades\App::bind('hokeo.vessel.formatters.'.$base, function($app) use ($class)
					{
						return new $class;
					});
				}
			}
		}

		return $formatters;
	}

	/**
	 * Returns validated formatters set in Vessel instance
	 * 
	 * @return array
	 */
	public function getAll()
	{
		return $this->formatters;
	}

	/**
	 * Gets formatter by name
	 * 
	 * @param  string $name
	 * @return object Formatter implementing FormatterInterface
	 */
	public function get($name)
	{
		if (in_array($name, $this->formatters))
		{
			return \Illuminate\Support\Facades\App::make('hokeo.vessel.formatters.'.$name);
		}
		else
		{
			throw new \Exception('Formatter does not exist or is not valid.');
		}
	}

	/**
	 * Sets formatter (for front/back-end rendering/editing)
	 * 
	 * @param string $name Name of formatter (must be set in $this->formatters)
	 */
	public function set($name)
	{
		if (in_array($name, $this->formatters))
		{
			$this->set_formatter_name = $name;
		}
	}

	/**
	 * Checks if a formatter was set
	 * 
	 * @return boolean
	 */
	public function is_set()
	{
		return true && $this->set_formatter_name;
	}

	/**
	 * Returns set formatter
	 * 
	 * @return object
	 */
	public function formatter()
	{
		return $this->formatter;
	}

	/**
	 * Calls useAssets on set formatter
	 */
	public function useAssets()
	{
		if ($this->is_set())
		{
			$this->formatter()->useAssets();
		}
	}
}