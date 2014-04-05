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

	protected function authenticate($username = 'andrew')
    {
    	$user = User::where('username', $username)->first();

		$this->be($user);
    }

	public function pageHelper()
	{
		return $this->app->make('Hokeo\\Vessel\\PageHelper');
	}

	public function testSaveNewPage()
	{
		$pagehelper = $this->pageHelper();

		$this->authenticate();

		$page_fields = array(
			'title' => 'Test Save New Page',
			'formatter' => 'Hokeo\\Vessel\\Formatter\\Plain',
			'content' => 'This is the test save new page content.',
			'slug' => 'test-save-new-page',
			'parent' => 'none',
			'nest_url' => '1',
			'description' => 'Descrip of test save new page.',
			'visible' => '1',
			'in_menu' => '1',
			'template' => 'none',
		);

		$this->app->make('request')->merge($page_fields);

		$response = $pagehelper->savepage(new \Hokeo\Vessel\Page, 'new', false);

		$this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

		$page = \Hokeo\Vessel\Page::where('slug', $page_fields['slug'])->first();

		$this->assertNotNull($page);

		if ($page)
		{
			$this->assertEquals($page_fields['title'], $page->title);
			$this->assertEquals($page_fields['formatter'], $page->formatter);
			$this->assertEquals($page_fields['content'], $page->raw);
			$this->assertEquals($page_fields['slug'], $page->slug);
			$this->assertEquals(null, $page->parent);
			$this->assertEquals($page_fields['nest_url'], $page->nest_url);
			$this->assertEquals($page_fields['description'], $page->description);
			$this->assertEquals($page_fields['visible'], $page->visible);
			$this->assertEquals($page_fields['in_menu'], $page->in_menu);
			$this->assertEquals($page_fields['template'], $page->template);
		}
	}
}