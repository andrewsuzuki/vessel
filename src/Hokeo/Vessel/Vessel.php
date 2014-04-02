<?php namespace Hokeo\Vessel;

use Illuminate\Foundation\Application;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;
use Menu\Menu;

class Vessel {

	protected $app;

	protected $html;

	protected $url;

	protected $menu;

	protected $storage_path;

	protected $back_menu_built = false;

	protected $dirs = array('', '/');

	public function __construct(Application $app, HtmlBuilder $html, UrlGenerator $url, Menu $menu)
	{
		$this->app  = $app;
		$this->html = $html;
		$this->url  = $url;
		$this->menu = $menu;

		$this->storage_path = storage_path().'/vessel';
		$this->checkStoragePath();
	}

	/**
	 * Gets vessel version or version component
	 * 
	 * @param  string $type full|short|major|minor|patch
	 * @return string
	 */
	public function getVersion($type = 'full')
	{
		$available = array('full', 'short', 'major', 'minor', 'patch');
		if (in_array($type, $available)) return (string) $this->app->make('vessel.version.'.$type);
	}

	/**
	 * Checks if all vessel storage directories exist, and makes them if not.
	 */
	public function checkStoragePath()
	{
		foreach ($this->dirs as $path)
		{
			if (!is_dir($this->storage_path.$path))
			{
				mkdir($this->storage_path.$path, 0777, true);
			}
		}
	}

	/**
	 * Get absolute path to path relative to app/storage/vessel
	 * 
	 * @param  string $path
	 * @return string
	 */
	public function path($path = '')
	{
		if (in_array($path, $this->dirs))
		{
			return $this->storage_path.$path;
		}
	}

