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

		include __DIR__.'/../../errors.php'; // errors (classes)
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
			'Baum\\BaumServiceProvider',
			'Intervention\\Image\\ImageServiceProvider',
			'Menu\\MenuServiceProvider',
			'Andrewsuzuki\\Perm\\PermServiceProvider'
		];

		foreach ($dependent_provides as $provider)
		{
			if (!class_exists($provider)) throw new \Exception('Vessel dependency '.$provider.' was not found.');
			$this->app->register($provider);
		}

		// add namespace for vessel settings (perm)
		$this->app['config']->addNamespace('vset', app_path('config/vessel'));

		// register error handlers
		$this->registerErrorHandlers();

		// IoC Bindings
		
		$this->bindModels();

		$this->app->bindShared('Hokeo\\Vessel\\Vessel', function($app) {
			return new Vessel(
				$app['app'],
				$app['config'],
				$app['files']
				);
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

		$this->app->bindShared('Hokeo\\Vessel\\MenuManager', function($app) {
			return new MenuManager(
				$app['html'],
				$app['url'],
				$app['Hokeo\\Vessel\\Plugin'],
				$app['Hokeo\\Vessel\\Menu'],
				$app['Hokeo\\Vessel\\Menuitem'],
				$app['Hokeo\\Vessel\\Page']
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
				$app['config'],
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

		$this->app->bindShared('Hokeo\\Vessel\\MediaHelper', function($app) {
			return new MediaHelper(
				$app['url'],
				$app['files'],
				$app['config'],
				$app['image'],
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

		$this->bindControllers();

		$this->app->make('Hokeo\\Vessel\\Vessel'); // construct

		$this->app['Hokeo\\Vessel\\Plugin']->enableAll(); // enable all plugins

		// route matched event
		$this->app['router']->matched(function($route, $request) {
			// determine if we're in front or back based on route name, and set VESSEL_FRONT boolean
			if (!defined('VESSEL_FRONT'))
			{
				define('VESSEL_FRONT', (($this->app['request']->is($this->app['config']->get('vessel::vessel.uri', 'vessel').'/*')) ? false : true));
			}
		});
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
				$app['config'],
				$app['view'],
				$app['Hokeo\\Vessel\\MenuManager'],
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

		$this->app->bind('Hokeo\\Vessel\\MenuController', function($app) {
			return new MenuController(
				$app['view'],
				$app['request'],
				$app['validator'],
				$app['auth'],
				$app['config'],
				$app['notification'],
				$app['redirect'],
				$app['Hokeo\\Vessel\\Asset'],
				$app['Hokeo\\Vessel\\Plugin'],
				$app['Hokeo\\Vessel\\MenuManager'],
				$app['Hokeo\\Vessel\\Menu']
				);
		});
		
		$this->app->bind('Hokeo\\Vessel\\MediaController', function($app) {
			return new MediaController(
				$app['view'],
				$app['url'],
				$app['request'],
				$app['files'],
				$app['auth'],
				$app['config'],
				new \Illuminate\Support\Facades\Response,
				$app['Hokeo\\Vessel\\Plugin'],
				$app['Hokeo\\Vessel\\Asset'],
				$app['Hokeo\\Vessel\\MediaHelper']
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
				$app['Hokeo\\Vessel\\User'],
				$app['Hokeo\\Vessel\\Role'],
				$app['Hokeo\\Vessel\\Permission']
				);
		});

		$this->app->bind('Hokeo\\Vessel\\SettingController', function($app) {
			return new SettingController(
				$app['view'],
				$app['request'],
				$app['validator'],
				$app['redirect'],
				$app['notification'],
				$app['Andrewsuzuki\\Perm\\Perm'],
				$app['Hokeo\\Vessel\\Theme'],
				$app['Hokeo\\Vessel\\PageHelper']
				);
		});
	}

	/**
	 * Binds models to IoC
	 * 
	 * @return void
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
			'Menu',
			'Menuitem'
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
	 * Register error handlers
	 * 
	 * @return response
	 */
	protected function registerErrorHandlers()
	{
		if ($this->app['config']->get('app.debug')) return; // if debugging is on, handle as normal (debug)

		$exception_handler = function() {
			if (defined('VESSEL_FRONT'))
			{
				$this->app['view']->share('title', 'Error');
				// get admin 404 if it's on the back and we're logged in, otherwise get theme 404
				$view = (VESSEL_FRONT || !$this->app['auth']->check()) ? 'vessel-theme::unknown' : 'vessel::errors.unknown';
				// if view exists, make+return
				if ($this->app['view']->exists($view)) return $this->app['view']->make($view);
			}

			return 'An unknown error occurred.'; // fallback
		};

		$notfound_handler = function() {
			if (defined('VESSEL_FRONT'))
			{
				$this->app['view']->share('title', '404 Not Found');
				// get admin 404 if it's on the back and we're logged in, otherwise get theme 404
				$view = (VESSEL_FRONT || !$this->app['auth']->check()) ? 'vessel-theme::404' : 'vessel::errors.404';
				// if view exists, make+return
				if ($this->app['view']->exists($view)) return $this->app['view']->make($view);
			}

			return '404 Not Found.'; // fallback
		};

		$this->app->fatal($exception_handler);

		$this->app->error(function(\Exception $e) use ($exception_handler) {
			return $exception_handler();
		});

		$this->app->error(function(\VesselFrontNotFoundException $e) use ($notfound_handler) {
			return $notfound_handler();
		});

		$this->app->error(function(\VesselBackNotFoundException $e) use ($notfound_handler) {
			return $notfound_handler();
		});
		
		$this->app->missing($notfound_handler);
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
