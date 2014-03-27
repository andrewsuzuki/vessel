<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Krucas\Notification\Facades\Notification;

class PageController extends Controller
{
	protected $filesystem;

	public function __construct(FilesystemInterface $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	public function getPages()
	{
		$pages = Page::with('user')->get();
		View::share('title', 'Pages');
		return View::make('vessel::pages')->with(compact('pages'));
	}

	public function getPagesNew()
	{
		$mode = 'new';
		$page = new Page;
		View::share('title', 'New Page');
		$this->setPageFormatter($page);
		$editor = FormatterFacade::formatter()->getEditorHtml();
		return View::make('vessel::pages_edit')->with(compact('page', 'mode', 'editor'));
	}

	public function postPagesNew()
	{
		$page = new Page;
		return $this->savePage($page, 'new');
	}

	public function getPagesEdit($id)
	{
		$mode = 'edit';
		$page = Page::find($id); // find page
		if (!$page)
		{
			throw new \VesselNotFoundException;
		}
		View::share('title', 'Edit '.$page->title); // set view title
		$this->setPageFormatter($page);
		$content = $this->getContent($page->id, true);
		$editor = FormatterFacade::formatter()->getEditorHtml($content); // get editor html

		return View::make('vessel::pages_edit')->with(compact('page', 'mode', 'editor'));
	}

	public function postPagesEdit($id)
	{
		$page = Page::findOrFail($id);
		return $this->savePage($page, 'edit');
	}

	/**
	 * Sets formatter (editor) for this page
	 * 
	 * @param object $page
	 */
	public function setPageFormatter($page)
	{
		// try for a set formatter input
		if (Input::get('formatter') && FormatterFacade::exists(Input::get('formatter')))
		{
			FormatterFacade::set(Input::get('formatter'));
			return 1;
		}
		// or try old input
		elseif (Input::old('formatter') && FormatterFacade::exists(Input::old('formatter')))
		{
			FormatterFacade::set(Input::old('formatter'));
			return 2;
		}
		// or try set page setting
		elseif ($page && $page->formatter && FormatterFacade::exists($page->formatter))
		{
			FormatterFacade::set($page->formatter);
			return 3;
		}
		// or try user preference
		elseif (Auth::user()->preferred_formatter && FormatterFacade::exists(Auth::user()->preferred_formatter))
		{
			FormatterFacade::set(Auth::user()->preferred_formatter);
			return 4;
		}
		// whoops, let's revert to Markdown
		else
		{
			FormatterFacade::set('Markdown');
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
		if (!$get_raw && $this->filesystem->has('pages/compiled/'.$page_id.'.php'))
		{
			return $this->filesystem->read('pages/compiled/'.$page_id.'.php');
		}
		elseif ($this->filesystem->has('pages/'.$page_id.'.v'))
		{
			return $this->filesystem->read('pages/'.$page_id.'.v');
		}
		else
		{
			return false;
		}
	}

	public function saveContent($page_id, $formatter, $content)
	{
		// save raw content
		$this->filesystem->put('pages/'.$page_id.'.v', $content);

		// save formatted and compiled content
		if (FormatterFacade::exists($formatter))
		{
			FormatterFacade::set($formatter);
			$formatter = FormatterFacade::formatter();

			$formatted = $formatter->render($content);
			$compiled = FormatterFacade::compileBlade($formatted);

			$this->filesystem->put('pages/compiled/'.$page_id.'.php', $compiled);
		}
	}

	public function savePage($page, $mode = 'edit')
	{
		if ($mode == 'edit')
		{
			if (Input::get('updated_at') != (string) $page->updated_at && !Input::get('force_edit'))
			{
				Notification::warning('This page has been updated elsewhere since you started editing. Click save again to force this edit.');
				return Redirect::back()->with('force_edit', 'true')->withInput();
			}

			$rules = Page::rules($page);
		}
		else
		{
			$rules = Page::rules();
		}

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			Notification::error($validator->messages()->first());
			return Redirect::back()->withInput();
		}

		$page->title = Input::get('title');
		$page->slug = Input::get('slug');
		$page->description = Input::get('description');
		$page->formatter = Input::get('formatter');
		$page->nest_url = Input::get('nest_url');
		$page->user()->associate(Auth::user());
		
		$page->save();

		$this->saveContent($page->id, $page->formatter, Input::get('content'));

		if (Input::get('parent') !== 'none')
		{
			$page->makeChildOf(Page::find(Input::get('parent')));
		}
		elseif (!$page->isRoot())
		{
			$page->makeRoot();
		}

		if ($mode == 'edit')
			Notification::success('Page was edited successfully.');
		else
			Notification::success('Page was created successfully.');

		return Redirect::route('vessel.pages.edit', array('id' => $page->id));
	}
}