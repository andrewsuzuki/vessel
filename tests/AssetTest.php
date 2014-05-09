<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\Asset;

class AssetTest extends TestBase {

	public function newAsset(array $methods = array())
	{		
		return $this->newClass('\\Hokeo\\Vessel\\Asset', array(
				'files' => 'Illuminate\Filesystem\Filesystem',
				'url'   => 'Illuminate\Routing\UrlGenerator',
		), $methods, function ($params) {
			$params[] = 'base/path/to/assets';
			return $params;
		});
	}

	public function testAddJs()
	{
		$a = $this->newAsset();

		$this->assertTrue($a->js('test.js', 'test'));
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('js'));

		$this->assertTrue($a->js('test2.js', 'test2', 'test2'));
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.js', 'namespace' => 'ad0234829205b9033196ba818f7a872b', 'common_name' => 'test2', 'conditional' => ''),
		), $a->getAdded('js'));

		$this->assertTrue($a->js('test3.js', 'test3', 'test3', 'lt IE 8'));
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.js', 'namespace' => 'ad0234829205b9033196ba818f7a872b', 'common_name' => 'test2', 'conditional' => ''),
			array('filename' => 'test3.js', 'namespace' => '8ad8757baa8564dc136c1e07507f4a98', 'common_name' => 'test3', 'conditional' => 'lt IE 8'),
		), $a->getAdded('js'));
	}

	public function testAddCss()
	{
		$a = $this->newAsset();

		$this->assertTrue($a->css('test.css', 'test'));
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('css'));

		$this->assertTrue($a->css('test2.css', 'test2', 'test2'));
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.css', 'namespace' => 'ad0234829205b9033196ba818f7a872b', 'common_name' => 'test2', 'conditional' => ''),
		), $a->getAdded('css'));

		$this->assertTrue($a->css('test3.css', 'test3', 'test3', 'lt IE 8'));
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.css', 'namespace' => 'ad0234829205b9033196ba818f7a872b', 'common_name' => 'test2', 'conditional' => ''),
			array('filename' => 'test3.css', 'namespace' => '8ad8757baa8564dc136c1e07507f4a98', 'common_name' => 'test3', 'conditional' => 'lt IE 8'),
		), $a->getAdded('css'));
	}

	public function testAddother()
	{
		$a = $this->newAsset();

		$this->assertTrue($a->other('test.png', 'test'));
		$this->assertEquals(array(
			array('filename' => 'test.png', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('other'));

		$this->assertTrue($a->other('test2.gif', 'test2'));
		$this->assertEquals(array(
			array('filename' => 'test.png', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.gif', 'namespace' => 'ad0234829205b9033196ba818f7a872b', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('other'));

		$this->assertTrue($a->other('test3.jpg', 'test3'));
		$this->assertEquals(array(
			array('filename' => 'test.png', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.gif', 'namespace' => 'ad0234829205b9033196ba818f7a872b', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test3.jpg', 'namespace' => '8ad8757baa8564dc136c1e07507f4a98', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('other'));
	}

	public function testAdd()
	{
		$a = $this->newAsset();

		$this->assertTrue($a->add('test.js', 'test', 'js'));
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('js'));

		$this->assertTrue($a->add('test2.js', 'test', 'js', 'com'));
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => 'com', 'conditional' => ''),
		), $a->getAdded('js'));

		$this->assertTrue($a->add('test3.js', 'test3', 'js', null, 'lt IE 8'));
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => 'com', 'conditional' => ''),
			array('filename' => 'test3.js', 'namespace' => '8ad8757baa8564dc136c1e07507f4a98', 'common_name' => null, 'conditional' => 'lt IE 8'),
		), $a->getAdded('js'));

		$this->assertFalse($a->add('test4.js', 'test4', 'js', 'com'));
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => 'com', 'conditional' => ''),
			array('filename' => 'test3.js', 'namespace' => '8ad8757baa8564dc136c1e07507f4a98', 'common_name' => null, 'conditional' => 'lt IE 8'),
		), $a->getAdded('js'));

		$this->assertTrue($a->add('test.css', 'test', 'css'));
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('css'));

		$this->assertTrue($a->add('test2.css', 'test', 'css', 'com'));
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => 'com', 'conditional' => ''),
		), $a->getAdded('css'));

		$this->assertTrue($a->add('test3.css', 'test3', 'css', null, 'lt IE 8'));
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => 'com', 'conditional' => ''),
			array('filename' => 'test3.css', 'namespace' => '8ad8757baa8564dc136c1e07507f4a98', 'common_name' => null, 'conditional' => 'lt IE 8'),
		), $a->getAdded('css'));

		$this->assertFalse($a->add('test4.css', 'test4', 'css', 'com'));
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test2.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => 'com', 'conditional' => ''),
			array('filename' => 'test3.css', 'namespace' => '8ad8757baa8564dc136c1e07507f4a98', 'common_name' => null, 'conditional' => 'lt IE 8'),
		), $a->getAdded('css'));

		$this->assertTrue($a->add('test.png', 'test', 'other'));
		$this->assertEquals(array(
			array('filename' => 'test.png', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('other'));

		$this->assertTrue($a->add('test.png', 'test'));
		$this->assertEquals(array(
			array('filename' => 'test.png', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
			array('filename' => 'test.png', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('will_revert_to_other'));
	}

	public function testGetAdded()
	{
		$a = $this->newAsset();

		$a->add('test.js', 'test', 'js');
		$this->assertEquals(array(
			array('filename' => 'test.js', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('js'));

		$a->add('test.css', 'test', 'css');
		$this->assertEquals(array(
			array('filename' => 'test.css', 'namespace' => '098f6bcd4621d373cade4e832627b4f6', 'common_name' => null, 'conditional' => ''),
		), $a->getAdded('css'));

		$this->assertEquals(array(), $a->getAdded('non_existing_type'));
	}

	public function testCommonAssetAdded()
	{
		$a = $this->newAsset();

		$this->assertFalse($a->commonAssetAdded('non_existing_type', 'foo'));
		$this->assertFalse($a->commonAssetAdded('js', 'ctest2'));
		$a->js('test2.js', 'test2', 'ctest2');
		$this->assertTrue($a->commonAssetAdded('js', 'ctest2'));
	}

	public function testMake()
	{
		$a = $this->newAsset(array(
			'url' => function($url) {
				$url->shouldReceive('to')->with('/assets/098f6bcd4621d373cade4e832627b4f6/test.css')->times(3)->andReturn('good');
				$url->shouldReceive('to')->with('/assets/ad0234829205b9033196ba818f7a872b/test2.css')->times(2)->andReturn('good2');
				$url->shouldReceive('to')->with('/assets/8ad8757baa8564dc136c1e07507f4a98/test3.css')->times(1)->andReturn('good3');

				$url->shouldReceive('to')->with('/assets/098f6bcd4621d373cade4e832627b4f6/test.js')->times(3)->andReturn('good');
				$url->shouldReceive('to')->with('/assets/ad0234829205b9033196ba818f7a872b/test2.js')->times(2)->andReturn('good2');
				$url->shouldReceive('to')->with('/assets/8ad8757baa8564dc136c1e07507f4a98/test3.js')->times(1)->andReturn('good3');
				return $url;
			}
		));

		$a->add('test.css', 'test', 'css');
		$this->assertEquals("\n\n<!-- Begin assets-css -->\n\n".
			'<link rel="stylesheet" type="text/css" href="good">'."\n".
		"\n<!-- End assets-css -->\n\n", $a->make('css'));

		$a->add('test2.css', 'test2', 'css');
		$this->assertEquals("\n\n<!-- Begin assets-css -->\n\n".
			'<link rel="stylesheet" type="text/css" href="good">'."\n".
			'<link rel="stylesheet" type="text/css" href="good2">'."\n".
		"\n<!-- End assets-css -->\n\n", $a->make('css'));

		$a->add('test3.css', 'test3', 'css', null, 'lt IE 8');
		$this->assertEquals("\n\n<!-- Begin assets-css -->\n\n".
			'<link rel="stylesheet" type="text/css" href="good">'."\n".
			'<link rel="stylesheet" type="text/css" href="good2">'."\n".
			'<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="good3"><![endif]-->'."\n".
		"\n<!-- End assets-css -->\n\n", $a->make('css'));

		$a->add('test.js', 'test', 'js');
		$this->assertEquals("\n\n<!-- Begin assets-js -->\n\n".
			'<script type="text/javascript" src="good"></script>'."\n".
		"\n<!-- End assets-js -->\n\n", $a->make('js'));

		$a->add('test2.js', 'test2', 'js');
		$this->assertEquals("\n\n<!-- Begin assets-js -->\n\n".
			'<script type="text/javascript" src="good"></script>'."\n".
			'<script type="text/javascript" src="good2"></script>'."\n".
		"\n<!-- End assets-js -->\n\n", $a->make('js'));

		$a->add('test3.js', 'test3', 'js', null, 'lt IE 8');
		$this->assertEquals("\n\n<!-- Begin assets-js -->\n\n".
			'<script type="text/javascript" src="good"></script>'."\n".
			'<script type="text/javascript" src="good2"></script>'."\n".
			'<!--[if lt IE 8]><script type="text/javascript" src="good3"></script><![endif]-->'."\n".
		"\n<!-- End assets-js -->\n\n", $a->make('js'));
	}
}