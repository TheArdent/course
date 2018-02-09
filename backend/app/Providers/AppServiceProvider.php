<?php

namespace App\Providers;

use App\Repositories\OSM;
use App\Repositories\Visicom;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
	public function register()
	{
		if ($this->app->environment() !== 'production') {
			$this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
		}
		$this->app->singleton('osm', function ($app) {
		    return new OSM();
        });
		$this->app->singleton('visicom', function ($app) {
		    return new Visicom();
        });
	}
}
