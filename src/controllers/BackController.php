<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Auth\AuthManager;
use Krucas\Notification\Notification;

class BackController extends Controller {

	protected $view;

	protected $input;

	protected $redirect;

	protected $auth;

	protected $notification;

	public function __construct(
		Environment $view,
		Request $input,
		Redirector $redirect,
		AuthManager $auth,
		Notification $notification)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->redirect     = $redirect;
		$this->auth         = $auth;
		$this->notification = $notification;
	}

	public function getHome()
	{
		return $this->view->make('vessel::home');
	}

	public function getLogin()
	{
		return $this->view->make('vessel::login');
	}

	public function postLogin()
	{
		$attempt = array(
			'password' => $this->input->get('password'),
			'confirmed'=> true
		);

		// check if username or email
		if (strpos($this->input->get('usernameemail'), '@') === false)
		{
			$attempt['username'] = $this->input->get('usernameemail');
		}
		else
		{
			$attempt['email'] = $this->input->get('usernameemail');
		}

		// attempt login
		if ($this->auth->attempt($attempt, $this->input->get('remember')))
		{
			$this->notification->success('You have been logged in successfully.');
			return $this->redirect->intended('vessel');
		}

		$this->notification->error('Your credentials were incorrect.');
		return $this->redirect->route('vessel.login')->withInput($this->input->except('password'));
	}

	public function getLogout()
	{
		$this->auth->logout();
		$this->notification->success('You have been logged out successfully.');
		return $this->redirect->route('vessel');
	}

}