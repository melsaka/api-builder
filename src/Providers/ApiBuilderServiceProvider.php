<?php

namespace Melsaka\ApiBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Melsaka\ApiBuilder\Commands\ApiCrud;
use Melsaka\ApiBuilder\Commands\ApiScaffold;
use Melsaka\ApiBuilder\Support\StubGenerator;
use Melsaka\ApiBuilder\Support\ModelInspector;
use Melsaka\ApiBuilder\Support\ValidationRuleBuilder;

class ApiBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('api-builder.model-inspector', fn() => new ModelInspector());
        $this->app->singleton('api-builder.rule-builder', fn() => new ValidationRuleBuilder());
        $this->app->singleton('api-builder.stub-generator', fn() => new StubGenerator());
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../stubs' => app_path('Console/Commands/stubs'),
        ], 'api-builder-stubs');
                
        if ($this->app->runningInConsole()) {
            $this->commands([
                ApiCrud::class,
                ApiScaffold::class,
            ]);
        }
    }
}
