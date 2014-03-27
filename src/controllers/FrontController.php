<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class FrontController extends Controller {

	protected $pageController;

	public function __construct(PageController $pageController)
	{
		$this->pageController = $pageController;
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

		if ($main)
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
				ThemeFacade::setElement([
					['page-title', function() use ($main) { return $main->title; }],
					['content', $this->pageController->evalContent($main->id)],
				]);

				return View::make('vessel-themes::suzuki.template');
				// $compiler = App::make('vessel.blade.compiler');
				// return $compiler->compileString(App::make('files')->get('vessel-themes::suzuki.template'));
			}
		}

		App::abort(404);
	}

}