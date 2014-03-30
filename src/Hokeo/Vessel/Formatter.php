<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\View\Compilers\BladeCompiler;

class Formatter {

	protected $app;

	protected $blade;

	protected $formatters = array();

	protected $set_formatter_name = null;

	public function __construct(Application $app, BladeCompiler $blade)
	{
		$this->app = $app;
		$this->blade = $blade;

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

					$this->app->bind('vessel.formatters.'.$base, function($app) use ($class)
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
		if ($this->exists($name))
		{
			return $this->app->make('vessel.formatters.'.$name);
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
		if ($this->exists($name))
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
	 * Checks if a formatter is available
	 * 
	 * @param  string $name
	 * @return bool
	 */
	public function exists($name)
	{
		return in_array($name, $this->formatters);
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

	public function selectArray()
	{
		return array_combine($this->getAll(), $this->getAll());
	}

	/**
	 * Compiles string as blade (to php)
	 * 
	 * @param  string $string
	 * @return string
	 */
	public function compileBlade($string)
	{
		return $this->blade->compileString($string);
	}
}