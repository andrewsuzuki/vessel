<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\Asset;

class AssetTest extends TestBase {

	protected $base_path = '/base/path/to/assets';

	public function newAsset(array $methods = array())
	{		
		return $this->newClass('\\Hokeo\\Vessel\\Asset', array(
				'file'  => 'Illuminate\Filesystem\Filesystem',
				'url'   => 'Illuminate\Routing\UrlGenerator',
		), $methods, function ($params) {
			$params[] = $this->base_path;
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

	public function testPublish()
	{
		$a = $this->newAsset(array(
			'file' => function($file) {
				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(1)->andReturn(false);
				$file->shouldReceive('isFile')->with('/copy/this/test.css')->times(1)->andReturn(true);
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(true);
				$file->shouldReceive('copy')->with('/copy/this/test.css', $this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(1)->andReturn(true);

				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(1)->andReturn(false);
				$file->shouldReceive('isFile')->with('/copy/this/test')->times(1)->andReturn(false);
				$file->shouldReceive('isDirectory')->with('/copy/this/test')->times(1)->andReturn(true);
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(true);
				$file->shouldReceive('copyDirectory')->with('/copy/this/test', $this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(1)->andReturn(true);

				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(1)->andReturn(false);
				$file->shouldReceive('isFile')->with('/copy/this/test.css')->times(1)->andReturn(true);
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(true);
				$file->shouldReceive('copy')->with('/copy/this/test.css', $this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(1)->andReturn(false);

				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(1)->andReturn(false);
				$file->shouldReceive('isFile')->with('/copy/this/test')->times(1)->andReturn(false);
				$file->shouldReceive('isDirectory')->with('/copy/this/test')->times(1)->andReturn(true);
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(true);
				$file->shouldReceive('copyDirectory')->with('/copy/this/test', $this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(1)->andReturn(false);

				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(1)->andReturn(false);
				$file->shouldReceive('isFile')->with('/copy/this/test')->times(1)->andReturn(false);
				$file->shouldReceive('isDirectory')->with('/copy/this/test')->times(1)->andReturn(false);

				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(1)->andReturn(true);

				return $file;
			}
		));

		$this->assertTrue($a->publish('/copy/this/test.css', 'Hokeo/Vessel'));
		$this->assertTrue($a->publish('/copy/this/test', 'Hokeo/Vessel'));
		$this->assertFalse($a->publish('/copy/this/test.css', 'Hokeo/Vessel'));
		$this->assertFalse($a->publish('/copy/this/test', 'Hokeo/Vessel'));
		$this->assertFalse($a->publish('/copy/this/test', 'Hokeo/Vessel'));
		$this->assertFalse($a->publish('/copy/this/test.css', 'Hokeo/Vessel'));
	}

	public function testUnpublish()
	{
		$a = $this->newAsset(array(
			'file' => function($file) {
				$file->shouldReceive('isFile')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(4)->andReturn(true);
				$file->shouldReceive('delete')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(3)->andReturn(true);
				$file->shouldReceive('delete')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(1)->andReturn(false);

				$file->shouldReceive('isFile')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(4)->andReturn(false);
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(4)->andReturn(true);
				$file->shouldReceive('deleteDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(3)->andReturn(true);
				$file->shouldReceive('deleteDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test')->times(1)->andReturn(false);

				$file->shouldReceive('isFile')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/does_not_exist')->times(1)->andReturn(false);
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/does_not_exist')->times(1)->andReturn(false);

				return $file;
			}
		));

		$this->assertTrue($a->unpublish('test.css', 'Hokeo/Vessel'));
		$this->assertTrue($a->unpublish('foo/bar/test.css', 'Hokeo/Vessel'));
		$this->assertTrue($a->unpublish('/foo/bar/test.css', 'Hokeo/Vessel'));
		$this->assertFalse($a->unpublish('test.css', 'Hokeo/Vessel'));

		$this->assertTrue($a->unpublish('test', 'Hokeo/Vessel'));
		$this->assertTrue($a->unpublish('foo/bar/test', 'Hokeo/Vessel'));
		$this->assertTrue($a->unpublish('/foo/bar/test', 'Hokeo/Vessel'));
		$this->assertFalse($a->unpublish('test', 'Hokeo/Vessel'));

		$this->assertFalse($a->unpublish('does_not_exist', 'Hokeo/Vessel'));
	}

	public function testIsPublished()
	{
		$a = $this->newAsset(array(
			'file' => function($file) {
				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test.css')->times(1)->andReturn(true);
				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test2.css')->times(1)->andReturn(true);
				$file->shouldReceive('exists')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379/test3.css')->times(1)->andReturn(true);
				$file->shouldReceive('exists')->with($this->base_path.'/098f6bcd4621d373cade4e832627b4f6/test.js')->times(1)->andReturn(false);
				return $file;
			}
		));

		$this->assertTrue($a->isPublished('test.css', 'Hokeo/Vessel'));
		$this->assertTrue($a->isPublished('foo/bar/test2.css', 'Hokeo/Vessel'));
		$this->assertTrue($a->isPublished('/foo/bar/test3.css', 'Hokeo/Vessel'));
		$this->assertFalse($a->isPublished('test.js', 'test'));
	}

	public function testCreateNamespace()
	{
		$a = $this->newAsset(array(
			'file' => function($file) {
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(true);

				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(false);
				$file->shouldReceive('makeDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379', 511, true, false)->times(1)->andReturn(true);

				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(false);
				$file->shouldReceive('makeDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379', 511, true, false)->times(1)->andReturn(false);
				return $file;
			}
		));

		$this->assertTrue($a->createNamespace('Hokeo/Vessel'));
		$this->assertTrue($a->createNamespace('Hokeo/Vessel'));
		$this->assertFalse($a->createNamespace('Hokeo/Vessel'));
	}

	public function testNamespaceExists()
	{
		$a = $this->newAsset(array(
			'file' => function($file) {
				$file->shouldReceive('isDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(true);
				$file->shouldReceive('isDirectory')->with($this->base_path.'/098f6bcd4621d373cade4e832627b4f6')->times(1)->andReturn(false);
				return $file;
			}
		));

		$this->assertTrue($a->namespaceExists('Hokeo/Vessel'));
		$this->assertFalse($a->namespaceExists('test'));
	}

	public function testDeleteNamespace()
	{
		$a = $this->newAsset(array(
			'file' => function($file) {
				$file->shouldReceive('deleteDirectory')->with($this->base_path.'/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn(true);
				$file->shouldReceive('deleteDirectory')->with($this->base_path.'/098f6bcd4621d373cade4e832627b4f6')->times(1)->andReturn(false);
				return $file;
			}
		));

		$this->assertTrue($a->deleteNamespace('Hokeo/Vessel'));
		$this->assertFalse($a->deleteNamespace('test'));
	}

	public function testGetDirFromNamespace()
	{
		$a = $this->newAsset();

		$this->assertEquals($this->base_path.'/098f6bcd4621d373cade4e832627b4f6', $a->getDirFromNamespace('test'));
		$this->assertEquals($this->base_path.'/2aa166bfa25bc9664c585069c804a379', $a->getDirFromNamespace('Hokeo/Vessel'));
		$this->assertEquals($this->base_path.'/2aa166bfa25bc9664c585069c804a379/file.js', $a->getDirFromNamespace('Hokeo/Vessel', 'file.js'));
		$this->assertEquals($this->base_path.'/2aa166bfa25bc9664c585069c804a379/file2.js', $a->getDirFromNamespace('Hokeo/Vessel', '/file2.js'));
		$this->assertEquals($this->base_path.'/2aa166bfa25bc9664c585069c804a379/path/to/file3.js', $a->getDirFromNamespace('Hokeo/Vessel', '/path/to/file3.js'));
		$this->assertEquals($this->base_path.'/2aa166bfa25bc9664c585069c804a379/file4.js', $a->getDirFromNamespace('2aa166bfa25bc9664c585069c804a379', 'file4.js', false));
		$this->assertEquals($this->base_path.'/2aa166bfa25bc9664c585069c804a379', $a->getDirFromNamespace('2aa166bfa25bc9664c585069c804a379', null, false));
	}

	public function testGetUrlFromNamespace()
	{
		$a = $this->newAsset(array(
			'url' => function($url) {
				$url->shouldReceive('to')->with('/assets/098f6bcd4621d373cade4e832627b4f6')->times(1)->andReturn('good');
				$url->shouldReceive('to')->with('/assets/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn('good2');
				$url->shouldReceive('to')->with('/assets/2aa166bfa25bc9664c585069c804a379/file.js')->times(1)->andReturn('good3');
				$url->shouldReceive('to')->with('/assets/2aa166bfa25bc9664c585069c804a379/file2.js')->times(1)->andReturn('good4');
				$url->shouldReceive('to')->with('/assets/2aa166bfa25bc9664c585069c804a379')->times(1)->andReturn('good5');
				return $url;
			}
		));

		$this->assertEquals('good', $a->getUrlFromNamespace('test'));
		$this->assertEquals('good2', $a->getUrlFromNamespace('Hokeo/Vessel'));
		$this->assertEquals('good3', $a->getUrlFromNamespace('Hokeo/Vessel', 'file.js'));
		$this->assertEquals('good4', $a->getUrlFromNamespace('2aa166bfa25bc9664c585069c804a379', 'file2.js', false));
		$this->assertEquals('good5', $a->getUrlFromNamespace('2aa166bfa25bc9664c585069c804a379', null, false));
	}

	public function testEncodeNamespace()
	{
		$a = $this->newAsset();

		$this->assertEquals('098f6bcd4621d373cade4e832627b4f6', $a->encodeNamespace('test'));
		$this->assertEquals('2aa166bfa25bc9664c585069c804a379', $a->encodeNamespace('Hokeo/Vessel'));
	}

}