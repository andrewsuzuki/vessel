<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Routing\Redirector;
use Krucas\Notification\Notification;

class PageController extends Controller
{
	protected $view;

	protected $input;

	protected $auth;

	protected $redirect;

	protected $pagehelper;

	protected $notification;

	protected $fm;

	protected $theme;

	protected $page;

	protected $pagehistory;

	public function __construct(
		Environment $view,
		Request $input,
		AuthManager $auth,
		Redirector $redirect,
		Notification $notification,
		PageHelper $pagehelper,
		FormatterManager $fm,
		Theme $theme,
		Page $page,
		Pagehistory $pagehistory)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->auth         = $auth;
		$this->redirect     = $redirect;
		$this->notification = $notification;
		$this->pagehelper   = $pagehelper;
		$this->fm           = $fm;
		$this->theme        = $theme;
		$this->page         = $page;
		$this->pagehistory  = $pagehistory;
	}

	public function getPages()
	{
		$pages = $this->page->with('user')->get();
		$this->view->share('title', 'Pages');
		return $this->view->make('vessel::pages')->with(compact('pages'));
	}

	public function getPagesNew()
	{
		$mode = 'new';
		$page = $this->page->newInstance();

		$this->view->share('title', 'New Page');

		$formatter = $this->fm->tryEach(
			$this->input->get('formatter'),
			$this->input->old('formatter'),
			$this->auth->user()->preferred_formatter
			);

		$interface = $formatter->fmInterface('', '');

		$formatter->fmSetup();

		$formatters_select_array = $this->fm->filterForSelect('page');
		$formatter_current = get_class($formatter);

		$this->theme->load();
		$sub_templates = $this->theme->getThemeSubsSelect();

		return $this->view->make('vessel::pages_edit')->with(compact(
			'page',
			'mode',
			'interface',
			'formatters_select_array',
			'formatter_current',
			'sub_templates'
			));
	}

	public function postPagesNew()
	{
		$page = $this->page->newInstance();
		return $this->pagehelper->savePage($page, 'new');
	}

	public function getPagesEdit($id)
	{
		$mode = 'edit';
		$page = $this->page->with('history')->find($id); // find page
		if (!$page) throw new \VesselBackNotFoundException; // throw error if not found

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
				throw new \VesselBackNotFoundException;
			}
		}

		$this->view->share('title', 'Edit '.$page->title.(($pagehistory) ? ' <span class="label label-pagehistory label-'.(($pagehistory->is_draft) ? 'primary">Draft ' : 'info">Edit ').$pagehistory->edit.'</span>' : '')); // set view title

		$formatter = $this->fm->tryEach(
			$this->input->get('formatter'),
			$this->input->old('formatter'),
			$page->formatter,
			$this->auth->user()->preferred_formatter
			);

		$raw = ($pagehistory) ? $pagehistory->raw : $page->raw;
		$made = ($pagehistory) ? $pagehistory->made : $page->made;

		$interface = $formatter->fmInterface($raw, $made);

		$formatter->fmSetup();

		$formatters_select_array = $this->fm->filterForSelect('page');
		$formatter_current = get_class($formatter);

		$this->theme->load();
		$sub_templates = $this->theme->getThemeSubsSelect();

		return $this->view->make('vessel::pages_edit')->with(compact(
			'page',
			'pagehistory',
			'edits',
			'drafts',
			'mode',
			'interface',
			'formatters_select_array',
			'formatter_current',
			'sub_templates'
			));
	}

	public function postPagesEdit($id)
	{
		$page = $this->page->findOrFail($id);
		$is_draft = $this->input->has('save_as_draft');
		return $this->pagehelper->savePage($page, 'edit', $is_draft);
	}

	public function getPagesDelete($id)
	{
		$page = $this->page->find($id);

		if ($page)
		{
			$page->delete();
			$this->notification->success('Page was deleted successfully.');
			return $this->redirect->route('vessel.pages');
		}

		throw new \VesselBackNotFoundException;
	}

	public function getPageHistoryDelete($id)
	{
		$pagehistory = $this->pagehistory->find($id);

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
		
		throw new \VesselBackNotFoundException;
	}


	public function getPageHistoryDeleteAll($id)
	{
		$page = $this->page->find($id);

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

		throw new \VesselBackNotFoundException;
	}
}