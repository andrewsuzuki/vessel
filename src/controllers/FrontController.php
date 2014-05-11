<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\View\Environment;

class FrontController extends Controller {

	protected $app;

	protected $config;

	protected $view;

	protected $mm;

	protected $pagehelper;

	protected $blockhelper;

	protected $fm;

	protected $theme;

	protected $page; // model

	public function __construct(
		Application $app,
		Repository $config,
		Environment $view,
		MenuManager $mm,
		PageHelper $pagehelper,
		BlockHelper $blockhelper,
		FormatterManager $fm,
		Theme $theme,
		Page $page)
	{
		$this->app         = $app;
		$this->config      = $config;
		$this->view        = $view;
		$this->mm          = $mm;
		$this->pagehelper  = $pagehelper;
		$this->blockhelper = $blockhelper;
		$this->fm          = $fm;
		$this->theme       = $theme;
		$this->page        = $page;
	}

	/**
	 * Get page on front end
	 * 
	 * @param string $all Request path (from route)
	 */
	public function getPage($all)
	{
		if ($all == '/') // if this is home...
		{
			$home = $this->config->get('vset::site.home'); // get home page from config
			$main = $this->page->find($home); // get home page by id
			if (!$home || !$main || !$main->isRoot()) throw new \VesselFrontNotFoundException; // make sure home exists & is root
			$hierarchy = array($main->slug);
		}
		else
		{
			$hierarchy = explode('/', $all); // explode request path

			// get last page slug's page
			$main = $this->page->where('slug', end($hierarchy))->first();
			reset($hierarchy);
		}

		// load theme with name from settings, with no fallbacks
		$theme_good = $this->theme->load($this->config->get('vset::site.theme'), false);
		// if all fails...
		if (!$theme_good) throw new \VesselFrontNotFoundException;

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
				if (count($hierarchy) > 1) $valid = false;
			}
			
			if ($valid)
			{
				if ($this->fm->registered($main->formatter))
				{
					// Create menu
					$menu = $this->mm->handler('vessel.menu.front', array('class' => 'nav navbar-nav'));

					$menu->add('/', 'Home')
					->add('/about', 'About')
					->add('#', 'More', $this->mm->items('more')
						->add('/blog', 'Blog')
						);

					$this->mm->handler('vessel.menu.front')->getItemsAtDepth(0)->map(function($item)
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
						'site-title' => function() {
							return $this->config->get('vset::site.title', '');
						},
						'page-title' => function() use ($main) {
							return $main->title;
						},
						'description' => function($call) use ($main) {
							if ($main->description)
								return $main->description;
							else
								return $this->config->get('vset::site.description', '');
						},
						'site-desc' => function() {
							return $this->config->get('vset::site.description', '');
						},
						'page-desc' => function() use ($main) {
							return $main->description;
						},
						'menu' => function($call, $name) use ($main) {
							return $this->mm->getSavedMenu($name);
						},
						'block' => function($call, $slug) {
							return $this->blockhelper->display($slug);
						},
					]);

					$formatter = $this->fm->get($main->formatter);

					$this->theme->setElement([
						'content' => function() use ($main, $formatter) {
							return $formatter->make($main->raw, $main->made);
						},
					]);

					$view_name = ($main->template && $main->template !== 'none') ? $main->template.'_template' : 'template'; // use sub-template if specified
					if (!$this->view->exists('vessel-theme::'.$view_name)) $view_name = 'template'; // revert to default template if it doesn't exist

					return $view = $this->view->make('vessel-theme::'.$view_name);
				}
			}
		}

		throw new \VesselFrontNotFoundException;
	}

}
