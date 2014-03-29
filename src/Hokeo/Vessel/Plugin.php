<?php namespace Hokeo\Vessel;

use Illuminate\Support\ClassLoader;
use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Application as App;

class Plugin {

	protected $filesystem;

	protected $classloader;

	protected $config;

	protected $app;

	protected $available = null;

	protected $enabled;

	protected $plugins = array();

	protected $hooks = array();

	public function __construct(ClassLoader $classloader, Config $config, App $app)
	{
		$this->classloader = $classloader;
		$this->config      = $config;
		$this->app         = $app;
		$this->plugin_path = $this->config->get('vessel::vessel.plugin_path', app_path().'/plugins');
		$this->filesystem  = $this->app->make('Hokeo\Vessel\FilesystemInterface', array('path' => $this->plugin_path));
	}

	/**
	 * Returns base plugins directory path
	 * 
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->plugin_path;
	}

	/**
	 * Get available plugins and validate
	 * 
	 * @return array
	 */
	public function getAvailable()
	{
		$plugins = $this->filesystem->listContents('/');

		$available = array();

		foreach ($plugins as $plugin)
		{
			if ($plugin['type'] == 'dir')
			{
				if ($this->filesystem->has('/'.$plugin['basename'].'/plugin.json'))
				{
					try
					{
						$json = json_decode($this->filesystem->read('/'.$plugin['basename'].'/plugin.json'), true);
					}
					catch (\Exception $e)
					{
						break;
					}

					if (is_array($json) &&
						isset($json['name']) && strlen($json['name']) &&
						isset($json['class']) && strlen($json['class']) &&
						isset($json['title']) && strlen($json['title']) &&
						isset($json['author']) && strlen($json['author']) &&
						$json['name'] == $plugin['basename']
						)
					{
						$available[$json['name']] = $json;
					}
				}
			}
		}

		$this->available = $available;

		return $available;
	}

	/**
	 * Enables an available plugin.
	 * 
	 * @param  string $name Name of plugin (plugin directory)
	 * @return boolean      If plugin was enabled
	 */
	public function enable($name)
	{
		if (is_null($this->available)) $this->getAvailable(); // if available plugins haven't been retrieved, then get them

		if (isset($this->available[$name]))
		{
			$dir = $this->plugin_path.'/'.$name;
			// autoload this plugin's directory
			$this->classloader->addDirectories(array($dir));

			if (class_exists($this->available[$name]['class']))
			{
				// make sure plugin class extends Pluggable
				if (get_parent_class($this->available[$name]['class']) == 'Hokeo\Vessel\Pluggable')
				{
					// register service provider
					$this->app->register($this->available[$name]['class']);

					// add to enabled array
					$this->enabled[$name] = $this->available[$name];

					return true;
				}
			}

			// whoops, class didn't exist. remove autoloading directory
			$this->classloader->removeDirectories(array($dir));
		}

		return false;
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
	 * @param  string  $hook     Name of hook
	 * @param  callable  $callback Callback function or method (use standard php array(class, method))
	 * @param  integer $priority Priority of hook; not guaranteed if other plugins are installed
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