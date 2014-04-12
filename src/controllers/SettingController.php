<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Illuminate\Routing\Redirector;
use Krucas\Notification\Notification;
use Andrewsuzuki\Perm\Perm;

class SettingController extends Controller {

	protected $view;

	protected $input;

	protected $auth;

	protected $validator;

	protected $redirect;

	protected $notification;

	public function __construct(
		Environment $view,
		Request $input,
		Factory $validator,
		Redirector $redirect,
		Notification $notification,
		Perm $perm)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->validator    = $validator;
		$this->redirect     = $redirect;
		$this->notification = $notification;
		$this->perm         = $perm;
	}

	/**
	 * Get settings page
	 * 
	 * @return response
	 */
	public function getSettings()
	{
		$this->view->share('title', 'Site Settings');

		// load persistent site settings and cast to object (emulates model for Form)
		$settings = (object) $this->perm->load('vessel.site')->getAll();

		return $this->view->make('vessel::settings')->with(compact('settings'));
	}

	/**
	 * Handle settings saving
	 * 
	 * @return response
	 */
	public function postSettings()
	{
		// validation rules
		$rules = array(
			'title' => 'required',
		);

		$validator = $this->validator->make($this->input->all(), $rules); // validate input

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// load, modify, save persistent site settings using perm
		$settings = $this->perm->load('vessel.site');
		$settings->set(array(
			'title' => $this->input->get('title'),
		));
		$settings->save();

		$this->notification->success('Site settings were updated successfully.');
		return $this->redirect->route('vessel.settings');
	}
}