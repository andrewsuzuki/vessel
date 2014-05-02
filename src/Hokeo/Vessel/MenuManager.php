<?php namespace Hokeo\Vessel;

use Menu\Menu as VMenu;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;

class MenuManager extends VMenu {

	protected $html;

	protected $url;

	protected $plugin;

	protected $menu; // model

	protected $menuitem; // model

	protected $page; // model

	protected $registered_mappers;

	/**
	 * Construct menu class
	 * 
	 * @return void
	 */
	public function __construct(
		HtmlBuilder $html,
		UrlGenerator $url,
		Plugin $plugin,
		Menu $menu,
		Menuitem $menuitem,
		Page $page)
	{
		$this->html     = $html;
		$this->url      = $url;
		$this->plugin   = $plugin;
		$this->menu     = $menu;
		$this->menuitem = $menuitem;
		$this->page     = $page;

		$this->registered_mappers = array();
		$this->registerBootstrapNavbarMapper();
	}

	/**
	 * Format a menu with items as <ol> for the backend drag-drop editor
	 * 
	 * @return string <ol> formatted for dbushell/nestable
	 */
	public function ddList($menu, $menuitems = null)
	{
		// if menuitems are not given, get the root items and order the set.
		if (!$menuitems) $menuitems = $menu->menuitems()->where('parent_id', null)->orderBy('lft')->get();
		$html = '<ol class="dd-list">'; // start our html <ol> list
		foreach ($menuitems as $item) // loop items
		{
			$type = $this->getItemType($item); // get the item's type based on null values

			if ($type == 'page')
			{
				$dataattrs = 'data-title="'.$item['title'].'" data-page="'.$item['page_id'].'"';
				$page = $this->page->find($item['page_id']);
				$page_title = ($page) ? $page->title : 'Page deleted'; // handle deleted pages with simple replacement
				$title = $item['title'].' (Page: '.$page_title.')';
			}
			elseif ($type == 'link')
			{
				$dataattrs = 'data-title="'.$item['title'].'" data-link="'.$item['link_if'].'"';
				$title = $item['title'].' (Link: <a href="'.$item['link_if'].'">'.$item['link_if'].'</a>)';
			}
			else
			{
				$dataattrs = '';
				$title = 'Separator';
			}

			$html .= '<li class="dd-item" data-id="'.$item['id'].'" data-type="'.$type.'" '.$dataattrs.'>'.
					'<div class="dd-handle"></div><div class="dd-content">'.$title.
					'&nbsp;&middot;&nbsp;<a href="#" class="menuitem-edit">Edit</a>'.
					'&nbsp;&middot;&nbsp;<a href="#" class="menuitem-delete" style="color:red">Delete</a></div>';

			$children = $item->children()->get(); // get this item's children
			if (!$children->isEmpty()) $html .= $this->ddList(null, $children); // recursive call with immediate children items

			$html .= '</li>';
		}

		$html .= '</ol>';

		return $html;
	}

	/**
	 * Gets menuitem type (based on null values)
	 *
	 * @param  object|array Menuitem
	 * @return string       'page'|'link'|'sep'
	 */
	public function getItemType($item)
	{
		if (is_null($item['page_id']) && is_null($item['link_if']))
			return 'sep';
		elseif (is_null($item['page_id']))
			return 'link';
		else
			return 'page';
	}


	/**
	 * Renders a saved menu by slug
	 *
	 * @param  string        $menu_slug Menu slug
	 * @param  bool|string   $map       Bool true maps menu with saved mapper, false doesn't, or string maps with registered mapper name
	 * @return string|null              HTML of menu, or null if it doesn't exist
	 */
	public function getSavedMenu($menu_slug, $map = true)
	{
		$menu = $this->menu->with('menuitems')->where('slug', $menu_slug)->first();
		if (!$menu) return null;
		return $this->makeSavedMenuHandler($menu, $map)->render();
	}

