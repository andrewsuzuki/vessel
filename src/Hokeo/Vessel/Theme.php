<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\View\Environment;
use Illuminate\Filesystem\Filesystem;
use Andrewsuzuki\Perm\Perm;

class Theme {

	protected $config;

	protected $app;

	protected $filesystem;

	protected $perm;

	protected $themes_path;

	protected $available = null;

	protected $theme = null;

	protected $themes;

	protected $elements = array();

	protected $current_element = null;

	public function __construct(Application $app, Repository $config, Environment $view, Filesystem $filesystem, Perm $perm)
	{
		$this->app         = $app;
		$this->config      = $config;
		$this->view        = $view;
		$this->filesystem  = $filesystem;
		$this->perm        = $perm;

		$this->themes_path  = base_path().DIRECTORY_SEPARATOR.'themes';
	}

	/**
	 * Get base themes path
	 * 
	 * @return string Absolute path to /themes
	 */
	public function getBasePath()
	{
		return $this->themes_path;
	}

	/**
	 * Gets path to a theme if it exists
	 * @param  string $name Name of theme (directory)
	 * @return string       Path to theme
	 */
	public function getThemePath($name)
	{
		if ($this->filesystem->exists($this->themes_path.DIRECTORY_SEPARATOR.$name))
			return $this->themes_path.DIRECTORY_SEPARATOR.$name;
	}

	/**
	 * Get views in dot notation, recursive, from loaded theme directory
	 * 
	 * @return array                       Array of view names (key = view, value = display)
	 */
	public function getThemeViews()
	{
		if ($this->theme)
		{
			$directory = new \RecursiveDirectoryIterator($this->getThemePath($this->theme['name']));
			$flattened = new \RecursiveIteratorIterator($directory);

			// Make sure the path does not contain "/.Trash*" folders and ends with either _template.blade.php or _template.php
			$files = new \RegexIterator($flattened, '#^(?:[A-Z]:)?(?:/(?!\.Trash)[^/]+)+/[^/]+_template\.(?:blade.php|php)$#Di');

			$return = array();

			foreach ($files as $file)
			{
				$view = str_replace(DIRECTORY_SEPARATOR, '.', // replace / with .
								str_replace(['.blade.php', '.php'], '', // strip extension
									str_replace($this->getThemePath($this->theme['name']).DIRECTORY_SEPARATOR, '', (string) $file) // abs->rel to theme dir
								)
							);

				$view = substr($view, 0, -9); // remove _template

				// make display name (i.e. pages.about turns to Pages > About)
				$display = implode(' > ', array_map(function($part) {
					return ucfirst($part);
				}, explode('.', $view)));

				$return[$view] = $display;
			}	

			return $return;
		}
	}

	/**
	 * Gets <select> array for theme sub-template (with default)
	 * 
	 * @return array
	 */
	public function getThemeViewsSelect()
	{
		$templates = ($this->theme) ? $this->getThemeViews() : array();
		$templates = array_reverse($templates);
		$templates['none'] = '-- Default template --';
		$templates = array_reverse($templates);

		return $templates;
	}

	/**
	 * Loads a stored (or any available) theme for use. 
	 * 
	 * @param  null|string $name      Name of theme directory, or null to use stored setting (if it exists)
	 * @param  boolean     $try_first If true, and if a specified/stored theme could not be found, try first available theme
	 * @return boolean                If a theme was loaded
	 */
	public function load($name = null, $try_first = false)
	{
		$success = false;

		if (!$name)
		{
			$theme = $this->perm->load('vessel.config')->get('theme'); // get set theme from settings

			if ($theme && isset($theme['name']) && isset($theme['info']))
			{
				$this->theme = $theme;
				$success = true;
			}
		}
		else
		{
			$check = $this->check($name);

			if ($check)
			{
				$this->theme = ['name' => $name, 'info' => $check];
				$success = true;
			}
		}

		if ($success)
		{
			// success, so set the view namespace vessel-theme to our theme directory
			$this->view->addNamespace('vessel-theme', $this->themes_path.DIRECTORY_SEPARATOR.$this->theme['name']);
			return true;
		}
		else
		{
			// since name/loading failed, let's try the first available theme
			if ($try_first)
			{
				$available = $this->getAvailable(); // get available

				// if it's not empty, call recursively with name (and prevent infinite loop)
				if (!empty($available))
				{
					$name = key($available);
					$load = $this->load($name, false);

					if ($load)
					{
						$this->set($name); // save it for next time
					}

					return $load; // t/f from recursive call
				}
			}

			return false;
		}
	}

	/**
	 * Sets/saves a theme choice
	 * 
	 * @param string $name Name (directory) of theme
	 */
	public function set($name)
	{
		$check = $this->check($name);

		if (!$check) return false;

		$this->perm->load('vessel.config')->set(array('theme.name' => $name, 'theme.info' => $check));

		return true;
	}

	/**
	 * Get available themes
	 * 
	 * @return array
	 */
	public function getAvailable()
	{
		$themes = $this->filesystem->directories($this->themes_path);

		$available = array();

		foreach ($themes as $theme)
		{
			$check = $this->check($theme);

			if (!$check) continue;
			$name = basename($theme);
			$available[$name] = $check;
		}

		return $available;
	}

	/**
	 * Check for a valid theme (existence of template.blade.php and theme.php with title and author)
	 * 
	 * @param  string $name Name (directory) of theme
	 * @return mixed        Bool false if theme wasn't valid, or info array returned by its theme.php if valid
	 */
	public function check($name)
	{
		$name = basename($name);

		$theme = $this->themes_path.DIRECTORY_SEPARATOR.$name;

		if ($this->filesystem->exists($theme))
		{
			if ($this->filesystem->exists($theme.DIRECTORY_SEPARATOR.'template.blade.php') && $this->filesystem->exists($theme.DIRECTORY_SEPARATOR.'theme.php'))
			{
				$info = $this->filesystem->getRequire($theme.DIRECTORY_SEPARATOR.'theme.php');

				$infocheck = array('title', 'author');

				$failed = false;

				foreach ($infocheck as $check)
				{
					if (!isset($info[$check]) || !strlen($info[$check])) $failed = true; break;
				}

				if ($failed) return false;

				return $info;
			}
		}
	}

	/**
	 * Checks if view in loaded theme dir exists
	 * 
	 * @param  string  $view View (dot notation)
	 * @return boolean
	 */
	public function themeViewExists($view)
	{
		if ($this->theme)
		{
			return $this->view->exists('vessel-theme::'.$view);
		}
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
		if (isset($this->elements[$name]) && $this->current_element !== $name)
		{
			$this->current_element = $name; // set current element to this (preventing recursion)
			
			// Check if the value is callable
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
	 * Sets theme element value
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