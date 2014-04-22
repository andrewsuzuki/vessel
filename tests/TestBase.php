<?php namespace Hokeo\Vessel\Test;

use \Mockery as m;

class TestBase extends \PHPUnit_Framework_TestCase {

	protected function tearDown()
	{
		m::close();
	}

	/**
	 * Create class with mocked dependencies
	 * 
	 * @param  string $class        Class name
	 * @param  array  $dependencies Array of dependencies to inject (name => class)
	 * @param  array  $methods      Array of callbacks to apply to each dependency, receives dependency class and must return it
	 * @return object               Class with mocked dependencies
	 */
	public function newClass($class, array $dependencies = array(), array $methods = array())
	{
		// $params = array();

		// foreach ($dependencies as $name => $dep)
		// 	$params[$name] = $this->getMockBuilder($dep)->disableOriginalConstructor()->getMock();

		// foreach ($methods as $name => $method)
		// 	$params[$name] = call_user_func($method, $params[$name]);

		// $reflection = new \ReflectionClass($class);
		// return $reflection->newInstanceArgs($params);
		
		$params = array();

		foreach ($dependencies as $name => $dep)
			$params[$name] = m::mock($dep);

		foreach ($methods as $name => $method)
			$params[$name] = call_user_func($method, $params[$name]);

		$reflection = new \ReflectionClass($class);
		return $reflection->newInstanceArgs($params);
	}
}