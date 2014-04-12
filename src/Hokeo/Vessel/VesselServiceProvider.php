<?php namespace Hokeo\Vessel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Html\HtmlBuilder;
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

		// Semantic versioning components
		$this->app['vessel.version.major'] = '0';
		$this->app['vessel.version.minor'] = '5';
		$this->app['vessel.version.patch'] = '0';
		$this->app['vessel.version.short'] = $this->app['vessel.version.major'].'.'.$this->app['vessel.version.minor'];
		$this->app['vessel.version.full']  = $this->app['vessel.version.short'].'.'.$this->app['vessel.version.patch'];
		$this->app['vessel.version']       = $this->app['vessel.version.full'];

		include __DIR__.'/../../errors.php'; // errors
		include __DIR__.'/../../routes.php'; // routes
		include __DIR__.'/../../filters.php'; // filters
		include __DIR__.'/../../macros.php'; // html/form macros
		include __DIR__.'/../../validators.php'; // validators
		include __DIR__.'/../../events.php'; // events
		include __DIR__.'/../../composers.php'; // composer and creators
		include __DIR__.'/../../observers.php'; // model observers
		include __DIR__.'/../../misc.php'; // blade extensions, helper functions, etc
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// set config package hint
		$this->app['config']->package('hokeo/vessel', __DIR__.'/../../config');

		// Register dependencies
		$dependent_provides = [
			'Krucas\\Notification\\NotificationServiceProvider',
			'Zizaco\\Entrust\\EntrustServiceProvider',
			'Menu\\MenuServiceProvider',
			'Andrewsuzuki\\Perm\\PermServiceProvider'
		];

		foreach ($dependent_provides as $provider)
		{
			if (!class_exists($provider)) throw new \Exception('Vessel dependency '.$provider.' was not found.');
			$this->app->register($provider);
		}

		// set some dependency config values
		
		$this->app['config']->set('entrust::role', '\\Hokeo\\Vessel\\Role');
		$this->app['config']->set('entrust::permission', '\\Hokeo\\Vessel\\Permission');

		// IoC Bindings
		
		$this->bindModels();

		$this->app->bindShared('Hokeo\\Vessel\\Vessel', function($app) {
			return new Vessel($app['app']);
		});

		$this->app->bindShared('Hokeo\\Vessel\\Plugin', function($app) {
			return new Plugin(
				$app['app'],
				$app['config'],
				new ClassLoader,
				$app['files'],
				$app['Andrewsuzuki\\Perm\\Perm']
				);
		});

		$this->app->bindShared('Hokeo\\Vessel\\Menu', function($app) {
			return new Menu(
				$app['html'],
				$app['url'],
				$app['Hokeo\\Vessel\\Plugin']
				);
		});

		$this->app->bindShared('Hokeo\\Vessel\\FormatterManager', function($app) {
			return new FormatterManager(
				$app['app'],
				$app['blade.compiler'],
				$app['Hokeo\\Vessel\\Plugin']
				);
		});

		$this->app->bindShared('Hokeo\\Vessel\\Asset', function($app) {
			return new Asset;
		});

		$this->app->bindShared('Hokeo\\Vessel\\PageHelper', function($app) {
			return new PageHelper(
				$app['request'],
				$app['db'],
				$app['redirect'],
				$app['auth'],
				$app['validator'],
				$app['notification'],
				$app['Hokeo\\Vessel\\FormatterManager'],
				$app['Hokeo\\Vessel\\Page'],
				$app['Hokeo\\Vessel\\Pagehistory'],
				$app['Hokeo\\Vessel\\Plugin']
				);
		});

		$this->app->bindShared('Hokeo\\Vessel\\BlockHelper', function($app) {
			return new BlockHelper(
				$app['request'],
				$app['redirect'],
				$app['auth'],
				$app['validator'],
				$app['notification'],
				$app['Hokeo\\Vessel\\FormatterManager'],
				$app['Hokeo\\Vessel\\Block'],
				$app['Hokeo\\Vessel\\Plugin']
				);
		});

		$this->app->bindShared('Hokeo\\Vessel\\Theme', function($app) {
			return new Theme($app['app'],
				$app['config'],
				$app['view'],
				$app['files'],
				$app['Andrewsuzuki\\Perm\\Perm'],
				$app['Hokeo\\Vessel\\Plugin']
				);
		});

		$this->app->make('Hokeo\\Vessel\\Vessel'); // construct

		$this->bindControllers();

		$this->app['Hokeo\\Vessel\\Plugin']->enableAll(); // enable all plugins
	}

	/**
	 * Binds controllers to IoC
	 * 
	 * @return void
	 */
	protected function bindControllers()
	{
		$this->app->bind('Hokeo\\Vessel\\FrontController', function($app) {
			return new FrontController(
				$app['app'],
				$app['view'],
				$app['Hokeo\\Vessel\\Vessel'],
				$app['Hokeo\\Vessel\\Menu'],
				$app['Hokeo\\Vessel\\PageHelper'],
				$app['Hokeo\\Vessel\\BlockHelper'],
				$app['Hokeo\\Vessel\\FormatterManager'],
				$app['Hokeo\\Vessel\\Theme'],
				$app['Hokeo\\Vessel\\Page']
				);
		});

		$this->app->bind('Hokeo\\Vessel\\BackController', function($app) {
			return new BackController(
				$app['view'],
				$app['request'],
				$app['redirect'],
				$app['auth'],
				$app['notification']
				);
		});

		$this->app->bind('Hokeo\\Vessel\\PageController', function($app) {
			return new PageController(
				$app['view'],
				$app['request'],
				$app['auth'],
				$app['redirect'],
				$app['notification'],
				$app['Hokeo\\Vessel\\PageHelper'],
				$app['Hokeo\\Vessel\\FormatterManager'],
				$app['Hokeo\\Vessel\\Theme'],
				$app['Hokeo\\Vessel\\Page'],
				$app['Hokeo\\Vessel\\Pagehistory']
				);
		});

		$this->app->bind('Hokeo\\Vessel\\BlockController', function($app) {
			return new BlockController(
				$app['view'],
				$app['request'],
				$app['auth'],
				$app['redirect'],
				$app['notification'],
				$app['Hokeo\\Vessel\\BlockHelper'],
				$app['Hokeo\\Vessel\\FormatterManager'],
				$app['Hokeo\\Vessel\\Theme'],
				$app['Hokeo\\Vessel\\Block']
				);
		});

		$this->app->bind('Hokeo\\Vessel\\UserController', function($app) {
			return new UserController(
				$app['view'],
				$app['request'],
				$app['auth'],
				$app['validator'],
				$app['hash'],
				$app['redirect'],
				$app['notification'],
				$app['Hokeo\\Vessel\\User']
				);
		});
	}

	/**
	 * Binds models to IoC
	 * 
	 * @return type description
	 */
	protected function bindModels()
	{
		$models = array(
			'User',
			'Permission',
			'Role',
			'Page',
			'Pagehistory',
			'Block',
			);

		foreach ($models as $model)
		{
			$this->app->bind('Hokeo\\Vessel\\'.$model, function() use ($model) {
				$model = 'Hokeo\\Vessel\\'.$model;
				return new $model;
			});
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'Hokeo\\Vessel\\Vessel',
			'Hokeo\\Vessel\\Plugin',
			'Hokeo\\Vessel\\FormatterManager',
			'Hokeo\\Vessel\\Asset',
			'Hokeo\\Vessel\\PageHelper',
			'Hokeo\\Vessel\\Theme',
		];
	}

}
