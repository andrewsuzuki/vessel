<?php namespace Hokeo\Vessel\Test;

use Hokeo\Vessel\Asset;

class AssetTest extends \PHPUnit_Framework_TestCase {

	public function setup()
	{
		
	}

	public function testConstruct()
	{
		$a = new Asset;

		$this->assertEquals(array(), $a->get('css'));
		$this->assertEquals(array(), $a->get('js'));
	}

	public function testAddJs()
	{
		$a = new Asset;

		$this->assertTrue($a->js('test.js', 'test'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.js', 'if' => ''),
		), $a->get('js'));

		$this->assertTrue($a->js('test2.js', 'test2', 'lt IE 8'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.js', 'if' => ''),
			'test2' => array('source' => 'test2.js', 'if' => 'lt IE 8'),
		), $a->get('js'));

		$this->assertFalse($a->js('test2_new.js', 'test2', 'lt IE 6'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.js', 'if' => ''),
			'test2' => array('source' => 'test2.js', 'if' => 'lt IE 8'),
		), $a->get('js'));
	}

	public function testAddCss()
	{
		$a = new Asset;

		$this->assertTrue($a->css('test.css', 'test'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.css', 'if' => ''),
		), $a->get('css'));

		$this->assertTrue($a->css('test2.css', 'test2', 'lt IE 8'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.css', 'if' => ''),
			'test2' => array('source' => 'test2.css', 'if' => 'lt IE 8'),
		), $a->get('css'));

		$this->assertFalse($a->css('test2_new.css', 'test2', 'lt IE 6'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.css', 'if' => ''),
			'test2' => array('source' => 'test2.css', 'if' => 'lt IE 8'),
		), $a->get('css'));
	}

	public function testAdd()
	{
		$a = new Asset;

		$this->assertTrue($a->add('css', 'test.css', 'test'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.css', 'if' => ''),
		), $a->get('css'));

		$this->assertTrue($a->add('css', 'test2.css', 'test2', 'lt IE 8'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.css', 'if' => ''),
			'test2' => array('source' => 'test2.css', 'if' => 'lt IE 8'),
		), $a->get('css'));

		$this->assertFalse($a->add('css', 'test2_new.css', 'test2', 'lt IE 6'));
		
		$this->assertTrue($a->add('js', 'test.js', 'test'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.js', 'if' => ''),
		), $a->get('js'));

		$this->assertTrue($a->add('js', 'test2.js', 'test2', 'lt IE 8'));
		$this->assertEquals(array(
			'test' => array('source' => 'test.js', 'if' => ''),
			'test2' => array('source' => 'test2.js', 'if' => 'lt IE 8'),
		), $a->get('js'));

		$this->assertFalse($a->add('js', 'test2_new.js', 'test2', 'lt IE 6'));

		$this->assertEquals(array(
			'test' => array('source' => 'test.css', 'if' => ''),
			'test2' => array('source' => 'test2.css', 'if' => 'lt IE 8'),
		), $a->get('css'));

		$this->assertEquals(array(
			'test' => array('source' => 'test.js', 'if' => ''),
			'test2' => array('source' => 'test2.js', 'if' => 'lt IE 8'),
		), $a->get('js'));
	}

	public function testGet()
	{
		$a = new Asset;

		$a->add('css', 'test.css', 'test');
		$this->assertEquals(array(
			'test' => array('source' => 'test.css', 'if' => ''),
		), $a->get('css'));

		$a->add('js', 'test.js', 'test');
		$this->assertEquals(array(
			'test' => array('source' => 'test.js', 'if' => ''),
		), $a->get('js'));

		$this->assertEquals(null, $a->get('non_existing_type'));
	}

	public function testMake()
	{
		$a = new Asset;

		$a->css('test.css', 'test');
		$this->assertEquals("\n\n<!-- Begin assets-css -->\n\n".
			'<link rel="stylesheet" type="text/css" href="test.css">'."\n".
		"\n<!-- End assets-css -->\n\n", $a->make('css'));

		$a->css('test2.css', 'test2');
		$this->assertEquals("\n\n<!-- Begin assets-css -->\n\n".
			'<link rel="stylesheet" type="text/css" href="test.css">'."\n".
			'<link rel="stylesheet" type="text/css" href="test2.css">'."\n".
		"\n<!-- End assets-css -->\n\n", $a->make('css'));

		$a->css('test3.css', 'test3', 'lt IE 8');
		$this->assertEquals("\n\n<!-- Begin assets-css -->\n\n".
			'<link rel="stylesheet" type="text/css" href="test.css">'."\n".
			'<link rel="stylesheet" type="text/css" href="test2.css">'."\n".
			'<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="test3.css"><![endif]-->'."\n".
		"\n<!-- End assets-css -->\n\n", $a->make('css'));

		$a->js('test.js', 'test');
		$this->assertEquals("\n\n<!-- Begin assets-js -->\n\n".
			'<script type="text/javascript" src="test.js"></script>'."\n".
		"\n<!-- End assets-js -->\n\n", $a->make('js'));

		$a->js('test2.js', 'test2');
		$this->assertEquals("\n\n<!-- Begin assets-js -->\n\n".
			'<script type="text/javascript" src="test.js"></script>'."\n".
			'<script type="text/javascript" src="test2.js"></script>'."\n".
		"\n<!-- End assets-js -->\n\n", $a->make('js'));

		$a->js('test3.js', 'test3', 'lt IE 8');
		$this->assertEquals("\n\n<!-- Begin assets-js -->\n\n".
			'<script type="text/javascript" src="test.js"></script>'."\n".
			'<script type="text/javascript" src="test2.js"></script>'."\n".
			'<!--[if lt IE 8]><script type="text/javascript" src="test3.js"></script><![endif]-->'."\n".
		"\n<!-- End assets-js -->\n\n", $a->make('js'));
	}
}