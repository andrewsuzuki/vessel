<?php namespace Hokeo\Vessel;

use Mockery as m;

class PageHelperTest extends \Illuminate\Foundation\Testing\TestCase {

	protected $mock;

	public function createApplication()
	{
		$unitTesting = true;
		$testEnvironment = 'testing';
		return require __DIR__.'/../../../../bootstrap/start.php';
	}

	public function setUp()
	{
		parent::setUp();
		$this->app->register('Hokeo\\Vessel\\VesselServiceProvider');
	}

	public function tearDown()
	{
	}

	public function pageHelper()
	{
		return $this->app->make('Hokeo\\Vessel\\PageHelper');
	}

	public function testSetPageFormatterWithInput()
	{
		$pagehelper = $this->pageHelper();

		$this->app->make('request')->merge(array('formatter' => 'Markdown'));

		$this->assertEquals(1, $pagehelper->setPageFormatter(null));
	}

	public function testMakeContent()
	{
		$pagehelper = $this->pageHelper();

		$this->assertEquals('<p><em>test</em></p>', trim($pagehelper->makeContent('Markdown', '*test*')));
	}

}