	/**
	 * Returns a saved menu and its menuitems and returns a vespakoen/menu handler
	 *
	 * @param  object        $menu Menu model
	 * @param  bool|string   $map  Bool true maps menu with saved mapper, false doesn't, or string maps with registered mapper name
	 * @return object
	 */
	public function makeSavedMenuHandler($menu, $map = true)
	{
		$handle = $this->handler('front.'.$menu->slug); // create handler

		$handle = $this->addSavedMenuitems($handle, $menu->menuitems->toHierarchy()); // add hierarchical menuitems to handler

		$handle = $this->plugin->fire('back.menu.front', array($handle, $menu), true); // plugin filter on handler

		$mapper = (is_string($map)) ? $map : $menu->mapper; // determine mapper to use

		if (!$map || !$mapper || !$this->mapperRegistered($mapper)) return $handle; // no mapping if mapper dne

		try
		{
			$this->useRegisteredMapper($handle, $mapper); // map menu handler with specified mapper
		}
		catch (\Exception $e)
		{
			// revert to no mapping
		}

		return $handle;
	}

	/**
	 * Adds saved menuitem to menu handler or ItemList
	 *
	 * @return void
	 */
	protected function addSavedMenuitems($handler, $items)
	{
		foreach ($items as $item) // loop over this level's items
		{
			$type = $this->getItemType($item); // get type

			if ($type == 'page' && ($page = $this->page->find($item->page_id))) // if it's a page, then make sure it exists
				$link = $page->url(); // get page url
			elseif ($type == 'link')
				$link = $item->link_if; // get link
			else
				$link = '#'; // if it wasn't an existing page revert to #

			// if there are children, recursively add children (using empty ItemList object instead of handler), otherwise children are null
			$children = ($item->children) ? $this->addSavedMenuitems($this->items(), $item->children) : null;

			if ($type == 'sep')
				$handler->raw('{SEPARATOR}', $children);
			else
				$handler->add($link, $item->title, $children);
		}

		return $handler;
	}

	/**
	 * Save menuitems for a menu
	 *
	 * @param  obj      $menu     Menu object
	 * @param  array    $items    Array of items to update; children items are added under item's 'children' key
	 * @param  null|obj $parent   The parent menu item for current level, do not specify (used for recursive calls)
	 * @param  array    $previous The previously saved menuitems, do not specify (used for recursive calls)
	 * @return array|bool         Updated items from the item's level and its children, or boolean false if it failed
	 */
	public function saveItems($menu, array $items, $parent = null, array $previous = array())
	{
		if (!$menu) return false; // lame check for existing menu

		if (!$parent) // if this is the root level...
		{
			$previous = array(); // new $previous list

			foreach ($menu->menuitems as $saveditem) // loop *previous*ly saved items
			{
				if (!$saveditem->isRoot()) $saveditem->makeRoot(); // if one isn't a root, "reset" it as a root
				$previous[$saveditem->id] = $saveditem; // make array of previous menu items
			}
		}

		$updated = array();

		$last = null;

		foreach ($items as $item) // loop updating items
		{
			if (isset($previous[$item['id']])) // if this item already exists...
				$obj = $previous[$item['id']]; // set $obj equal to it
			else
				$obj = $this->menuitem->newInstance(); // otherwise create a new Menuitem

			if ($item['type'] == 'page') // menuitems for pages
			{
				if (!isset($item['title']) || !strlen($item['title']) ||
					!isset($item['page']) || !$this->page->find($item['page'])) // validate
				{
					$obj->delete(); // delete it if it didn't pass validation
					continue;
				}

				$obj->title   = $item['title'];
				$obj->page_id = $item['page'];
				$obj->link_if = null;
			}
			elseif ($item['type'] == 'link') // menuitems for links
			{
				if (!isset($item['title']) || !strlen($item['title']) ||
					!isset($item['link']) || !strlen($item['link'])) // validate
				{
					$obj->delete();
					continue;
				}

				$obj->title   = $item['title'];
				$obj->page_id = null;
				$obj->link_if = $item['link'];
			}
			elseif ($item['type'] == 'sep') // item separators
			{
				$obj->title   = '';
				$obj->page_id = null;
				$obj->link_if = null;
			}
			else
			{
				$obj->delete();
				continue;
			}

			$obj->menu()->associate($menu); // associate item with menu

			try
			{
				$obj->save(); // save menuitem
				if ($parent) $obj->makeChildOf($parent); // if a parent is specified, make this item a child of it
				if ($last) $obj->moveToRightOf($last); // on this level, move this item to after the previous item
				$last = $obj; // set $last for the next iteration
				$updated[] = $obj->id; // mark this item as updated

				// if item has children, call this method recursively and merge into list of saved items
				if (isset($item['children']) && !empty($item['children']))
				{
					$children = $this->saveItems($menu, $item['children'], $obj, $previous); // save this item's children
					if (is_array($children)) $updated = array_merge($updated, $children); // merge children ids with this item's $updated
				}
			}
			catch (\Exception $e)
			{
				return false;
			}
		}

		// if this is the root level, loop over previously saved items
		// and delete ones that weren't updated this time
		if (!$parent)
		{
			foreach ($previous as $item)
				if (!in_array($item->id, $updated)) $item->delete();
		}

		return $updated;
	}

