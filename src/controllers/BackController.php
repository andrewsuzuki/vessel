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

class BackController extends Controller {

	public function getHome()
	{
		return View::make('vessel::home');
	}

	public function getDne()
	{
		throw new \VesselNotFoundException;
	}

	public function getLogin()
	{
		return View::make('vessel::login');
	}

	public function postLogin()
	{
		$attempt = array(
			'password' => Input::get('password'),
			'confirmed'=> true
		);

		// check if username or email
		if (strpos(Input::get('usernameemail'), '@') === false)
		{
			$attempt['username'] = Input::get('usernameemail');
		}
		else
		{
			$attempt['email'] = Input::get('usernameemail');
		}

		// attempt login
		if (Auth::attempt($attempt, Input::get('remember')))
		{
			return Redirect::route('vessel');
		}

		Notification::error('Your credentials were incorrect.');
		return Redirect::route('vessel.login')->withInput(Input::except('password'));
	}

	public function getLogout()
	{
		Auth::logout();
		return Redirect::route('vessel');
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
		FormatterFacade::set('Markdown');
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
		FormatterFacade::set('Markdown');
		$editor = FormatterFacade::formatter()->getEditorHtml(); // get editor html
		return View::make('vessel::pages_edit')->with(compact('page', 'mode', 'editor'));
	}

	public function postPagesEdit($id)
	{
		$page = Page::findOrFail($id);
		return $this->savePage($page, 'edit');
	}

	public function savePage($page, $mode = 'edit')
	{
		if ($mode == 'edit')
		{
			if (Input::get('updated_at') !== $page->updated_at && !Input::get('force_edit'))
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
		$page->user()->associate(Auth::user());
		
		$page->save();

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