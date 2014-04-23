<?php namespace Hokeo\Vessel;

use Menu\Menu as VMenu;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;

class MenuManager extends VMenu {

	protected $html;

	protected $url;

	protected $plugin;

	protected $menu; // model

	protected $back_menu_built;

	/**
	 * Construct menu class
	 * 
	 * @return void
	 */
	public function __construct(
		HtmlBuilder $html,
		UrlGenerator $url,
		Plugin $plugin,
		Menu $menu)
	{
		$this->html   = $html;
		$this->url    = $url;
		$this->plugin = $plugin;
		$this->menu   = $menu;
	}

	/**
	 * Make Vessel back-end menu on handler vessel.back.menu.main
	 * 
	 * @return void
	 */
	public function backMenu()
	{
		if (!$this->back_menu_built)
		{
			$menu = $this->handler('vessel.back.menu.main', array('class' => 'nav navbar-nav'));
			$menu->add($this->url->route('vessel'), 'Home')
			->add($this->url->route('vessel.pages'), 'Pages')
			->add($this->url->route('vessel.blocks'), 'Blocks')
			->add($this->url->route('vessel.menus'), 'Menus')
			->add($this->url->route('vessel.media'), 'Media')
			->add($this->url->route('vessel.users'), 'Users')
			->add($this->url->route('vessel.settings'), 'Settings');

			$menu = $this->plugin->fire('back.menu.main', [$menu], true);

			$this->handler('vessel.back.menu.main')->mapBootstrap();

			$this->back_menu_built = true;
		}
	}

	/**
	 * Maps a menu for bootstrap navbar formatting
	 * 
	 * @return void
	 */
	public function mapBootstrap()
	{
		return $this->getItemsAtDepth(0)->map(function($item)
		{
			if($item->hasChildren())
			{
				$item->addClass('dropdown');

				$item->getChildren()
				->addClass('dropdown-menu');

				$item->getContent()
				->addClass('dropdown-toggle')
				->dataToggle('dropdown')
				->nest(' <b class="caret"></b>');
			}
		});
	}
}