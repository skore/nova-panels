<?php

namespace SkoreLabs\NovaPanels;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('custom-panels', __DIR__.'/../dist/js/fields.js');
            // Nova::style('relationship-stack', __DIR__ . '/../dist/css/fields.css');
        });
    }
}
