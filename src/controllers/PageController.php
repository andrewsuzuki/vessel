<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment as View;

class PageController extends Controller
{
	protected $view;

	protected $pagehelper;

	protected $formatter;

	public function __construct(View $view, PageHelper $pagehelper, Formatter $formatter)
	{
		$this->view       = $view;
		$this->pagehelper = $pagehelper;
		$this->formatter  = $formatter;
	}

	public function getPages()
	{
		$pages = Page::with('user')->get();
		$this->view->share('title', 'Pages');
		return $this->view->make('vessel::pages')->with(compact('pages'));
	}

	public function getPagesNew()
	{
		$mode = 'new';
		$page = new Page;
		$this->view->share('title', 'New Page');
		$this->pagehelper->setPageFormatter($page);
		$editor = $this->formatter->formatter()->getEditorHtml();
		return $this->view->make('vessel::pages_edit')->with(compact('page', 'mode', 'editor'));
	}

	public function postPagesNew()
	{
		$page = new Page;
		return $this->pagehelper->savePage($page, 'new');
	}

	public function getPagesEdit($id)
	{
		$mode = 'edit';
		$page = Page::find($id); // find page
		if (!$page)
		{
			throw new \VesselNotFoundException;
		}
		$this->view->share('title', 'Edit '.$page->title); // set view title
		var_dump($this->formatter->is_set());
		$this->pagehelper->setPageFormatter($page);
		var_dump($this->formatter->is_set());
		exit;
		$content = $this->pagehelper->getContent($page->id, true);
		$editor = $this->formatter->formatter()->getEditorHtml($content); // get editor html

		return $this->view->make('vessel::pages_edit')->with(compact('page', 'mode', 'editor'));
	}

	public function postPagesEdit($id)
	{
		$page = Page::findOrFail($id);
		return $this->pagehelper->savePage($page, 'edit');
	}
}