	/**
	 * Gets pages to display in 
	 * 
	 * @return obj collection
	 */
	public function getMenuablePages()
	{
		$pages = $this->page->all();
		return $this->plugin->fire('back.menumanager.getmenuablepages', $pages, true);
	}

	/**
	 * Make Vessel back-end menu on handler vessel.back.menu.main
	 * 
	 * @return void
	 */
	public function backMenu()
	{
		// make handler
		$menu = $this->handler('back.main');

		// items list
		$items = array(
			'vessel'          => array('display' => t('layout.menu.home'), 'permission' => null),
			'vessel.pages'    => array('display' => t('layout.menu.pages'), 'permission' => 'pages_manage'),
			'vessel.blocks'   => array('display' => t('layout.menu.blocks'), 'permission' => 'blocks_manage'),
			'vessel.menus'    => array('display' => t('layout.menu.menus'), 'permission' => 'menus_manage'),
			'vessel.media'    => array('display' => t('layout.menu.media'), 'permission' => 'media_manage'),
			'vessel.users'    => array('display' => t('layout.menu.users'), 'permission' => 'users_manage'),
			'vessel.settings' => array('display' => t('layout.menu.settings'), 'permission' => 'settings_manage'),
		);

		// add items to menu
		foreach ($items as $route => $info)
			if ($info['permission'] && can($info['permission'])) $menu->add($this->url->route($route), $info['display']);

		// filter
		$menu = $this->plugin->fire('back.menu.main', $menu, true);

		// map html for bootstrap
		$this->useRegisteredMapper($menu, 'bootstrap-navbar');
	}

	/**
	 * Registers a menu handler mapper
	 *
	 * @param  string   $name   Name of mapper, should be something unique (will not overwrite existing if not)
	 * @param  string   $title  Display title of mapper
	 * @param  callable $mapper Callable (can be array(class, method)) to map handler
	 * @return boole            Success
	 */
	public function registerMapper($name, $title, $mapper)
	{
		if (is_callable($mapper) && !isset($this->registered_mappers[$name]))
		{
			$this->registered_mappers[$name] = array(
				'name'   => $name,
				'title'  => $title,
				'mapper' => $mapper
			);

			return true;
		}

		return false;
	}

	/**
	 * Gets array of registered mappers
	 * 
	 * @return array Format: name => array(name, title, mapper)
	 */
	public function getRegisteredMappers()
	{
		return $this->registered_mappers;
	}

	/**
	 * Gets array of registered mappers for <select>
	 * 
	 * @return array Format: name => title
	 */
	public function getRegisteredMappersSelectArray()
	{
		$mappers = array();

		foreach ($this->registered_mappers as $mapper)
			$mappers[$mapper['name']] = $mapper['title'];

		return $mappers;
	}

	/**
	 * Checks if mapper with name exists
	 *
	 * @param  string $name Name of mapper
	 * @return bool
	 */
	public function mapperRegistered($name)
	{
		return isset($this->registered_mappers[$name]);
	}

	/**
	 * Uses a registered mapper on a menu handler
	 *
	 * @param  object $handler Menu handler
	 * @param  string $name    Name of registered menu mapper
	 * @return void
	 */
	public function useRegisteredMapper(&$handler, $name)
	{
		if (isset($this->registered_mappers[$name]))
			call_user_func($this->registered_mappers[$name]['mapper'], $handler);
		else
			throw new \Exception('Specified menu mapper is not registered.');
	}

	/**
	 * Register mapper for bootstrap navbar
	 * 
	 * @return void
	 */
	public function registerBootstrapNavbarMapper()
	{
		$this->registerMapper('bootstrap-navbar', 'Bootstrap Navbar', function($handler) {
			$handler->addClass('nav navbar-nav')->getItemsAtDepth(0)->map(function($item)
			{
				if ($item->hasChildren())
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

			$handler->getAllItems()->map(function($item)
			{
				if ($item->getValue()->getValue() == '{SEPARATOR}')
				{
					$item->getValue()->setValue('');
					$item->addClass('divider');
				}
			});
		});
	}
}