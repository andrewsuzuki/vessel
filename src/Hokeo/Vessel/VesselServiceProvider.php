<?php namespace Hokeo\Vessel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Html\HtmlBuilder;
use Menu\Menu;
use Illuminate\Support\ClassLoader;

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
		$this->package('hokeo/vessel', null, __DIR__.'/../..');

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

		// clone Philf/Setting and configure
		// $this->app['vessel.setting'] = $this->app->make('setting');
		// $this->app['vessel.setting']->path($this->app['vessel.vessel']->path('/'));
		// $this->app['vessel.setting']->filename('settings.json');

		include __DIR__.'/../../errors.php'; // errors
		include __DIR__.'/../../routes.php'; // routes
		include __DIR__.'/../../filters.php'; // filters
		include __DIR__.'/../../macros.php'; // html/form macros
		include __DIR__.'/../../validators.php'; // validators
		include __DIR__.'/../../events.php'; // events
		include __DIR__.'/../../composers.php'; // composer and creators
		include __DIR__.'/../../misc.php'; // blade extensions, helper functions, etc
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register dependencies
		$dependent_provides = [
			'Krucas\Notification\NotificationServiceProvider',
			'Philf\Setting\SettingServiceProvider',
			'Zizaco\Entrust\EntrustServiceProvider',
			'Menu\MenuServiceProvider',
		];

		foreach ($dependent_provides as $provider)
		{
			if (!class_exists($provider)) throw new \Exception('Vessel dependency '.$provider.' was not found.');
			$this->app->register($provider);
		}

		// IoC Bindings
		// // 
		// $this->app->singleton('vessel.vessel',    'Hokeo\\Vessel\\Vessel');
		// $this->app->singleton('vessel.setting',   'Hokeo\\Vessel\\Setting');
		// $this->app->singleton('vessel.plugin',    'Hokeo\\Vessel\\Plugin');
		// $this->app->singleton('vessel.formatter', 'Hokeo\\Vessel\\Formatter');
		// $this->app->singleton('vessel.asset',     'Hokeo\\Vessel\\Asset');
		// $this->app->singleton('vessel.pagehelper','Hokeo\\Vessel\\PageHelper');
		
		$this->app->bindShared('vessel.vessel', function($app) {
			return new Vessel($app['app'], new HtmlBuilder, $app['url'], new Menu);
		});

		$this->app->bindShared('vessel.setting', function($app) {
			return new Setting($app['vessel.vessel']);
		});

		$this->app->bindShared('vessel.plugin', function($app) {
			return new Plugin($app['app'], $app['config'], new ClassLoader, $app['files'], $app['vessel.setting']);
		});

		$this->app->bindShared('vessel.formatter', function($app) {
			echo 'FORMATTER CREATED';
			return new Formatter($app['app'], $app['blade.compiler']);
		});

		$this->app->bindShared('vessel.asset', function($app) {
			return new Asset();
		});

		$this->app->bindShared('vessel.pagehelper', function($app) {
			echo 'PAGEHELPER CREATED';
			return new PageHelper(
				$app['vessel.vessel'],
				$app['vessel.formatter'],
				$app['files'],
				$app['request'],
				$app['redirect'],
				$app['auth'],
				$app['validator'],
				$app['notification']
				);
		});

		$this->app->make('vessel.vessel'); // construct
		// exit;
		// $this->app['vessel.plugin']->getAvailable(true); // enable all plugins
		$this->app['vessel.plugin']->enableAll(); // enable all plugins

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
			'vessel.setting',
			'vessel.vessel',
			'vessel.plugin',
			'vessel.formatter',
			'vessel.asset',
			'vessel.theme',
		];
	}

}
