<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Krucas\Notification\Notification;

class BackController extends Controller {

	protected $view;

	protected $redirect;

	protected $input;

	protected $auth;

	protected $notification;

	public function __construct(
		Environment $view,
		Redirector $redirect,
		Request $input,
		AuthManager $auth,
		Notification $notification)
	{
		$this->view         = $view;
		$this->redirect     = $redirect;
		$this->input        = $input;
		$this->auth         = $auth;
		$this->notification = $notification;
	}

	public function getHome()
	{
		return $this->view->make('vessel::home');
	}

	public function getDne()
	{
		throw new \VesselNotFoundException;
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
			return $this->redirect->route('vessel');
		}

		return $this->redirect->route('vessel.login')->withInput($this->input->except('password'));
	}

	public function getLogout()
	{
		$this->auth->logout();
		return $this->redirect->route('vessel');
	}

}