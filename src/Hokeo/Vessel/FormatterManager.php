<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\View\Compilers\BladeCompiler;

class FormatterManager {

	protected $app;

	protected $blade;

	protected $plugin;

	protected $formatters = array();

	protected $native = array('Plain', 'Html', 'Markdown');

	public function __construct(Application $app, BladeCompiler $blade, Plugin $plugin)
	{
		$this->app    = $app;
		$this->blade  = $blade;
		$this->plugin = $plugin;

		foreach ($this->native as $formatter)
		{
			$this->register('Hokeo\\Vessel\\Formatter\\'.$formatter);
		}
	}

	/**
	 * Registers a class as a formatter
	 * 
	 * @return void
	 */
	public function register($class)
	{
		// check that class exists, wasn't already registered, and implements FormatterInterface
		if (!isset($this->formatters[$class]) && class_exists($class) && in_array('Hokeo\\Vessel\\FormatterInterface', class_implements($class)))
		{
			// Bind to IoC
			$this->app->bind($class, function($app) use ($class)
			{
				return new $class;
			});

			$formatter = $this->app->make($class);

			// Get formatter display name and what to use it for
			$name = $formatter->fmName();
			$for = $formatter->fmFor();

			// Verify returns for name + for
			if (is_string($name) && is_array($for))
			{
				// register
				$this->formatters[$class] = array('name' => $name, 'for' => $for, 'class' => $class);
			}
		}
	}

	/**
	 * Returns registered formatters
	 * 
	 * @return array
	 */
	public function getRegistered()
	{
		return $this->formatters;
	}

	/**
	 * Filters formatters for a use
	 *
	 * @param  string $use ('page', 'block', etc)
	 * @return array  Filtered formatters for type
	 */
	public function filterFor($use)
	{
		$filtered = array();

		foreach ($this->formatters as $formatter)
		{
			if (in_array($use, $formatter['for']))
			{
				$filtered[] = $formatter;
			}
		}

		return $filtered;
	}

	/**
	 * Returns <select> array of filtered formatters
	 * 
	 * @return array key=class, value=display name
	 */
	public function filterForSelect($use)
	{
		$formatters = $this->filterFor($use);

		$select = array();

		foreach ($formatters as $formatter)
		{
			$select[$formatter['class']] = $formatter['name'];
		}

		return $select;
	}

	/**
	 * Gets registered formatter by class name
	 * 
	 * @param  string $class
	 * @param  mixed  $type     Null to bypass, or string to only get if it is of specified type ('page', 'block', etc)
	 * @return object Formatter (implementing FormatterInterface)
	 */
	public function get($class, $type = null)
	{
		if ($this->registered($class) && (!$type || in_array($type, $this->formatters[$class]['for'])))
		{
			return $this->app->make($class);
		}
		else
		{
			throw new \Exception('Formatter does not exist, is not of the correct type, or is not valid.');
		}
	}

	/**
	 * Checks if a formatter is registered
	 * 
	 * @param  string $name
	 * @return bool
	 */
	public function registered($class)
	{
		return isset($this->formatters[$class]);
	}

	/**
	 * Tries any number of strings as registered formatter class names, first to last, as possible formatters
	 * 
	 * @return object Formatter
	 */
	public function tryEach()
	{
		foreach (func_get_args() as $formatter)
		{
			if ($formatter && $this->registered($formatter))
			{
				return $this->get($formatter);
			}
		}

		// revert to plain
		return $this->get('Hokeo\\Vessel\\Formatter\\Plain');
	}

	// HELPERS
	
	/**
	 * Converts PHP tags in string to entities
	 * 
	 * @return string String with <? and ?> converted to lt/gt entities
	 */
	public function phpEntities($string)
	{
		return str_replace(['<?', '?>'], ['&lt;?', '?&gt'], $string);
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