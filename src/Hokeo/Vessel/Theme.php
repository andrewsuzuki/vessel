<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\View\Environment;
use Illuminate\Filesystem\Filesystem;
use Andrewsuzuki\Perm\Perm;

class Theme {

	protected $app;

	protected $config;

	protected $view;

	protected $filesystem;

	protected $perm;

	protected $themes_path;

	protected $theme = null;

	protected $elements = array();

	protected $current_element = null;

	public function __construct(
		Application $app,
		Repository $config,
		Environment $view,
		Filesystem $filesystem,
		Perm $perm)
	{
		$this->app         = $app;
		$this->config      = $config;
		$this->view        = $view;
		$this->filesystem  = $filesystem;
		$this->perm        = $perm;

		$this->themes_path     = base_path().'/themes';
		$this->theme           = null;
		$this->elements        = array();
		$this->current_element = null;
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
	 * 
	 * @param  string $name Name or base directory of theme
	 * @return string|void  Path to theme if it exists
	 */
	public function getThemePath($name)
	{
		$name  = basename($name); // dir -> name 
		$theme = $this->themes_path.'/'.$name; // name -> dir

		if ($this->filesystem->exists($theme))
			return $theme;
	}

	/**
	 * Check for a valid theme (existence of template.blade.php and theme.php with title and author)
	 * 
	 * @param  string $name Name or base directory of theme
	 * @return mixed        Info array returned by its theme.php if valid, or false if not
	 */
	public function get($name)
	{
		if ($theme = $this->getThemePath($name)) // check theme dir exists and get path
		{
			if ($this->filesystem->exists($theme.'/template.blade.php') && $this->filesystem->exists($theme.'/theme.php')) // check dir has required files
			{
				$info = $this->filesystem->getRequire($theme.'/theme.php'); // get return from theme.php

				$infocheck = array('title', 'author');

				// if theme return doesn't have each required key, return false
				foreach ($infocheck as $check)
				{
					if (!isset($info[$check]) || !strlen($info[$check])) return false;
				}

				return $info; // otherwise return theme return info array
			}
		}

		return false;
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
		if (!$name) $name = $this->perm->load('vessel.site')->get('theme'); // if name not specified, get from settings

		$info = $this->get($name); // get theme info

		if ($info)
		{
			$this->theme = ['name' => $name, 'info' => $info]; // set theme property

			// success, so set the view namespace vessel-theme to our theme directory
			$this->view->addNamespace('vessel-theme', $this->themes_path.'/'.$this->theme['name']);
			return true;
		}
		else
		{
			// name/loading failed, so we can try to revert to the first available theme
			if ($try_first)
			{
				$available = $this->getAvailable(); // get available

				if (!empty($available))
					return $this->load(key($available), false); // get first name and call this method (+ prevent infinite)
			}

			return false;
		}
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
			if (!($info = $this->get($theme))) continue;
			$name = basename($theme);
			$available[$name] = $info;
		}

		return $available;
	}

	/**
	 * Get views in dot notation, recursive, from loaded theme directory
	 * 
	 * @return array Array of view names (viewname => displayname)
	 */
	public function getThemeSubs()
	{
		if ($this->theme)
		{
			$path = $this->getThemePath($this->theme['name']).'/';

			$files = $this->filesystem->allFiles($path); // get all files in theme folder

			$return = array();

			foreach ($files as $file)
			{
				$basename = basename($file);

				// skip basenames starting with '.', and non *.sub.blade.php or *.sub.php
				if (substr($basename, 0, 1) != '.' &&
					(substr($basename, -14) == '.sub.blade.php' || substr($basename, -8) == '.sub.php'))
				{				
					$view = str_replace('/', '.', // replace / with .
									str_replace(['.blade.php', '.php'], '', // strip extension
										str_replace($path, '', $file) // abs->rel to theme dir
									)
								);

					$view = substr($view, 0, -4); // remove .sub

					// make display name (i.e. pages.about turns to Pages > About)
					$display = implode(' > ', array_map(function($part) {
						return ucfirst($part);
					}, explode('.', $view)));

					$return[$view] = $display;
				}
			}	

			return $return;
		}
	}

	/**
	 * Gets <select> array for theme sub-template (with default)
	 * 
	 * @return array
	 */
	public function getThemeSubsSelect()
	{
		$templates = ($this->theme) ? $this->getThemeSubs() : array();
		$templates = array_reverse($templates);
		$templates['none'] = '-- Default Template --';
		$templates = array_reverse($templates);

		return $templates;
	}

	/**
	 * Checks if view in loaded theme dir exists
	 * 
	 * @param  string  $view View (dot notation)
	 * @return boolean|void
	 */
	public function themeViewExists($view)
	{
		if ($this->theme)
			return $this->view->exists('vessel-theme::'.$view);
	}

	/**
	 * Sets theme element value
	 * 
	 * @param  mixed $name   Name of element ('content', 'title', etc) OR array of multiple associative 'name's and 'value's
	 * @param  mixed $value  Anything that can be cast to a string, OR a lambda/callback function (will receive args as-is (name, arg2, arg3, etc) from template)
	 * @return void
	 */
	public function setElement($name, $value = null)
	{
		if (is_array($name))
			foreach ($name as $subname => $subvalue) $this->setElement($subname, $subvalue); #recursion ftw
		elseif (is_string($name))
			$this->elements[$name] = $value;
	}

	/**
	 * Returns set theme element ('content', 'title', 'block', 'stylesheets', etc)
	 * 
	 * @param  string $name Element name
	 * @param  mixed        Any number of params to pass to callables/lambdas
	 * @return mixed        Element value, or null if not set (might be null anyways if lambda returns it)
	 */
	public function element($name)
	{
		// Check if this element's name has been set
		if (isset($this->elements[$name]) && $this->current_element !== $name)
		{
			$this->current_element = $name; // set current element to this (preventing recursion)
			
			if (is_callable($this->elements[$name])) // check if the value is callable
				return call_user_func_array($this->elements[$name], func_get_args());
			else // if not, then return it as-is
				return $this->elements[$name];
		}

		return null;
	}
}