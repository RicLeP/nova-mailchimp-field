<?php

namespace Riclep\NovaMailchimpField;

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	    $this->app->booted(function () {
		    $this->routes();
	    });

        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-mailchimp-field', __DIR__.'/../dist/js/field.js');
            Nova::style('nova-mailchimp-field', __DIR__.'/../dist/css/field.css');
        });
    }

	/**
	 * Register the tool's routes.
	 *
	 * @return void
	 */
	protected function routes()
	{
		if ($this->app->routesAreCached()) {
			return;
		}

		Route::prefix('nova-vendor/nova-mailchimp-field')
			->group(__DIR__.'/../routes/api.php');
	}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
