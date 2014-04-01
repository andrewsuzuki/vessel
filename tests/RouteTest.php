<?php

class RouteTest extends Illuminate\Foundation\Testing\TestCase {

	protected $vessel_route_prefix;

	public function setUp()
	{
		parent::setUp();

		$this->vessel_route_prefix = $this->app['config']->get('vessel::vessel.uri', 'vessel');
	}

	public function createApplication()
	{
		
	}

	protected function getPackageProviders()
	{
		return array('Hokeo\\Vessel\\VesselServiceProvider');
	}

	public function getRoutePath($path)
	{
		return '/'.$this->vessel_route_prefix.(($path == '/') ? '' : (($path[0] == '/') ? $path : '/'.$path));
	}

	public function testHome()
	{
		$crawler = $this->call('GET', $this->getRoutePath('/'));
		$this->assertResponseOk();
	}
}