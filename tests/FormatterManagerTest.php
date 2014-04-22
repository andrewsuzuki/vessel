<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\FormatterManager;

class FormatterManagerTest extends TestBase {

	public function setup()
	{
		
	}

	public function newFm(array $methods = array())
	{
		return $this->newClass('\\Hokeo\\Vessel\\FormatterManager', array(
				'app'    => 'Illuminate\Foundation\Application',
				'blade'  => 'Illuminate\View\Compilers\BladeCompiler',
				'plugin' => 'Hokeo\Vessel\Plugin',
			), $methods);
	}

	public function testConstructAndRegisterAndGetRegistered()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$this->assertFalse($fm->register('Hokeo\\Vessel\\Formatter\\Plain')); // already registered
		$this->assertFalse($fm->register('Non\\Existing\\Class')); // non existing

		$this->assertEquals(array(
			'Hokeo\\Vessel\\Formatter\\Plain'    => array('name' => 'Plain', 'for' => array('page', 'block'), 'class' => 'Hokeo\\Vessel\\Formatter\\Plain'),
			'Hokeo\\Vessel\\Formatter\\Html'     => array('name' => 'Html', 'for' => array('page', 'block'), 'class' => 'Hokeo\\Vessel\\Formatter\\Html'),
			'Hokeo\\Vessel\\Formatter\\Markdown' => array('name' => 'Markdown', 'for' => array('page', 'block'), 'class' => 'Hokeo\\Vessel\\Formatter\\Markdown'),
		), $fm->getRegistered());
	}

	public function testFilterFor()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$this->assertEquals(array(
			'Hokeo\\Vessel\\Formatter\\Plain'    => array('name' => 'Plain', 'for' => array('page', 'block'), 'class' => 'Hokeo\\Vessel\\Formatter\\Plain'),
			'Hokeo\\Vessel\\Formatter\\Html'     => array('name' => 'Html', 'for' => array('page', 'block'), 'class' => 'Hokeo\\Vessel\\Formatter\\Html'),
			'Hokeo\\Vessel\\Formatter\\Markdown' => array('name' => 'Markdown', 'for' => array('page', 'block'), 'class' => 'Hokeo\\Vessel\\Formatter\\Markdown'),
		), $fm->filterFor('block'));
	}

	public function testFilterForSelect()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$this->assertEquals(array(
			'Hokeo\\Vessel\\Formatter\\Plain'    => 'Plain',
			'Hokeo\\Vessel\\Formatter\\Html'     => 'Html',
			'Hokeo\\Vessel\\Formatter\\Markdown' => 'Markdown',
		), $fm->filterForSelect('block'));
	}

	public function testRegistered()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$this->assertTrue($fm->registered('Hokeo\\Vessel\\Formatter\\Plain'));
		$this->assertTrue($fm->registered('Hokeo\\Vessel\\Formatter\\Html'));
		$this->assertTrue($fm->registered('Hokeo\\Vessel\\Formatter\\Markdown'));
		$this->assertFalse($fm->registered('Hokeo\\Vessel\\Formatter\\NonExistingFormatter'));
		$this->assertFalse($fm->registered('Foo\\Bar\\NonExistingFormatter'));
		$this->assertFalse($fm->registered(function() {}));
	}

	public function testGet()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(6)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown,
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$this->assertInstanceOf('Hokeo\\Vessel\\Formatter\\Plain', $fm->get('Hokeo\\Vessel\\Formatter\\Plain'));
		$this->assertInstanceOf('Hokeo\\Vessel\\Formatter\\Html', $fm->get('Hokeo\\Vessel\\Formatter\\Html', 'page'));
		$this->assertInstanceOf('Hokeo\\Vessel\\Formatter\\Markdown', $fm->get('Hokeo\\Vessel\\Formatter\\Markdown', 'block'));
	}

	/**
	 * @expectedException		 Exception
	 * @expectedExceptionMessage Formatter is not registered or does not exist.
	 */
	public function testGetThrowsExceptionIfDoesNotExist()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$fm->get('Hokeo\\Vessel\\Formatter\\NonExistingFormatter');
	}

	/**
	 * @expectedException		 Exception
	 * @expectedExceptionMessage Formatter is not of the correct type.
	 */
	public function testGetThrowsExceptionIfNotSpecifiedType()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$fm->get('Hokeo\\Vessel\\Formatter\\Plain', 'foobartype');
	}

	public function testTryEach()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(5)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown,
					new \Hokeo\Vessel\Formatter\Markdown,
					new \Hokeo\Vessel\Formatter\Plain
					);
				return $app;
			},
		));

		$this->assertInstanceOf('Hokeo\\Vessel\\Formatter\\Markdown', $fm->tryEach(
			'Hokeo\\Vessel\\Formatter\\NonExistingFormatter',
			'Hokeo\\Vessel\\Formatter\\Markdown',
			'Hokeo\\Vessel\\Formatter\\Html')
		);

		$this->assertInstanceOf('Hokeo\\Vessel\\Formatter\\Plain', $fm->tryEach(
			'Hokeo\\Vessel\\Formatter\\NonExistingFormatter',
			'Hokeo\\Vessel\\Formatter\\NonExistingFormatterAnother',
			'Chinpokomon\\Shoe')
		);
	}

	public function testPhpEntities()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
		));

		$this->assertEquals('&lt;?php die("Foobar"); ?&gt;', $fm->phpEntities('<?php die("Foobar"); ?>'));
		$this->assertEquals('&lt;?php die("Foobar"); ?&gt; <html></html> &lt;? echo 5+3; ?&gt;', $fm->phpEntities('<?php die("Foobar"); ?> <html></html> <? echo 5+3; ?>'));
		$this->assertEquals('&lt;?=$foo ?&gt;', $fm->phpEntities('<?=$foo ?>'));
	}

	public function testCompileBlade()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\Vessel\Formatter\Plain,
					new \Hokeo\Vessel\Formatter\Html,
					new \Hokeo\Vessel\Formatter\Markdown
					);
				return $app;
			},
			'blade' => function($blade) {
				$blade->shouldReceive('compileString')->times(2)->andReturn(
					'<?php echo $foo; ?>',
					'<?php echo $__env->yieldContent(\'templates\'); ?>'
					);
				return $blade;
			}
		));

		$this->assertEquals('<?php echo $foo; ?>', $fm->compileBlade('{{ $foo }}'));
		$this->assertEquals('<?php echo $__env->yieldContent(\'templates\'); ?>', $fm->compileBlade('@yield(\'templates\')'));
	}
}