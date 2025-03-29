<?php

namespace TecnoCampos\DynamicModelFilter;

use Illuminate\Support\ServiceProvider;

class RequestFiltersServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'dynamicfilters');
    }
}
