<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Validation\Factory;
use Illuminate\Hashing\HasherInterface;
use Illuminate\Routing\Redirector;
use Krucas\Notification\Notification;

class UserController extends Controller {

	protected $view;

	protected $input;

	protected $auth;

	protected $validator;

	protected $hash;

	protected $redirect;

	protected $notification;

	protected $user; // model

	public function __construct(
		Environment $view,
		Request $input,
		AuthManager $auth,
		Factory $validator,
		HasherInterface $hash,
		Redirector $redirect,
		Notification $notification,
		User $user)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->auth         = $auth;
		$this->validator    = $validator;
		$this->hash         = $hash;
		$this->redirect     = $redirect;
		$this->notification = $notification;
		$this->user         = $user;
	}

	/**
	 * Get user's settings page
	 * 
	 * @return response
	 */
	public function getMe()
	{
		$user = $this->auth->user();

		$this->view->share('title', 'User Settings');

		return $this->view->make('vessel::settings')->with(compact('user'));
	}

	/**
	 * Handle user setting saving
	 * 
	 * @return response
	 */
	public function postMe()
	{
		// get current authenticated user
		$user = $this->auth->user();

		$rules = $this->user->rules('user'); // get validation rules (for user settings editing)
		$validator = $this->validator->make($this->input->all(), $rules); // validate input

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// save settings
		
		$user->email      = $this->input->get('email');
		$user->first_name = $this->input->get('first_name');
		$user->last_name  = $this->input->get('last_name');

		// if a new password was entered, save it
		if ($this->input->get('password'))
			$user->password = $this->hash->make($this->input->get('password'));

		$user->save();
		
		$this->notification->success('Your settings were updated successfully.');
		return $this->redirect->route('vessel.me');
	}
}