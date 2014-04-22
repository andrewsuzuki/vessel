<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\Theme;

class ThemeTest extends TestBase {

	public $themes_path;

	public $themeinfo1;

	public function setup()
	{
		$this->themes_path = base_path().'/themes';

		$this->themeinfo1 = array(
			'title' => 'Foobar',
			'author' => 'Andrew',
			'authorUrl' => 'http://andrewsuzuki.com'
			);
	}

	public function newTheme(array $methods = array())
	{
		return $this->newClass('\\Hokeo\\Vessel\\Theme', array(
				'app'        => 'Illuminate\Foundation\Application',
				'config'     => 'Illuminate\Config\Repository',
				'view'       => 'Illuminate\View\Environment',
				'filesystem' => 'Illuminate\Filesystem\Filesystem',
				'perm'       => 'Andrewsuzuki\Perm\Perm',
			), $methods);
	}

	public function testGetBasePath()
	{
		$t = $this->newTheme();

		$this->assertEquals($this->themes_path, $t->getBasePath());
	}

	public function testGetThemePath()
	{
		$t = $this->newTheme(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->times(2)->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/non_existing')->once()->andReturn(false);
				return $fs;
			}
			));

		$this->assertEquals($this->themes_path.'/foobar', $t->getThemePath('foobar'));
		$this->assertEquals($this->themes_path.'/foobar', $t->getThemePath($this->themes_path.'/foobar'));
		$this->assertNull($t->getThemePath('non_existing'));
	}

	public function testGet()
	{
		$t = $this->newTheme(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->times(4)->andReturn(true, false, true, true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/template.blade.php')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/theme.php')->times(3)->andReturn(true, false, true);
				$fs->shouldReceive('getRequire')->with($this->themes_path.'/foobar/theme.php')->times(2)->andReturn($this->themeinfo1, array('title' => 'Foobar'));
				return $fs;
			}
			));

		$this->assertEquals($this->themeinfo1, $t->get('foobar'));
		$this->assertFalse($t->get('foobar'));
		$this->assertFalse($t->get('foobar'));
		$this->assertFalse($t->get('foobar'));
	}

	public function testLoad()
	{
		$t = $this->newTheme(array(
			'perm' => function($p) {
				$p->shouldReceive('load->get')->with('theme')->times(4)->andReturn('foobar');
				return $p;
			},
			'filesystem' => function($fs) {
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->times(6)->andReturn(true, false, true, true, true, false);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/template.blade.php')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/theme.php')->times(4)->andReturn(true, false, true, true);
				$fs->shouldReceive('getRequire')->with($this->themes_path.'/foobar/theme.php')->times(3)->andReturn($this->themeinfo1, array('title' => 'Foobar'), $this->themeinfo1);
				return $fs;
			},
			'view' => function($v) {
				$v->shouldReceive('addNamespace')->with('vessel-theme', $this->themes_path.'/foobar');
				return $v;
			}
			));

		$this->assertTrue($t->load());
		$this->assertFalse($t->load());
		$this->assertFalse($t->load());
		$this->assertFalse($t->load());

		$this->assertTrue($t->load('foobar'));
		$this->assertFalse($t->load('foobar'));
	}

	public function testLoadWithFallbackToFirstAvailableTheme()
	{
		$t = $this->newTheme(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->times(6)->andReturn(false, true, true, false, false, true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/template.blade.php')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/theme.php')->times(3)->andReturn(true, true, false);
				$fs->shouldReceive('getRequire')->with($this->themes_path.'/foobar/theme.php')->times(2)->andReturn($this->themeinfo1, $this->themeinfo1);
				$fs->shouldReceive('directories')->with($this->themes_path)->times(3)->andReturn(array($this->themes_path.'/foobar'), array(), array($this->themes_path.'/foobar'));
				return $fs;
			},
			'view' => function($v) {
				$v->shouldReceive('addNamespace')->with('vessel-theme', $this->themes_path.'/foobar');
				return $v;
			}
			));

		$this->assertTrue($t->load('foobar', true));
		$this->assertFalse($t->load('foobar', true)); // no available fallback themes
		$this->assertFalse($t->load('foobar', true)); // invalid single fallback available theme
	}

	public function testGetAvailable()
	{
		$t = $this->newTheme(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('directories')->with($this->themes_path)->times(3)->andReturn(array(), array($this->themes_path.'/foobar'));
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->times(2)->andReturn(true, false);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/template.blade.php')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/theme.php')->times(1)->andReturn(true);
				$fs->shouldReceive('getRequire')->with($this->themes_path.'/foobar/theme.php')->times(1)->andReturn($this->themeinfo1);
				return $fs;
			},
			));

		$this->assertEquals(array(), $t->getAvailable());
		$this->assertEquals(array('foobar' => $this->themeinfo1), $t->getAvailable());
		$this->assertEquals(array(), $t->getAvailable());
	}

	public function testGetThemeSubs()
	{	
		$t = $this->newTheme(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/template.blade.php')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/theme.php')->andReturn(true);
				$fs->shouldReceive('getRequire')->with($this->themes_path.'/foobar/theme.php')->andReturn($this->themeinfo1);

				$fs->shouldReceive('allFiles')->with($this->themes_path.'/foobar/')->once()->andReturn(array(
					$this->themes_path.'/foobar/.DS_STORE',
					$this->themes_path.'/foobar/about.sub.blade.php',
					$this->themes_path.'/foobar/aboutme.php',
					$this->themes_path.'/foobar/.',
					$this->themes_path.'/foobar/..',
					$this->themes_path.'/foobar/profile/andrew.sub.php'
					));
				return $fs;
			},
			'view' => function($v) {
				$v->shouldReceive('addNamespace')->with('vessel-theme', $this->themes_path.'/foobar');
				return $v;
			}
			));
	
		$t->load('foobar');

		$this->assertEquals(array('about' => 'About', 'profile.andrew' => 'Profile > Andrew'), $t->getThemeSubs());
	}

	public function testGetThemeSubsSelect()
	{	
		$t = $this->newTheme(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/template.blade.php')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/theme.php')->andReturn(true);
				$fs->shouldReceive('getRequire')->with($this->themes_path.'/foobar/theme.php')->andReturn($this->themeinfo1);

				$fs->shouldReceive('allFiles')->with($this->themes_path.'/foobar/')->once()->andReturn(array(
					$this->themes_path.'/foobar/.DS_STORE',
					$this->themes_path.'/foobar/about.sub.blade.php',
					$this->themes_path.'/foobar/aboutme.php',
					$this->themes_path.'/foobar/.',
					$this->themes_path.'/foobar/..',
					$this->themes_path.'/foobar/profile/andrew.sub.php'
					));
				return $fs;
			},
			'view' => function($v) {
				$v->shouldReceive('addNamespace')->with('vessel-theme', $this->themes_path.'/foobar');
				return $v;
			}
			));

		$this->assertEquals(array('none' => '-- Default Template --'), $t->getThemeSubsSelect());
	
		$t->load('foobar');

		$this->assertEquals(array('none' => '-- Default Template --', 'about' => 'About', 'profile.andrew' => 'Profile > Andrew'), $t->getThemeSubsSelect());
	}

	public function testThemeViewExists()
	{	
		$t = $this->newTheme(array(
			'filesystem' => function($fs) {
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/template.blade.php')->andReturn(true);
				$fs->shouldReceive('exists')->with($this->themes_path.'/foobar/theme.php')->andReturn(true);
				$fs->shouldReceive('getRequire')->with($this->themes_path.'/foobar/theme.php')->andReturn($this->themeinfo1);

				return $fs;
			},
			'view' => function($v) {
				$v->shouldReceive('addNamespace')->with('vessel-theme', $this->themes_path.'/foobar');

				$v->shouldReceive('exists')->with('vessel-theme::foobar')->andReturn(true);
				$v->shouldReceive('exists')->with('vessel-theme::non_existing')->andReturn(false);

				return $v;
			}
			));
	
		$t->load('foobar');

		$this->assertTrue($t->themeViewExists('foobar'));
		$this->assertFalse($t->themeViewExists('non_existing'));
	}

	public function testElementAndSetElement()
	{	
		$t = $this->newTheme();
	
		$t->setElement(1, 'this will not work');
		$t->setElement('test1', 'test1_value');
		$t->setElement('test2', 1);
		$t->setElement(array(
			'test3' => 3.14,
			'test4' => true
			));
		$t->setElement('test5', array('foo' => 'bar'));

		$t->setElement(array(
			'test6' => function() {
				return 3 + 5;
			},
			'test7' => function($call, $n) {
				return $n + 5;
			}
			));

		$usetestvar = 1;

		$t->setElement('test8', function($call, $n) use ($usetestvar) {
			return $n + $usetestvar + 5;
		});

		$this->assertNull($t->element('non_existing'));
		$this->assertNull($t->element(1));
		$this->assertEquals('test1_value', $t->element('test1'));
		$this->assertEquals(1,             $t->element('test2'));
		$this->assertEquals(3.14,          $t->element('test3'));
		$this->assertEquals(true,          $t->element('test4'));
		$this->assertEquals(array('foo' => 'bar'), $t->element('test5'));
		$this->assertEquals(8,             $t->element('test6'));
		$this->assertEquals(9,             $t->element('test7', 4));
		$this->assertEquals(10,            $t->element('test8', 4));
	}
}