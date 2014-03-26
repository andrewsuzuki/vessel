<?php namespace Hokeo\Vessel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

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

		// Constant for easy determination of Laravel 4.1.x vs. 4.0.x
		$this->app['hokeo.vessel.4.1'] = version_compare(\Illuminate\Foundation\Application::VERSION, '4.1') > -1;

		include __DIR__.'/../../errors.php';
		include __DIR__.'/../../routes.php';
		include __DIR__.'/../../filters.php';
		include __DIR__.'/../../macros.php';
		include __DIR__.'/../../validators.php';
		include __DIR__.'/../../events.php';
		include __DIR__.'/../../composers.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Hokeo\Vessel\FormatterInterface', function() {
			return new Formatter\Blade(
				$this->app->make('files'),
				$this->app->make('path.storage') . '/views'
				);
		});

		$this->app->bind('Hokeo\Vessel\EngineInterface', 'Hokeo\Vessel\Engine\Blade');

		$this->app->singleton('hokeo.vessel.vessel',    'Hokeo\\Vessel\\Vessel');
		$this->app->singleton('hokeo.vessel.formatter', 'Hokeo\\Vessel\\Formatter');
		$this->app->singleton('hokeo.vessel.asset',     'Hokeo\\Vessel\\Asset');

		$app = $this->app;

		$this->app->before(function() use ($app) {

		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
    		'Hokeo\Vessel\FormatterInterface',
    		'Hokeo\Vessel\EngineInterface',
    		'hokeo.vessel.vessel',
    		'hokeo.vessel.formatter',
    	];
	}

}
