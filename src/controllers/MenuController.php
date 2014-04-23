<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Response;

class MenuController extends Controller {

	protected $view;

	protected $input;

	protected $auth;

	protected $config;

	protected $response;

	protected $plugin;

	public function __construct(
		Environment $view,
		Request $input,
		AuthManager $auth,
		Repository $config,
		Response $response,
		Plugin $plugin)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->auth         = $auth;
		$this->config       = $config;
		$this->response     = $response;
		$this->plugin       = $plugin;
	}

	/**
	 * Get all menus page
	 * 
	 * @return object response
	 */
	public function getMenus()
	{
		$menus = array();

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
		$this->view->share('title', 'Menu');
		return $this->view->make('vessel::menu');
	}

	/**
	 * Get edit menu page
	 *
	 * @param  int|string $id ID of menu to edit
	 * @return object         response
	 */
	public function getMenuEdit($id)
	{
		$this->view->share('title', 'Menu');
		return $this->view->make('vessel::menu');
	}
}