<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Krucas\Notification\Notification;
use Illuminate\Routing\Redirector;

class MenuController extends Controller {

	protected $view;

	protected $input;

	protected $validator;

	protected $auth;

	protected $config;

	protected $notification;

	protected $redirector;

	protected $asset;

	protected $plugin;

	public function __construct(
		Environment $view,
		Request $input,
		Factory $validator,
		AuthManager $auth,
		Repository $config,
		Notification $notification,
		Redirector $redirect,
		Asset $asset,
		Plugin $plugin,
		MenuManager $menumanager,
		Menu $menu)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->validator    = $validator;
		$this->auth         = $auth;
		$this->config       = $config;
		$this->notification = $notification;
		$this->redirect     = $redirect;
		$this->asset        = $asset;
		$this->plugin       = $plugin;
		$this->menumanager  = $menumanager;
		$this->menu         = $menu;
	}

	/**
	 * Get all menus page
	 * 
	 * @return object response
	 */
	public function getMenus()
	{
		$menus = $this->menu->all();
		$this->view->share('title', 'Menus');
		return $this->view->make('vessel::menus')->with(compact('menus'));
	}

	/**
	 * Get new menu page
	 * 
	 * @return object response
	 */
	public function getMenuNew()
	{
		$mode = 'new';
		$menu                 = $this->menu->newInstance();
		$ddlist               = $this->menumanager->ddList($menu);
		$menuable_pages       = $this->menumanager->getMenuablePages();
		$mappers_select_array = $this->menumanager->getRegisteredMappersSelectArray();

		$this->asset->css(asset('packages/hokeo/vessel/css/jquery.nestable.css'), 'jquery-nestable');
		$this->asset->js(asset('packages/hokeo/vessel/js/jquery.nestable.js'), 'jquery-nestable');
		$this->view->share('title', 'New Menu');
		return $this->view->make('vessel::menu')->with(compact('menu', 'ddlist', 'menuable_pages', 'mappers_select_array', 'mode'));
	}

	/**
	 * Get edit menu page
	 *
	 * @param  int|string $id ID of menu to edit
	 * @return object         response
	 */
	public function getMenuEdit($id)
	{
		$mode = 'edit';
		$menu = $this->menu->with('menuitems')->where('id', $id)->first(); // get menu with items
		if (!$menu) throw new \VesselBackNotFoundException;
		$ddlist               = $this->menumanager->ddList($menu);
		$menuable_pages       = $this->menumanager->getMenuablePages();
		$mappers_select_array = $this->menumanager->getRegisteredMappersSelectArray();

		$this->asset->css(asset('packages/hokeo/vessel/css/jquery.nestable.css'), 'jquery-nestable');
		$this->asset->js(asset('packages/hokeo/vessel/js/jquery.nestable.js'), 'jquery-nestable');
		$this->view->share('title', 'Edit Menu');
		return $this->view->make('vessel::menu')->with(compact('menu', 'ddlist', 'menuable_pages', 'mappers_select_array', 'mode'));
	}

	/**
	 * Handle new/edit menu form post
	 * 
	 * @return type description
	 */
	public function postMenuEdit($id = null)
	{
		$is_new = !((bool) $id); // determine if this is a new menu
		$menu   = ($is_new) ? $this->menu->newInstance() : $this->menu->with('menuitems')->where('id', $id)->first(); // get new or existing menu model
		if (!$menu) throw new \VesselBackNotFoundException;

		$rules     = ($is_new) ? $this->menu->rules() : $this->menu->rules($menu); // get rules
		$validator = $this->validator->make($this->input->all(), $rules); // validate input

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// save menu
		$menu->title       = $this->input->get('title');
		$menu->slug        = $this->input->get('slug');
		$menu->description = $this->input->get('description');
		$menu->mapper      = $this->input->get('mapper');
		$menu->user()->associate($this->auth->user());
		$menu->save();

		// save menuitems
		$items = json_decode($this->input->get('menuitems'), true);
		if ($this->menumanager->saveItems($menu, $items) === false)
		{
			// redirect back with error and input
			$this->notification->error(t('messages.menus.delete.items-error'));
			return $this->redirect->back()->withInput();
		}

		$this->notification->success(t('messages.general.save-success', array('name' => 'Menu')));
		return $this->redirect->route('vessel.menus.edit', array('id' => $menu->id));
	}

	/**
	 * Delete a menu and redirect
	 *
	 * @param  int|string $id ID of menu to delete
	 * @return object         redirect response
	 */
	public function getMenuDelete($id)
	{
		$menu = $this->menu->find($id); // get menu
		if (!$menu) throw new \VesselBackNotFoundException;

		if ($menu->delete()) // delete menu (model event deletes associated menuitems automagically)
			$this->notification->success(t('messages.general.delete-success', array('name' => 'Menu')));
		else
			$this->notification->error(t('messages.menus.delete.error'));

		return $this->redirect->route('vessel.menus');
	}
}