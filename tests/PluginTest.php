<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\Plugin;

class PluginTest extends TestBase {

	public $plugin;

	public $path;

	public $plugin1info;

	public $plugin2info;

	public $plugin3info;

	public function setup()
	{
		$this->path = '/path/to/plugins';

		$this->plugin1info = array(
			'name'      => 'vendor1/plugin1',
			'pluggable' => 'Vendor1\\Plugin1\\Plugin1',
			'title'     => 'Plugin 1',
			'author'    => 'Vendor 1',
		);

		$this->plugin2info = array(
			'name'      => 'vendor1/plugin2',
			'pluggable' => 'Vendor1\\Plugin2\\Plugin2',
			'title'     => 'Plugin 2',
			'author'    => 'Vendor 1',
		);

		$this->plugin3info = array(
			'name'      => 'vendor2/plugin3',
			'pluggable' => 'Vendor2\\Plugin3\\Plugin3',
			'title'     => 'Plugin 3',
			'author'    => 'Vendor 2',
		);
	}

	public function newPlugin(array $methods = array())
	{
		return $this->newClass('\\Hokeo\\Vessel\\Plugin', array(
				'app'         => 'Illuminate\Foundation\Application',
				'config'      => 'Illuminate\Config\Repository',
				'classloader' => 'Illuminate\Support\ClassLoader',
				'filesystem'  => 'Illuminate\Filesystem\Filesystem',
				'perm'        => 'Andrewsuzuki\Perm\Perm',
			), $methods);
	}

	public function testGetBasePath()
	{
		$p = $this->newPlugin();

		$this->assertEquals(base_path().'/plugins', $p->getBasePath());
	}

	public function testGetAvailable()
	{
		$p = $this->newPlugin(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('directories')->times(3)->andReturn(
					array($this->path.'/vendor1', $this->path.'/vendor2'),
					array($this->path.'/vendor1/plugin1', $this->path.'/vendor1/plugin2'),
					array($this->path.'/vendor2/plugin3')
					);
				$fs->shouldReceive('exists')->times(3)->andReturn(false, true, true);
				$fs->shouldReceive('getRequire')->times(2)->andReturn($this->plugin2info, $this->plugin3info);
				return $fs;
			},
			'classloader' => function($cl) {
				$cl->shouldReceive('addDirectories');
				$cl->shouldReceive('load');
				return $cl;
			},
			'perm' => function($p) {
				$p->shouldReceive('load');
				$p->shouldReceive('set');
				$p->shouldReceive('save');
				return $p;
			},
		));

		$this->assertEquals(array(
			'vendor1/plugin2' => $this->plugin2info,
			'vendor2/plugin3' => $this->plugin3info,
		), $p->getAvailable());
	}

	public function testHook()
	{	
		$p = $this->newPlugin();

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
		$p = $this->newPlugin();

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
		$p = $this->newPlugin();

		$p->hook('test.hook.name', array('foo', 'bar'));
	}

	public function testAllHooks()
	{
		$p = $this->newPlugin();
		$callback = function() {
			return 'test';
		};
		$p->hook('test.hook.name', $callback);
		$this->assertEquals(array('test.hook.name' => array(array('callback' => $callback, 'priority' => 0, 'n' => 0))), $p->allHooks());
	}

	public function testHookIsSet()
	{
		$p = $this->newPlugin();

		$p->hook('test.hook.name', function() {
			return 'test';
		});

		$this->assertTrue($p->hookIsSet('test.hook.name'));
		$this->assertFalse($p->hookIsSet('test.hook.another'));
	}

	public function testSortHook()
	{
		$p = $this->newPlugin();
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
		$p = $this->newPlugin();

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