	/**
	 * Evaluate (execute) PHP code in string
	 * 
	 * @param  string $content String, possibly containing PHP
	 * @return string          Evaluated content
	 */
	public function returnEval($content)
	{
		ob_start();

		try
		{
			eval('?>'.$content);
		}
		catch (Exception $e)
		{
			ob_end_clean();
			throw $e;
		}

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	public function backMenu()
	{
		if (!$this->back_menu_built)
		{
			$menu = $this->menu->handler('vessel.menu.main', array('class' => 'nav navbar-nav'));
			$menu->add($this->url->route('vessel'), 'Home')
			->add($this->url->route('vessel.pages'), 'Pages')
			->add('#', 'Blocks')
			->add('#', 'Media')
			->add('#', 'Users')
			->add('#', 'Settings');

			$this->menu->handler('vessel.menu.main')->getItemsAtDepth(0)->map(function($item)
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

			$this->back_menu_built = true;
		}
	}

	/**
	 * Gets collection of menu pages
	 * 
	 * @return nested collection object
	 */
	public function getMenuHierarchy()
	{
		return Page::visible()->menu()->get()->toHierarchy();
	}

	public function getMenu($active = null, array $attributes = array(), $active_class = 'active', $active_parent_class = 'active-parent', $parent = null)
	{
		$html = '';

		if ($parent)
			$pages = $parent->children()->get()->toHierarchy();
		else
			$pages = $this->getMenuHierarchy();

		foreach ($pages as $page)
		{
			// li tag with targeted attributes
			$html .= '<li '.$this->html->attributes($this->nestedAttributes($attributes, 'li', $page->getLevel(), $page->id, $page->slug));

			// a tag targeted attributes
			$link_attributes = $this->nestedAttributes($attributes, 'a', $page->getLevel(), $page->id, $page->slug);

			// if a tag has no class, set a blank one
			if (!isset($link_attributes['class']))
				$link_attributes['class'] = '';

			// if the 'current' page is this or an ancestor of this page, add the active_class
			if ($active && $page->isSelfOrAncestorOf($active))
			{
				$link_attributes['class'] .= ' '.$active_class;
			}

			// if the 'current' page is an ancestor of this page, add the active_parent_class
			if ($active && $page->isAncestorOf($active))
			{
				$link_attributes['class'] .= ' '.$active_parent_class;
			}

			// build a link tag with attributes
			$link = '<a href="'.$page->url().'" '.$this->html->attributes($link_attributes);

			$sub = '';

			// if this page has some children...
			if (!$page->children()->get()->isEmpty())
			{
				// add li:has-children attributes
				$html .= ' '.$this->html->attributes($this->nestedAttributes($attributes, 'li:has-children', $page->getLevel(), $page->id, $page->slug));

				// add a:has-children attributes
				$link_haschildren_attributes = $this->nestedAttributes($attributes, 'a:has-children', $page->getLevel(), $page->id, $page->slug);
				$link .= ' '.$this->html->attributes($link_haschildren_attributes);

				// let's go recursive and build a sub-menu
				$sub .= '<ul '.$this->html->attributes($this->nestedAttributes($attributes, 'ul', $page->getLevel(), $page->id, $page->slug)).'>';
				$sub .= $this->getMenu($active, $attributes, $active_class, $active_parent_class, $page);
				$sub .= '</ul>';
			}

			// finish our link with optional content-pre/post "attributes" surrounding page title
			$link .= '>'.
			((isset($link_haschildren_attributes) && isset($link_haschildren_attributes['content-pre'])) ? $link_haschildren_attributes['content-pre'] : '').
			((isset($link_attributes['content-pre'])) ? $link_attributes['content-pre'] : '').
			$page->title.
			((isset($link_attributes['content-post'])) ? $link_attributes['content-post'] : '').
			((isset($link_haschildren_attributes) && isset($link_haschildren_attributes['content-post'])) ? $link_haschildren_attributes['content-post'] : '').
			'</a>';

			$html .= '>'; // close first li
			$html .= $link;
			$html .= $sub;
			$html .= '</li>';
		}

		return $html;
	}

	public function nestedAttributes(array $nested_attributes, $this_element, $this_level, $this_id = null, $this_slug = null)
	{
		$this_element = strtolower($this_element);

		foreach ($nested_attributes as $attribute)
		{
			// check if the required array elements are given
			if (is_array($attribute) && isset($attribute['element']) && isset($attribute['attributes']) && is_array($attribute['attributes']))
			{
				if (strtolower($attribute['element']) == $this_element) // match element
				{
					if (isset($attribute['limits'])) // see if limits are set
					{
						// explode multiple limits
						$attribute['limits'] = explode('|', $attribute['limits']);

						$and_match = false;

						foreach ($attribute['limits'] as $limit)
						{
							if ($limit == '&')
							{
								$and_match = true; // if we come across &, then treat the remaining limits with AND
							}
							elseif (ctype_digit($limit)) // integer limit (single level), ex. '3'
							{
								if (intval($limit) == $this_level)
								{
									$matched = true;
									if (!$and_match) break;
								}
								elseif ($and_match)
								{
									$matched = false; break;
								}
							}
							elseif (substr($limit, -1, 1) == '-') // integer upper bound (inclusive), ex. '3-'
							{
								if (intval(substr($limit, 0, -1)) >= $this_level)
								{
									$matched = true;
									if (!$and_match) break;
								}
								elseif ($and_match)
								{
									$matched = false; break;
								}
							}
							elseif (substr($limit, -1, 1) == '+') // integer lower bound (inclusive), ex. '3+'
							{
								if (intval(substr($limit, 0, -1)) <= $this_level)
								{
									$matched = true;
									if (!$and_match) break;
								}
								elseif ($and_match)
								{
									$matched = false; break;
								}
							}
							elseif (count(($lowerupper = explode('-', $limit))) == 2)  // integer range (inclusive), ex. '2-4'
							{
								if (intval($lowerupper[0]) <= $this_level && intval($lowerupper[1]) >= $this_level)
								{
									$matched = true;
									if (!$and_match) break;
								}
								elseif ($and_match)
								{
									$matched = false; break;
								}
							}
							elseif ($this_id && strtolower(substr($limit, 0, 3)) == 'id=') // equals given id, if given
							{
								if (substr($limit, 3) == $this_id)
								{
									$matched = true;
									if (!$and_match) break;
								}
								elseif ($and_match)
								{
									$matched = false; break;
								}
							}
							elseif ($this_slug && strtolower(substr($limit, 0, 5)) == 'slug=') // equals given slug, if given
							{
								if (substr($limit, 5) == $this_slug)
								{
									$matched = true;
									if (!$and_match) break;
								}
								elseif ($and_match)
								{
									$matched = false; break;
								}
							}
						}
					}
					else
					{
						$matched = true;
					}

					if (isset($matched) && $matched)
					{
						return $attribute['attributes']; // it's a match and the limits are good, so return the given attributes for this element
					}
				}
			}
		}

		return array();
	}
}