<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment as View;

class PageController extends Controller
{
	protected $view;

	protected $pagehelper;

	protected $formatter;

	protected $theme;

	public function __construct(View $view, PageHelper $pagehelper, Formatter $formatter, Theme $theme)
	{
		$this->view       = $view;
		$this->pagehelper = $pagehelper;
		$this->formatter  = $formatter;
		$this->theme      = $theme;
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

		$this->theme->load();
		$sub_templates = $this->theme->getThemeViewsSelect();

		return $this->view->make('vessel::pages_edit')->with(compact('page', 'mode', 'editor', 'sub_templates'));
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
		if (!$page) throw new \VesselNotFoundException; // throw error if not found

		$this->view->share('title', 'Edit '.$page->title); // set view title
		$this->pagehelper->setPageFormatter($page); // set formatter according to page setting (editor)

		$content       = $this->pagehelper->getContent($page->id, true); // grab page content from file
		$editor        = $this->formatter->formatter()->getEditorHtml($content); // get editor html

		$this->theme->load();
		$sub_templates = $this->theme->getThemeViewsSelect();

		return $this->view->make('vessel::pages_edit')->with(compact('page', 'mode', 'editor', 'sub_templates'));
	}

	public function postPagesEdit($id)
	{
		$page = Page::findOrFail($id);
		return $this->pagehelper->savePage($page, 'edit');
	}
}