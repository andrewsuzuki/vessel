<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\Plugin;

class PluginManagerTest extends TestBase {

	public $plugin;

	public $path;

	public function setup()
	{
		$this->path = '/path/to/plugins';
	}

	public function newPluginManager(array $methods = array())
	{
		return $this->newClass('\\Hokeo\\Vessel\\PluginManager', array(
				'app'         => 'Illuminate\Foundation\Application',
				'filesystem'  => 'Illuminate\Filesystem\Filesystem',
				'perm'        => 'Andrewsuzuki\Perm\Perm',
			), $methods);
	}

	public function testGetBasePath()
	{
		$p = $this->newPluginManager();

		$this->assertEquals(base_path().'/plugins', $p->getBasePath());
	}

	public function testGetAvailable()
	{
		$p = $this->newPluginManager(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('directories')->times(3)->andReturn(
					array($this->path.'/vendor1', $this->path.'/vendor2'),
					array($this->path.'/vendor1/plugin1', $this->path.'/vendor1/plugin2'),
					array($this->path.'/vendor2/plugin3')
					);
				$fs->shouldReceive('exists')->times(3)->andReturn(false, true, true);
				return $fs;
			}
		));

		$this->assertEquals(array(
			'Vendor1\\Plugin2\\' => 'vendor1/plugin2',
			'Vendor2\\Plugin3\\' => 'vendor2/plugin3',
		), $p->getAvailable());
	}
}