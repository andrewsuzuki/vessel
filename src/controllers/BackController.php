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
		Facades\Plugin::enable('helloworld');

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

}