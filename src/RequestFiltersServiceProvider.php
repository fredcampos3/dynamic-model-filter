<?php

namespace TecnoCampos\DynamicModelFilter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class RequestFiltersServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'dynamic-model-filter');
        Blade::componentNamespace('TecnoCampos\\DynamicModelFilter\\Resources\\Views\\Components', 'DMF');
    }
}
