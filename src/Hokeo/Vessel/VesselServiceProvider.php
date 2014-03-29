<?php namespace Hokeo\Vessel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Adapter\Local as FlyAdapter;

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
		$this->package('hokeo/vessel', 'vessel', __DIR__.'/../../');

		// Constant for easy determination of Laravel 4.1.x vs. 4.0.x
		$this->app['vessel.laravel.4.1'] = version_compare(\Illuminate\Foundation\Application::VERSION, '4.1') > -1;

		// Version
		$this->app['vessel.version.major'] = '0';
		$this->app['vessel.version.minor'] = '5';
		$this->app['vessel.version.patch'] = '0';
		$this->app['vessel.version.short'] = $this->app['vessel.version.major'].'.'.$this->app['vessel.version.minor'];
		$this->app['vessel.version.full']  = $this->app['vessel.version.short'].'.'.$this->app['vessel.version.patch'];
		$this->app['vessel.version']       = $this->app['vessel.version.full'];

		$this->app->singleton('vessel.theme', 'Hokeo\\Vessel\\Theme');
		$this->app->make('vessel.theme'); // construct

		include __DIR__.'/../../errors.php';
		include __DIR__.'/../../routes.php';
		include __DIR__.'/../../filters.php';
		include __DIR__.'/../../macros.php';
		include __DIR__.'/../../validators.php';
		include __DIR__.'/../../events.php';
		include __DIR__.'/../../composers.php';
		include __DIR__.'/../../misc.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('vessel.vessel',    'Hokeo\\Vessel\\Vessel');
		$this->app->singleton('vessel.plugin',    'Hokeo\\Vessel\\Plugin');
		$this->app->singleton('vessel.formatter', 'Hokeo\\Vessel\\Formatter');
		$this->app->singleton('vessel.asset',     'Hokeo\\Vessel\\Asset');

		$this->app->bind('Hokeo\Vessel\FilesystemInterface', function($app, array $params) {
			if (!isset($params['path'])) $params['path'] = $this->app->make('vessel.vessel')->path('/');
			return new Filesystem(new FlyAdapter($params['path']));
		});

		$this->app->make('vessel.vessel'); // construct

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
			'vessel.vessel',
			'vessel.plugin',
			'vessel.formatter',
			'vessel.asset',
			'vessel.theme',
			'Hokeo\Vessel\FilesystemInterface',
		];
	}

}
