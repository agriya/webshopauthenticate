<?php namespace Agriya\Webshopauthenticate;

use Illuminate\Support\ServiceProvider;

class WebshopauthenticateServiceProvider extends ServiceProvider {

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
		$this->package('agriya/webshopauthenticate');
		// Register the Sentry Service Provider
        $this->app->register('Cartalyst\Sentry\SentryServiceProvider');
		include __DIR__.'/../../filters.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['webshopauthenticate'] = $this->app->share(function($app)
		  {
		    return new Webshopauthenticate;
		  });
		$this->app->booting(function()
		{
		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
		  $loader->alias('Webshopauthenticate', 'Agriya\Webshopauthenticate\Facades\Webshopauthenticate');
		  $loader->alias('Sentry', 'Cartalyst\Sentry\Facades\Laravel\Sentry');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
