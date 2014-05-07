<?php namespace Hokeo\Vessel;

class Hooker {

	protected $hooks;

	/**
	 * Hooker constructor
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->hooks = array();
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
			throw new \Exception(t('messages.plugins.hook-not-valid-error', array('name' => $hook)));
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