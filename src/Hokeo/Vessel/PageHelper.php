<?php namespace Hokeo\Vessel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Auth\AuthManager;
use Illuminate\Validation\Factory;
use Krucas\Notification\Notification;

class PageHelper {

	protected $vessel;

	protected $formatter;

	protected $filesystem;

	protected $input;

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
		Redirector $redirect,
		AuthManager $auth,
		Factory $validator,
		Notification $notification)
	{
		$this->vessel       = $vessel;
		$this->formatter    = $formatter;
		$this->filesystem   = $filesystem;
		$this->input        = $input;
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

	public function evalContent($page_id, array $data = array())
	{
		ob_start();
		extract($data, EXTR_SKIP);

		try
		{
			eval('?>'.$this->getContent($page_id, false));
		}
		catch (Exception $e)
		{
			ob_end_clean();
			throw $e;
		}

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	public function getContent($page_id, $get_raw = false)
	{
		if (!$get_raw && $this->filesystem->exists($this->pages_path.'/compiled/'.$page_id.'.php'))
		{
			return $this->filesystem->get($this->pages_path.'/compiled/'.$page_id.'.php');
		}
		elseif ($this->filesystem->exists($this->pages_path.'/'.$page_id.'.v'))
		{
			return $this->filesystem->get($this->pages_path.'/'.$page_id.'.v');
		}
		else
		{
			return false;
		}
	}

	public function saveContent($page_id, $formatter, $content)
	{
		// save raw content
		$this->filesystem->put($this->pages_path.'/'.$page_id.'.v', $content);

		// save formatted and compiled content
		if ($this->formatter->exists($formatter))
		{
			$this->formatter->set($formatter);
			$formatter = $this->formatter->formatter();

			$formatted = $formatter->render($content);
			$compiled = $this->formatter->compileBlade($formatted);

			$this->filesystem->put($this->pages_path.'/compiled/'.$page_id.'.php', $compiled);
		}
	}

	public function savePage($page, $mode = 'edit')
	{
		if ($mode == 'edit')
		{
			if ($this->input->get('updated_at') != (string) $page->updated_at && !$this->input->get('force_edit'))
			{
				$this->notification->warning('This page has been updated elsewhere since you started editing. Click save again to force this edit.');
				return $this->redirect->back()->with('force_edit', 'true')->withInput();
			}

			$rules = Page::rules($page);
		}
		else
		{
			$rules = Page::rules();
		}

		$validator = $this->validator->make($this->input->all(), $rules);

		if ($validator->fails())
		{
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		$page->title       = $this->input->get('title');
		$page->slug        = $this->input->get('slug');
		$page->description = $this->input->get('description');
		$page->formatter   = $this->input->get('formatter');
		$page->nest_url    = (bool) $this->input->get('nest_url');
		$page->visible     = (bool) $this->input->get('visible');
		$page->in_menu     = (bool) $this->input->get('in_menu');

		$page->user()->associate($this->auth->user());
		
		$page->save();

		$this->saveContent($page->id, $page->formatter, $this->input->get('content'));

		if ($this->input->get('parent') !== 'none')
		{
			$page->makeChildOf(Page::find($this->input->get('parent')));
		}
		elseif (!$page->isRoot())
		{
			$page->makeRoot();
		}

		if ($mode == 'edit')
			$this->notification->success('Page was edited successfully.');
		else
			$this->notification->success('Page was created successfully.');

		return $this->redirect->route('vessel.pages.edit', array('id' => $page->id));
	}
}