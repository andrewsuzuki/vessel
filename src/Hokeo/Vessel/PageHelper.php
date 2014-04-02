<?php namespace Hokeo\Vessel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Database\DatabaseManager;
use Illuminate\Routing\Redirector;
use Illuminate\Auth\AuthManager;
use Illuminate\Validation\Factory;
use Krucas\Notification\Notification;

class PageHelper {

	protected $vessel;

	protected $formatter;

	protected $filesystem;

	protected $input;

	protected $db;

	protected $redirect;

	protected $auth;

	protected $validator;

	protected $notification;

	protected $pages_path;

	public function __construct(
		Vessel $vessel,
		Formatter $formatter,
		Filesystem $filesystem,
		Request $input,
		DatabaseManager $db,
		Redirector $redirect,
		AuthManager $auth,
		Factory $validator,
		Notification $notification)
	{
		$this->vessel       = $vessel;
		$this->formatter    = $formatter;
		$this->filesystem   = $filesystem;
		$this->input        = $input;
		$this->db           = $db;
		$this->redirect     = $redirect;
		$this->auth         = $auth;
		$this->validator    = $validator;
		$this->notification = $notification;

		$this->pages_path = $this->vessel->path('/pages');
	}

	/**
	 * Sets formatter (editor) for editing this page
	 * 
	 * @param object $page
	 */
	public function setPageFormatter($page)
	{
		// try for a set formatter input
		if ($this->input->get('formatter') && $this->formatter->exists($this->input->get('formatter')))
		{
			$this->formatter->set($this->input->get('formatter'));
			return 1;
		}
		// or try old input
		elseif ($this->input->old('formatter') && $this->formatter->exists($this->input->old('formatter')))
		{
			$this->formatter->set($this->input->old('formatter'));
			return 2;
		}
		// or try set page setting
		elseif ($page && $page->formatter && $this->formatter->exists($page->formatter))
		{
			$this->formatter->set($page->formatter);
			return 3;
		}
		// or try user preference
		elseif ($this->auth->user()->preferred_formatter && $this->formatter->exists($this->auth->user()->preferred_formatter))
		{
			$this->formatter->set($this->auth->user()->preferred_formatter);
			return 4;
		}
		// whoops, let's revert to Markdown
		else
		{
			$this->formatter->set('Markdown');
			return 5;
		}

		// hook here?
	}

	public function getDisplayContent($page)
	{
		return $this->vessel->returnEval($page->content_made);
	}

	/**
	 * Saves page content as file and compiles
	 * 
	 * @param  integer $page_id   ID of page
	 * @param  string  $formatter Name of formatter (compiler)
	 * @param  string  $content   Content of page
	 */
	public function makeContent($formatter, $content)
	{
		// encode php tags as entities
		$compiled = str_replace(['<?', '?>'], ['&lt;?', '?&gt'], $content);

		// save formatted and compiled content
		if ($this->formatter->exists($formatter))
		{
			// set and get formatter
			$this->formatter->set($formatter);
			$formatter = $this->formatter->formatter();

			// render content and compile resulting blade template
			$formatted = $formatter->render($compiled);
			$compiled = $this->formatter->compileBlade($formatted);
		}

		return $compiled;
	}

	/**
	 * Saves page given input
	 * 
	 * @param  object  $page  Page object (can be new)
	 * @param  string  $mode  edit|new
	 * @param  boolean $draft If editing, bool true will save as a draft
	 * @return object         Redirect response
	 */
	public function savePage($page, $mode = 'edit', $draft = false)
	{
		if ($mode == 'edit')
		{
			// if saving an edit, give a warning if it's been edited elsewhere since
			if ($this->input->get('updated_at') != (string) $page->updated_at && !$this->input->get('force_edit'))
			{
				$this->notification->warning('This page has been updated elsewhere since you started editing. Click save again to force this edit.');
				return $this->redirect->back()->with('force_edit', 'true')->withInput();
			}

			$rules = Page::rules($page); // validation rules for editing
		}
		else
		{
			$rules = Page::rules(); // validation rules for new
		}

		// validate input

		$validator = $this->validator->make($this->input->all(), $rules);

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// if we're editing and not saving a draft, then save an edit history first
		if ($mode == 'edit' && !$draft)
		{
			$edithistory              = new Pagehistory;

			$edithistory->title       = $page->title;
			$edithistory->slug        = $page->slug;
			$edithistory->description = $page->description;
			$edithistory->content = $page->content;
			$edithistory->formatter   = $page->formatter;
			$edithistory->template    = $page->template;
			$edithistory->nest_url    = $page->nest_url;
			$edithistory->visible     = $page->visible;
			$edithistory->in_menu     = $page->in_menu;

			$edithistory->is_draft    = false;
			$edithistory->created_at  = $page->updated_at;
			$edithistory->edit        = ($last_edit = $this->db->table($edithistory->getTable())->max('edit')) ? $last_edit + 1 : 1;

			$edithistory->page()->associate($page);

			$edithistory->user()->associate($page->user);

			$edithistory->save();
		}

		// determine setter (new draft or page)
		$setter = ($draft) ? new Pagehistory : $page;

		// set fields
		$setter->title       = $this->input->get('title');
		$setter->slug        = $this->input->get('slug');
		$setter->description = $this->input->get('description');
		$setter->content     = $this->input->get('content');
		$setter->formatter   = $this->input->get('formatter');
		$setter->template    = $this->input->get('template');
		$setter->nest_url    = (bool) $this->input->get('nest_url');
		$setter->visible     = (bool) $this->input->get('visible');
		$setter->in_menu     = (bool) $this->input->get('in_menu');

		// associate saver (user)
		$setter->user()->associate($this->auth->user());

		if ($draft)
		{
			$setter->is_draft   = true;
			$setter->created_at = \Carbon\Carbon::now();
			$setter->edit       = ($last_edit = $this->db->table($setter->getTable())->max('edit')) ? $last_edit + 1 : 1;

			$setter->page()->associate($page); // associate draft with page
		}
		else
		{
			// make (format + compile) content
			$setter->content_made = $this->makeContent($page->formatter, $this->input->get('content'));
		}

		$setter->save();

		// if a parent page was specified, make child of that parent
		if ($this->input->get('parent') !== 'none')
		{
			if ($draft)
				$setter->parent_id = $this->input->get('parent');
			else
				$setter->makeChildOf(Page::find($this->input->get('parent')));
		}
		// otherwise make it a root page (if it isn't already)
		elseif (!$page->isRoot())
		{
			if ($draft)
				$setter->parent_id = null;
			else
				$setter->makeRoot();
		}

		// determine success message
		if ($mode == 'edit' && $draft)
			$this->notification->success('Draft was saved successfully.');
		elseif ($mode == 'edit')
			$this->notification->success('Page was edited successfully.');
		else
			$this->notification->success('Page was created successfully.');

		$params = array('id' => $page->id);

		// let's show them the draft with the history param
		if ($draft) $params['history'] = $setter->id;

		return $this->redirect->route('vessel.pages.edit', $params);
	}
}