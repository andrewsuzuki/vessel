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

	protected $pagehelper;

	protected $pm;

	protected $role;

	public function __construct(
		Environment $view,
		Request $input,
		Factory $validator,
		Redirector $redirect,
		Notification $notification,
		Perm $perm,
		Theme $theme,
		PageHelper $pagehelper,
		PluginManager $pm,
		Role $role)
	{
		$this->view          = $view;
		$this->input         = $input;
		$this->validator     = $validator;
		$this->redirect      = $redirect;
		$this->notification  = $notification;
		$this->perm          = $perm;
		$this->theme         = $theme;
		$this->pagehelper    = $pagehelper;
		$this->pm            = $pm;
		$this->role          = $role;
	}

	/**
	 * Get settings page
	 * 
	 * @return response
	 */
	public function getSettings()
	{
		$this->view->share('title', t('settings.main-title'));

		// homepage select array
		$home_select_array = $this->pagehelper->possibleHomeArray();

		// timezone select array
		$timezones = \DateTimeZone::listIdentifiers();
		$timezone_select_array = array_combine($timezones, array_map(function($timezone) {
			return str_replace(['_', '/'], [' ', ' / '], $timezone);
		}, $timezones));

		// load persistent site settings
		$settings = $this->perm->load('vessel.site');

		// roles
		$roles = $this->role->all();

		// cast all settings to object (emulates model for form)
		$settings = (object) $settings->all();

		// plugins
		$plugins = $this->pm->getAvailable();
		sort($plugins); // sort alphabetically
		$plugins_enabled = $this->perm->load('vessel.plugins')->get('enabled');
		if (!is_array($plugins_enabled)) $plugins_enabled = array();

		// theme select array
		$themes = $this->theme->getAvailable();

		return $this->view->make('vessel::settings')->with(compact(
			'settings',
			'home_select_array',
			'roles',
			'timezone_select_array',
			'plugins',
			'plugins_enabled',
			'themes'
			));
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
			'title'                => 'required',
			'description'          => '',
			'url'                  => 'required|url',
			'home'                 => 'required|home_page_id',
			'timezone'             => 'required|timezone',
			// 'language'          => 'required|language',
			'registration'         => '', // checkbox
			'registration_confirm' => '', // checkbox
			'default_roles'        => 'roles',
			'plugins'              => 'plugins',
			'theme'                => 'required|theme',
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
			'description'        => $this->input->get('description'),
			'url'                => $this->input->get('url'),
			'home'               => $this->input->get('home'),
			'theme'              => $this->input->get('theme'),
			'timezone'           => $this->input->get('timezone'),
			'registration'       => (bool) $this->input->get('registration'),
		));


		if ($this->input->get('registration'))
		{
			$settings->registration_confirm = (bool) $this->input->get('registration_confirm');
			$settings->default_roles        = $this->input->get('default_roles');
		}

		$settings->save();

		// save enabled plugins
		$plugins = $this->perm->load('vessel.plugins');

		$available_plugins = $this->pm->getAvailable(); // get available plugins
		$enabled = array();
		if ($this->input->get('plugins'))
		{
			foreach ($this->input->get('plugins') as $dir)
				if (in_array($dir, $available_plugins)) $enabled[] = array_search($dir, $available_plugins);
		}

		$plugins->set('enabled', $enabled);

		$plugins->save();

		$this->notification->success(t('messages.settings.save-success'));
		return $this->redirect->route('vessel.settings');
	}
}