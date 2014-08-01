<?php namespace Wikichua\Ga;

use Illuminate\Support\ServiceProvider;
use App,
	Config,
	File;

class GaServiceProvider extends ServiceProvider {

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
		$this->package('wikichua/ga');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		App::bind('ga',function($app){
			if(!File::exists(Config::get('ga::ga.SECURITYCERT')))
			{
				throw new \Exception("Missing Security Cert in " . Config::get('ga::ga.SECURITYCERT'));
			}

			return new Ga();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('ga');
	}

}
