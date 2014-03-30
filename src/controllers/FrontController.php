<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Menu\Menu;

class FrontController extends Controller {

	protected $pagehelper;

	public function __construct(PageHelper $pagehelper, Menu $menu)
	{
		$this->pagehelper = $pagehelper;
		$this->menu = $menu;
	}

	/**
	 * Get page on front end
	 * 
	 * @param  string $all Request path (from route)
	 */
	public function getPage($all)
	{
		// explode request path
		$hierarchy = explode('/', $all);

		// get last page slug's page
		$main = Page::where('slug', end($hierarchy))->first();
		reset($hierarchy);

		if ($main && $main->visible)
		{
			$valid = true;

			// if the page requires a nested url, validate the given nest
			if ($main->nest_url)
			{
				$ancestors = $main->getAncestors();

				foreach ($ancestors as $i => $page)
				{
					if ($page->slug !== $hierarchy[$i])
					{
						$valid = false;
						break;
					}
				}
			}
			// otherwise, make sure there isn't a nest
			else
			{
				if (count($hierarchy) > 1)
				{
					$valid = false;
				}
			}
			
			if ($valid)
			{
				$menu = $this->menu->handler('vessel.menu.front', array('class' => 'nav navbar-nav'));

				$menu->add('/', 'Home')
				->add('/about', 'About')
				->add('#', 'More', $this->menu->items('more')
					->add('/blog', 'Blog'));

				// $menu->hydrate(function()
				// {
				// 	return Menu::where('type', '=', 'topmenu')
				// 	->orderBy('order', 'asc')
				// 	->get();
				// },
				// function($children, $item)
				// {
				// 	if($item->is_seperator)
				// 	{
				// 		$children->raw('')->onItem()->addClass('seperator');
				// 	}
				// 	else
				// 	{
				// 		$children->add($item->name, $item->content, Menu::items($item->name));
				// 	}
				// });

				$this->menu->handler('vessel.menu.front')->getItemsAtDepth(0)->map(function($item)
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


				Facades\Theme::setElement([
					['page-title', function() use ($main) {
						return $main->title;
					}],

					['menu', function($call, $name) use ($main) {
						return \Menu\Menu::handler('vessel.menu.'.$name)->render();
					}],

					['content', $this->pagehelper->evalContent($main->id)],
				]);

				return View::make('vessel-themes::suzuki.template');
			}
		}

		App::abort(404);
	}

}