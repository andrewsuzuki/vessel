<?php namespace Hokeo\Vessel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class VesselServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('hokeo/vessel');

		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Hokeo\Vessel\CompilerInterface', function() {
			return new Compiler\Blade(
				$this->app->make('files'),
				$this->app->make('path.storage') . '/views'
				);
		});

		$this->app->bind('Hokeo\Vessel\EngineInterface', 'Hokeo\Vessel\Engine\Blade');

		$this->app->bind('Hokeo\Vessel\VesselInterface', function() {
			return new Vessel;
		});

		$app = $this->app;

		// $this->app->finish(function() use ($app) {
		// 	echo 'hey';
		// });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
    		"Hokeo\Vessel\CompilerInterface",
    		"Hokeo\Vessel\EngineInterface",
    	];
	}

}
