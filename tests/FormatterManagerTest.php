<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\FormatterManager;

class FormatterManagerTest extends TestBase {


	public function setup()
	{
		parent::setup();

		require_once VESSEL_DIR_SRC.'/nativeplugins/Hokeo/PlainFormatter/Formatter.php';
		require_once VESSEL_DIR_SRC.'/nativeplugins/Hokeo/HtmlFormatter/Formatter.php';
		require_once VESSEL_DIR_SRC.'/nativeplugins/Hokeo/MarkdownFormatter/Formatter.php';
	}

	public function newFm(array $methods = array())
	{		
		return $this->newClass('\\Hokeo\\Vessel\\FormatterManager', array(
				'app'    => 'Illuminate\Foundation\Application'
			), $methods);
	}

	public function testRegisterAndGetRegisteredAndRegistered()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter
					);
				return $app;
			},
		));

		$this->assertTrue($fm->register('Hokeo\\PlainFormatter\\Formatter'));
		$this->assertTrue($fm->register('Hokeo\\HtmlFormatter\\Formatter'));
		$this->assertTrue($fm->register('Hokeo\\MarkdownFormatter\\Formatter'));
		$this->assertFalse($fm->register('Hokeo\\PlainFormatter\\Formatter')); // already registered
		$this->assertFalse($fm->register('Non\\Existing\\Class')); // non existing

		$this->assertEquals(array(
			'Hokeo\\PlainFormatter\\Formatter'    => array('name' => 'Plain', 'for' => array('page', 'block'), 'class' => 'Hokeo\\PlainFormatter\\Formatter'),
			'Hokeo\\HtmlFormatter\\Formatter'     => array('name' => 'Html', 'for' => array('page', 'block'), 'class' => 'Hokeo\\HtmlFormatter\\Formatter'),
			'Hokeo\\MarkdownFormatter\\Formatter' => array('name' => 'Markdown', 'for' => array('page', 'block'), 'class' => 'Hokeo\\MarkdownFormatter\\Formatter'),
		), $fm->getRegistered());

		$this->assertTrue($fm->registered('Hokeo\\PlainFormatter\\Formatter'));
		$this->assertTrue($fm->registered('Hokeo\\HtmlFormatter\\Formatter'));
		$this->assertTrue($fm->registered('Hokeo\\MarkdownFormatter\\Formatter'));
		$this->assertFalse($fm->registered('Hokeo\\NonExistingFormatter\\NotAFormatter'));
		$this->assertFalse($fm->registered('Foo\\Bar\\NonExistingFormatter'));
		$this->assertFalse($fm->registered(true));
	}

	public function testFilterFor()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter
					);
				return $app;
			},
		));

		$fm->register('Hokeo\\PlainFormatter\\Formatter');
		$fm->register('Hokeo\\HtmlFormatter\\Formatter');
		$fm->register('Hokeo\\MarkdownFormatter\\Formatter');

		$this->assertEquals(array(
			'Hokeo\\PlainFormatter\\Formatter'    => array('name' => 'Plain', 'for' => array('page', 'block'), 'class' => 'Hokeo\\PlainFormatter\\Formatter'),
			'Hokeo\\HtmlFormatter\\Formatter'     => array('name' => 'Html', 'for' => array('page', 'block'), 'class' => 'Hokeo\\HtmlFormatter\\Formatter'),
			'Hokeo\\MarkdownFormatter\\Formatter' => array('name' => 'Markdown', 'for' => array('page', 'block'), 'class' => 'Hokeo\\MarkdownFormatter\\Formatter'),
		), $fm->filterFor('block'));
	}

	public function testFilterForSelect()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter
					);
				return $app;
			},
		));

		$fm->register('Hokeo\\PlainFormatter\\Formatter');
		$fm->register('Hokeo\\HtmlFormatter\\Formatter');
		$fm->register('Hokeo\\MarkdownFormatter\\Formatter');

		$this->assertEquals(array(
			'Hokeo\\PlainFormatter\\Formatter'    => 'Plain',
			'Hokeo\\HtmlFormatter\\Formatter'     => 'Html',
			'Hokeo\\MarkdownFormatter\\Formatter' => 'Markdown',
		), $fm->filterForSelect('block'));
	}

	public function testGet()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(6)->andReturn(
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter,
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter
					);
				return $app;
			},
		));

		$fm->register('Hokeo\\PlainFormatter\\Formatter');
		$fm->register('Hokeo\\HtmlFormatter\\Formatter');
		$fm->register('Hokeo\\MarkdownFormatter\\Formatter');

		$this->assertInstanceOf('Hokeo\\PlainFormatter\\Formatter', $fm->get('Hokeo\\PlainFormatter\\Formatter'));
		$this->assertInstanceOf('Hokeo\\HtmlFormatter\\Formatter', $fm->get('Hokeo\\HtmlFormatter\\Formatter', 'page'));
		$this->assertInstanceOf('Hokeo\\MarkdownFormatter\\Formatter', $fm->get('Hokeo\\MarkdownFormatter\\Formatter', 'block'));
	}

	/**
	 * @expectedException		 Exception
	 * @expectedExceptionMessage messages.formatters.does-not-exist-error
	 */
	public function testGetThrowsExceptionIfDoesNotExist()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter
					);
				return $app;
			},
		));

		$fm->register('Hokeo\\PlainFormatter\\Formatter');
		$fm->register('Hokeo\\HtmlFormatter\\Formatter');
		$fm->register('Hokeo\\MarkdownFormatter\\Formatter');

		$fm->get('Hokeo\\Vessel\\Formatter\\NonExistingFormatter');
	}

	/**
	 * @expectedException		 Exception
	 * @expectedExceptionMessage messages.formatters.not-correct-type-error
	 */
	public function testGetThrowsExceptionIfNotSpecifiedType()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(3)->andReturn(
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter
					);
				return $app;
			},
		));

		$fm->register('Hokeo\\PlainFormatter\\Formatter');
		$fm->register('Hokeo\\HtmlFormatter\\Formatter');
		$fm->register('Hokeo\\MarkdownFormatter\\Formatter');

		$fm->get('Hokeo\\PlainFormatter\\Formatter', 'notatype');
	}

	public function testTryEach()
	{
		$fm = $this->newFm(array(
			'app' => function($app) {
				$app->shouldReceive('bind')->times(3);
				$app->shouldReceive('make')->times(5)->andReturn(
					new \Hokeo\PlainFormatter\Formatter,
					new \Hokeo\HtmlFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter,
					new \Hokeo\MarkdownFormatter\Formatter,
					new \Hokeo\PlainFormatter\Formatter
					);
				return $app;
			},
		));

		$fm->register('Hokeo\\PlainFormatter\\Formatter');
		$fm->register('Hokeo\\HtmlFormatter\\Formatter');
		$fm->register('Hokeo\\MarkdownFormatter\\Formatter');

		$this->assertInstanceOf('Hokeo\\MarkdownFormatter\\Formatter', $fm->tryEach(
			'Hokeo\\NonExistingFormatter\\NotAFormatter',
			'Hokeo\\MarkdownFormatter\\Formatter',
			'Hokeo\\HtmlFormatter\\Formatter')
		);

		$this->assertInstanceOf('Hokeo\\PlainFormatter\\Formatter', $fm->tryEach(
			'Hokeo\\NonExistingFormatter\\NotAFormatter',
			'Hokeo\\NonExistingFormatter\\NotAFormatterDos',
			'Chinpokomon\\Shoe')
		);
	}

	public function testPhpEntities()
	{
		$fm = $this->newFm();

		$this->assertEquals('&lt;?php die("Foobar"); ?&gt;', $fm->phpEntities('<?php die("Foobar"); ?>'));
		$this->assertEquals('&lt;?php die("Foobar"); ?&gt; <html></html> &lt;? echo 5+3; ?&gt;', $fm->phpEntities('<?php die("Foobar"); ?> <html></html> <? echo 5+3; ?>'));
		$this->assertEquals('&lt;?=$foo ?&gt;', $fm->phpEntities('<?=$foo ?>'));
	}
}