<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\Plugin;

class HookerTest extends TestBase {

	public $plugin;

	public function setup()
	{

	}

	public function newHooker(array $methods = array())
	{
		return $this->newClass('\\Hokeo\\Vessel\\Hooker', array(), $methods);
	}

	public function testHook()
	{	
		$p = $this->newHooker();

		$p->hook('test.hook.name', function() {
			return 'test';
		});

		$this->assertEquals('test', $p->fire('test.hook.name'));

		$p->hook('test.hook.name', function() {
			return 'again';
		});

		$this->assertEquals('testagain', $p->fire('test.hook.name'));

		$p->hook('test.hook.name', function() {
			return 'before';
		}, 100);

		$this->assertEquals('beforetestagain', $p->fire('test.hook.name'));

		$p->hook('test.hook.another', function() {
			return 'hey';
		}, 10);

		$p->hook('test.hook.another', function() {
			return 'yo, ';
		}, 11);

		$this->assertEquals('yo, hey', $p->fire('test.hook.another'));
	}

	/**
	 * @expectedException		 Exception
	 * @expectedExceptionMessage messages.plugins.hook-not-valid-error
	 */
	public function testHookExceptionIfNotValidName()
	{
		$p = $this->newHooker();

		$p->hook(function() {}, function() {
			return 'test';
		});
	}

	/**
	 * @expectedException		 Exception
	 * @expectedExceptionMessage messages.plugins.hook-not-valid-error
	 */
	public function testHookExceptionIfNotValidCallback()
	{
		$p = $this->newHooker();

		$p->hook('test.hook.name', array('foo', 'bar'));
	}

	public function testAllHooks()
	{
		$p = $this->newHooker();
		$callback = function() {
			return 'test';
		};
		$p->hook('test.hook.name', $callback);
		$this->assertEquals(array('test.hook.name' => array(array('callback' => $callback, 'priority' => 0, 'n' => 0))), $p->allHooks());
	}

	public function testHookIsSet()
	{
		$p = $this->newHooker();

		$p->hook('test.hook.name', function() {
			return 'test';
		});

		$this->assertTrue($p->hookIsSet('test.hook.name'));
		$this->assertFalse($p->hookIsSet('test.hook.another'));
	}

	public function testSortHook()
	{
		$p = $this->newHooker();
		$callback = function() {
			return 'test';
		};
		$callback2 = function() {
			return 'test';
		};
		$p->hook('test.hook.name', $callback);
		$p->hook('test.hook.name', $callback2, 100);

		$p->sortHook('test.hook.name');

		$this->assertEquals(array('test.hook.name' => array(
			array('callback' => $callback2, 'priority' => 100, 'n' => 1),
			array('callback' => $callback, 'priority' => 0, 'n' => 0),
		)), $p->allHooks());
	}

	public function testFire()
	{
		$p = $this->newHooker();

		$p->hook('test.hook.name', function() {
			return 'test';
		});

		$this->assertEquals('test', $p->fire('test.hook.name'));

		$p->hook('test.hook.with.data', function($append) {
			return 'test'.$append;
		});

		$this->assertEquals('testing', $p->fire('test.hook.with.data', array('ing')));

		$p->hook('test.hook.BAD.filter', function($n) {
			return $n + 2;
		});

		$this->assertEquals(4, $p->fire('test.hook.BAD.filter', array(4), true));

		$p->hook('test.hook.filter', function($n) {
			return array($n + 2);
		});

		$this->assertEquals(6, $p->fire('test.hook.filter', array(4), true, 0));
		$this->assertEquals(array(6), $p->fire('test.hook.filter', 4, true, true));

		$p->hook('test.hook.filter', function($n) {
			return array($n * 3);
		});

		$this->assertEquals(18, $p->fire('test.hook.filter', 4, true));
		$this->assertEquals(array(18), $p->fire('test.hook.filter', array(4), true, true));
	}
}