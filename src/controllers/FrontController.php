<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Application;
use Illuminate\View\Environment;

class FrontController extends Controller {

	protected $app;

	protected $view;

	protected $vessel;

	protected $menu;

	protected $pagehelper;

	protected $fm;

	protected $theme;

	protected $page; // model

	public function __construct(Application $app, Environment $view, Vessel $vessel, Menu $menu, PageHelper $pagehelper, FormatterManager $fm, Theme $theme, Page $page)
	{
		$this->app        = $app;
		$this->view       = $view;
		$this->vessel     = $vessel;
		$this->menu       = $menu;
		$this->pagehelper = $pagehelper;
		$this->fm         = $fm;
		$this->theme      = $theme;
		$this->page       = $page;
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
		$main = $this->page->where('slug', end($hierarchy))->first();
		reset($hierarchy);

		// load theme, with fallback
		$theme_good = $this->theme->load(null, true);
		// if all fails...
		if (!$theme_good) $this->app->abort(404);

		$this->theme->getThemeViews();

		// check that this page exists and is public
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
			// otherwise, make sure there ain't even a nest
			else
			{
				if (count($hierarchy) > 1)
				{
					$valid = false;
				}
			}
			
			if ($valid)
			{
				if ($this->fm->registered($main->formatter))
				{
					// Create menu
					$menu = $this->menu->handler('vessel.menu.front', array('class' => 'nav navbar-nav'));

					$menu->add('/', 'Home')
					->add('/about', 'About')
					->add('#', 'More', $this->menu->items('more')
						->add('/blog', 'Blog'));

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

					$this->theme->setElement([
						['page-title', function() use ($main) {
							return $main->title;
						}],

						['menu', function($call, $name) use ($main) {
							return $this->menu->handler('vessel.menu.'.$name)->render();
						}],

					]);

					$formatter = $this->fm->get($main->formatter);

					$this->theme->setElement([
						['content', function() use ($main, $formatter) {
							return $formatter->fmUse($main->raw, $main->made);
						}],
					]);

					$view_name = ($main->template && $main->template !== 'none') ? $main->template.'_template' : 'template';

					if (!$this->view->exists('vessel-theme::'.$view_name)) $view_name = 'template'; // revert to default template if it doesn't exist

					return $view = $this->view->make('vessel-theme::'.$view_name);
				}
			}
		}

		$this->app->abort(404);
	}

}