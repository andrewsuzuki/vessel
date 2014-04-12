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

	protected $validator;

	protected $redirect;

	protected $notification;

	protected $perm;

	protected $theme;

	public function __construct(
		Environment $view,
		Request $input,
		Factory $validator,
		Redirector $redirect,
		Notification $notification,
		Perm $perm,
		Theme $theme)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->validator    = $validator;
		$this->redirect     = $redirect;
		$this->notification = $notification;
		$this->perm         = $perm;
		$this->theme        = $theme;
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
		$settings = (object) $this->perm->load('vessel.site')->all();

		$timezones = \DateTimeZone::listIdentifiers();
		$timezone_select_array = array_combine($timezones, array_map(function($timezone) {
			return str_replace(['_', '/'], [' ', ' / '], $timezone);
		}, $timezones));

		$themes = $this->theme->getAvailable();

		return $this->view->make('vessel::settings')->with(compact('settings', 'timezone_select_array', 'themes'));
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
			'title'              => 'required',
			'title_format'       => 'required',
			'description'        => '',
			'description_format' => '',
			'url'                => 'required|url',
			'theme'              => 'required|theme',
			'timezone'           => 'required|timezone',
			// 'language'        => 'required|language',
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
			'title'              => $this->input->get('title'),
			'title_format'       => $this->input->get('title_format'),
			'description'        => $this->input->get('description'),
			'description_format' => $this->input->get('description_format'),
			'url'                => $this->input->get('url'),
			'theme'              => $this->input->get('theme'),
			'timezone'           => $this->input->get('timezone'),
		));
		$settings->save();

		$this->notification->success('Site settings were updated successfully.');
		return $this->redirect->route('vessel.settings');
	}
}