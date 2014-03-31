<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Krucas\Notification\Notification;

class PageController extends Controller
{
	protected $view;

	protected $input;

	protected $redirect;

	protected $pagehelper;

	protected $formatter;

	protected $theme;

	protected $notification;

	public function __construct(
		Environment $view,
		Request $input,
		Redirector $redirect,
		PageHelper $pagehelper,
		Formatter $formatter,
		Theme $theme,
		Notification $notification)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->redirect     = $redirect;
		$this->pagehelper   = $pagehelper;
		$this->formatter    = $formatter;
		$this->theme        = $theme;
		$this->notification = $notification;
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
		$page = Page::with('history')->find($id); // find page
		if (!$page) throw new \VesselNotFoundException; // throw error if not found

		$edits  = $page->history()->notDraft()->orderBy('edit', 'desc')->get();
		$drafts = $page->history()->draft()->orderBy('edit', 'desc')->get();

		$pagehistory = null;

		// if ?history is specified and the id exists, then replace page fields with the pagehistory's
		
		if ($this->input->has('history'))
		{
			if ($pagehistory = $page->history()->find($this->input->get('history')))
			{
				$page->title       = $pagehistory->title;
				$page->slug        = $pagehistory->slug;
				$page->description = $pagehistory->description;
				$page->formatter   = $pagehistory->formatter;
				$page->template    = $pagehistory->template;
				$page->nest_url    = $pagehistory->nest_url;
				$page->visible     = $pagehistory->visible;
				$page->in_menu     = $pagehistory->in_menu;
			}
			else
			{
				throw new \VesselNotFoundException;
			}
		}

		$this->view->share('title', 'Edit '.$page->title.(($pagehistory) ? ' <span class="label label-pagehistory label-'.(($pagehistory->is_draft) ? 'primary">Draft ' : 'info">Edit ').$pagehistory->edit.'</span>' : '')); // set view title
		$this->pagehelper->setPageFormatter($page); // set formatter according to page setting (editor)

		$content = ($pagehistory) ? $pagehistory->content : $this->pagehelper->getContent($page->id, true);

		$editor  = $this->formatter->formatter()->getEditorHtml($content); // get editor html

		$this->theme->load();
		$sub_templates = $this->theme->getThemeViewsSelect();

		return $this->view->make('vessel::pages_edit')->with(compact('page', 'pagehistory', 'edits', 'drafts', 'mode', 'editor', 'sub_templates'));
	}

	public function postPagesEdit($id)
	{
		$page = Page::findOrFail($id);
		$is_draft = $this->input->has('save_as_draft');
		return $this->pagehelper->savePage($page, 'edit', $is_draft);
	}

	public function getPagesDelete($id)
	{
		$page = Page::find($id);

		if ($page)
		{
			$page->delete();
			$this->notification->success('Page was deleted successfully.');
			return $this->redirect->route('vessel.pages');
		}

		throw new \VesselNotFoundException;
	}

	public function getPageHistoryDelete($id)
	{
		$pagehistory = Pagehistory::find($id);

		if ($pagehistory)
		{
			$page = $pagehistory->page;

			if ($pagehistory->is_draft)
				$this->notification->success('Draft was deleted successfully.');
			else
				$this->notification->success('Edit was deleted successfully.');

			$pagehistory->delete();

			return $this->redirect->route('vessel.pages.edit', array('id' => $page->id));
		}
		
		throw new \VesselNotFoundException;
	}


	public function getPageHistoryDeleteAll($id)
	{
		$page = Page::find($id);

		if ($page)
		{
			$type = $this->input->get('type');

			if ($type == 'edits' || $type == 'drafts')
			{
				$scope = ($type == 'drafts') ? 'draft' : 'notDraft';

				$page->history()->$scope()->delete();

				return $this->redirect->route('vessel.pages.edit', array('id' => $page->id));
			}
		}

		throw new \VesselNotFoundException;
	}
}