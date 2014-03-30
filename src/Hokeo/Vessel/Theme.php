<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application as App;
use Illuminate\Config\Repository as Config;
use Illuminate\View\Environment as View;
use Illuminate\Filesystem\Filesystem;

class Theme {

	protected $config;

	protected $app;

	protected $filesystem;

	protected $theme_path;

	protected $themes;

	protected $elements = array();

	public function __construct(App $app, Config $config, View $view, Filesystem $filesystem)
	{
		$this->app         = $app;
		$this->config      = $config;
		$this->view        = $view;
		$this->filesystem  = $filesystem;

		$this->theme_path  = base_path().'/themes';

		$this->view->addNamespace('vessel-themes', $this->theme_path);
	}

	/**
	 * Get available themes
	 * 
	 * @return array
	 */
	public function getAvailable()
	{
		$themes = scandir($this->theme_path);

		$formatters = array();

		foreach ($themes as $theme)
		{
			if (substr($theme, -4) == '.php')
			{
				$base = basename($theme, '.php');
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
	 * Returns set theme element ('content', 'title', 'block', 'stylesheets', etc)
	 * 
	 * @param  string $name Element name
	 * @return mixed        Element value, or null if not set (might be null anyways if lambda returns it)
	 */
	public function element($name)
	{
		// Check if this element's name has been set
		if (isset($this->elements[$name]))
		{
			// Check if the value is a lambda
			if (is_callable($this->elements[$name]))
			{
				return call_user_func_array($this->elements[$name], func_get_args());
			}
			// If not, then return it as-is
			else
			{
				return $this->elements[$name];
			}
		}

		return null;
	}

	/**
	 * Sets theme value
	 * 
	 * @param mixed $name   Name of element ('content', 'title', etc) OR array of multiple associative 'name's and 'value's
	 * @param mixed $value  Anything that can be cast to a string, OR a lambda/callback function (will receive args as-is (name, arg2, arg3, etc) from template)
	 */
	public function setElement($name, $value = null)
	{
		if (is_array($name))
		{
			foreach ($name as $element)
			{
				if (is_array($name) && count($element) >= 2)
				{
					$this->setElement($element[0], $element[1]); #recursion ftw
				}
			}
		}
		else
		{
			$this->elements[$name] = $value;
		}
	}
	
}