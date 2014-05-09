<?php namespace Hokeo\Vessel\Test;

use \Mockery as m;

class TestBase extends \PHPUnit_Framework_TestCase {

	/**
	 * Tear down test (close mockery)
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		m::close();
	}

	public function setup()
	{
		require __DIR__.'/../src/helpers.php';
	}

	/**
	 * Create class with mocked dependencies
	 * 
	 * @param  string $class        Class name
	 * @param  array  $dependencies Array of dependencies to inject (name => class)
	 * @param  array  $methods      Array of callbacks to apply to each dependency, receives dependency class and must return it
	 * @param  mixed  $param_filter Callable to modify parameter array before it's sent to class constructor, must return params array
	 * @return object               Class with mocked dependencies
	 */
	public function newClass($class, array $dependencies = array(), array $methods = array(), $param_filter = null)
	{
		$params = array();

		foreach ($dependencies as $name => $dep)
			$params[$name] = m::mock($dep);

		foreach ($methods as $name => $method)
			$params[$name] = call_user_func($method, $params[$name]);

		if (is_callable($param_filter))
			$params = call_user_func($param_filter, $params);

		$reflection = new \ReflectionClass($class);
		return $reflection->newInstanceArgs($params);
	}
}