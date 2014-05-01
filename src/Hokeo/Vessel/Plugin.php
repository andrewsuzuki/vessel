<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\Support\ClassLoader;
use Illuminate\Filesystem\Filesystem;
use Andrewsuzuki\Perm\Perm;

class Plugin {

	protected $app;
	
	protected $config;

	protected $classloader;

	protected $filesystem;

	protected $perm;

	protected $plugins_path;

	protected $available;

	protected $plugins;

	protected $hooks;

	public function __construct(
		Application $app,
		Repository $config,
		ClassLoader $classloader,
		Filesystem $filesystem,
		Perm $perm)
	{
		$this->app          = $app;
		$this->config       = $config;
		$this->classloader  = $classloader;
		$this->filesystem   = $filesystem;
		$this->perm         = $perm;
		$this->filesystem   = $filesystem;

		$this->plugins_path = base_path().'/plugins';
		$this->available    = null;
		$this->plugins      = array();
		$this->hooks        = array();
	}

	/**
	 * Returns base plugins directory path
	 * 
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->plugins_path;
	}

	/**
	 * Get available plugins and validate
	 * 
	 * @return array
	 */
	public function getAvailable($save = false)
	{
		$vendors = $this->filesystem->directories($this->plugins_path);

		$available = array();

		foreach ($vendors as $vendor)
		{
			$plugins = $this->filesystem->directories($vendor);

			foreach ($plugins as $plugin)
			{
				if ($this->filesystem->exists($plugin.DIRECTORY_SEPARATOR.'plugin.php'))
				{
					$this->classloader->addDirectories(array($this->plugins_path));

					// try to include plugin info file
					try
					{
						$info = $this->filesystem->getRequire($plugin.DIRECTORY_SEPARATOR.'plugin.php');
					}
					catch (\Exception $e)
					{

					}

					// validate plugin info
					if ($info && is_array($info) &&
						isset($info['name']) && strlen($info['name']) &&
						isset($info['pluggable']) && strlen($info['pluggable']) &&
						isset($info['title']) && strlen($info['title']) &&
						isset($info['author']) && strlen($info['author']) &&
						$info['name'] == basename(dirname($plugin)).'/'.basename($plugin)
						)
					{
						// load pluggable (plugin)
						$this->classloader->load($info['pluggable']);

						// validate pluggable
						if (defined('V_TEST_NOW') || (class_exists($info['pluggable']) && get_parent_class($info['pluggable']) == 'Hokeo\\Vessel\\Pluggable'))
						{
							// add to available
							$available[$info['name']] = $info;
						}
					}
				}
			}
		}

		$this->available = $available;
		if ($save)
			$this->perm->load('vessel.plugins')->set('available', $available)->save();
		return $available;
	}

	/**
	 * Enable all available plugins
	 */
	public function enableAll()
	{
		// Get available plugins
		if (is_null($this->available))
		{
			try
			{
				$this->available = $this->perm->load('vessel.plugins')->get('available');
				if (!$this->available) throw new \Exception;
			}
			catch (\Exception $e)
			{
				$this->getAvailable(true);
			}
		}

		foreach ($this->available as $plugin)
		{
			$this->enable($plugin['name']);
		}
	}

	/**
	 * Enable an available plugin.
	 * 
	 * @param  string $name Name of plugin (plugin directory)
	 * @return boolean      If plugin was enabled
	 */
	public function enable($name)
	{
		if (isset($this->available[$name]))
		{
			// autoload plugins
			$this->classloader->addDirectories(array($this->plugins_path));

			// register service provider if it's good
			if (class_exists($this->available[$name]['pluggable']) && get_parent_class($this->available[$name]['pluggable']) == 'Hokeo\\Vessel\\Pluggable')
			{
				$this->app->register($this->available[$name]['pluggable']);
			}
		}
	}

	/**
	 * Create hook
	 * 
	 * @param  string   $hook     Name of hook
	 * @param  callable $callback Callback function or method (use standard php array(class, method))
	 * @param  integer  $priority Priority of hook; not guaranteed if other plugins are installed (higher is sooner)
	 */
	public function hook($hook, $callback, $priority = 0)
	{
		// validate hook
		if (is_string($hook) && is_callable($callback))
		{
			// check that priority is an integer
			$priority = is_int($priority) ? $priority : 0;
			// make blank array for this hook if it doesn't exist
			if (!$this->hookIsSet($hook)) $this->hooks[$hook] = array();
			// add hook
			$this->hooks[$hook][] = array('callback' => $callback, 'priority' => $priority, 'n' => count($this->hooks[$hook]));
		}
		else
		{
			throw new \Exception('Hook not valid.');
		}
	}

	/**
	 * Retrieve all added hooks
	 * 
	 * @return array Hooks (name => array(callback, priority, number added))
	 */
	public function allHooks()
	{
		return $this->hooks;
	}

	/**
	 * Determine if a hook with specified name has been added yet
	 * 
	 * @param  string $hook Name of hook
	 * @return boolean
	 */
	public function hookIsSet($hook)
	{
		return isset($this->hooks[$hook]);
	}

	/**
	 * Sort hooks by priority
	 * 
	 * @param  string $name Name of hook
	 */
	public function sortHook($name)
	{
		if ($this->hookIsSet($name))
		{
			usort($this->hooks[$name], array($this, 'comparePriority'));
		}
	}

	/**
	 * Fire hook
	 * 
	 * @param  string       $hook        Name of hook
	 * @param  mixed        $data        Array of data to pass to hook callback, or non-arrays will automatically be inserted into empty array
	 * @param  boolean      $is_filter   If this is a filter (then data will be cascaded from one hook to the next, then returned)
	 * @param  integer|bool $return_only If it's a filter, an integer is given, and that index exists in the returned data, fire() will return only that data value, or bool true will return all data
	 * @return array|string              Filtered data array if is_filter, or string of string hook returns if !is_filter (action)
	 */
	public function fire($hook, $data = array(), $is_filter = false, $return_only = 0)
	{
		$data_count = count($data); // for later verification of filter response

		$action_strings = array();

		if (!is_array($data)) $data = array($data); // force data array if it isnt

		// validate fire
		if ($this->hookIsSet($hook))
		{
			$this->sortHook($hook); // sort hooks by priority

			foreach ($this->hooks[$hook] as $hook) // loop our ordered hooks
			{
				// call hook callback, feed it data
				$response = call_user_func_array($hook['callback'], $data);

				// if this is a filter, make sure the new data has the same # of elements (for the next filter)
				if ($is_filter && is_array($response) && $data_count == count($response))
					$data = $response;
				elseif (!$is_filter && is_string($response))
					$action_strings[] = $response;
			}
		}

		if ($is_filter)
			return (is_int($return_only) && isset($data[$return_only])) ? $data[$return_only] : $data;
		else
			return implode('', $action_strings);
	}

	/**
	 * Compare two plugins by priority (simple int comparison for use with usort())
	 * 
	 * @param  array  $a
	 * @param  arrray $b
	 * @return int    -1|0|1
	 */
	protected function comparePriority(array $a, array $b)
	{
		if ($a['priority'] == $b['priority'])
		{
			// if they're the same priority, revert to order added (['n'])
			if ($a['n'] < $b['n']) return -1;
			return 1;
		}
		if ($a['priority'] > $b['priority']) return -1;
		return 1;
	}
}