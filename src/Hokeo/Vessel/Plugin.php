<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\Support\ClassLoader;
use Illuminate\Filesystem\Filesystem;
use Hokeo\Vessel\Setting;

class Plugin {

	protected $app;
	
	protected $config;

	protected $classloader;

	protected $filesystem;

	protected $setting;

	protected $available = null;

	protected $enabled;

	protected $plugins = array();

	protected $hooks = array();

	public function __construct(Application $app, Repository $config, ClassLoader $classloader, Filesystem $filesystem, Setting $setting)
	{
		$this->app          = $app;
		$this->config       = $config;
		$this->classloader  = $classloader;
		$this->filesystem   = $filesystem;
		$this->setting      = $setting;
		$this->plugins_path = base_path().'/plugins';
		$this->filesystem   = $filesystem;
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

					// include plugin info file
					$info = @include $plugin.DIRECTORY_SEPARATOR.'plugin.php';

					// validate plugin info
					if (is_array($info) &&
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
						if (class_exists($info['pluggable']) && get_parent_class($info['pluggable']) == 'Hokeo\\Vessel\\Pluggable')
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
			$this->setting->set('plugins.available', $available);
		return $available;
	}

	/**
	 * Enables all available plugins
	 */
	public function enableAll()
	{
		// Get available plugins
		if (is_null($this->available))
		{
			try
			{
				$this->available = $this->setting->get('plugins.available');
			}
			catch (\Exception $e)
			{
				$this->getAvailable(true);
			}

			if (!is_array($this->available)) return;
		}

		foreach ($this->available as $plugin)
		{
			$this->enable($plugin['name']);
		}
	}

	/**
	 * Enables an available plugin.
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
	 * Sort hooks by priority
	 * 
	 * @param  string $hook Name of hook
	 */
	public function sortHook($hook)
	{
		if ($this->hookIsSet($hook))
		{
			uasort($this->hooks[$hook], array($this, 'comparePriority'));
		}
	}

	/**
	 * Compares priority of two priorities (simple int comparison for uasort())
	 * 
	 * @param  int $a
	 * @param  int $b
	 * @return int -1|0|1
	 */
	protected function comparePriority($a, $b)
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

	/**
	 * Determines if hook has been added yet
	 * 
	 * @param  string $hook Name of hook
	 * @return boolean
	 */
	public function hookIsSet($hook)
	{
		return isset($this->hooks[$hook]);
	}

	/**
	 * Creates hook
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
	 * Fires hook
	 * 
	 * @param  string  $hook      Name of hook
	 * @param  array   $data      Array of data to pass to hook callback
	 * @param  boolean $is_filter If this is a filter (data will be cascaded from one hook to the next, then returned)
	 * @return array|string       Filtered data array if is_filter, or string of string hook returns if !is_filter (action)
	 */
	public function fire($hook, $data = array(), $is_filter = false)
	{
		$data_count = count($data); // for later verification of filter response

		$action_strings = array();

		// validate fire
		if ($this->hookIsSet($hook) && is_array($data) && is_bool($is_filter))
		{
			$this->sortHook($hook); // sort hooks by priority

			foreach ($this->hooks[$hook] as $hook) // interate our hooks
			{
				// call hook callback, feed it data
				$response = call_user_func_array($hook['callback'], $data);

				// if this is a filter, make sure the new data is the same for the next filter
				if ($is_filter && is_array($response) && $data_count == count($response))
				{
					$data = $response;
				}
				elseif (!$is_filter && is_string($response))
				{
					$action_strings[] = $response;
				}
			}

		}

		if ($is_filter)
			return $data;
		else
			return implode('', $action_strings);
	}
}