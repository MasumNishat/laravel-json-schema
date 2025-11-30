<?php

namespace Masum\JsonSchema;

use Illuminate\Support\ServiceProvider;

class SchemaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('json-schema', function ($app) {
            return new Schema();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/json-schema.php', 'json-schema'
        );
    }

    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/json-schema.php' => config_path('json-schema.php'),
        ], 'json-schema-config');

        // Publish schemas directory
        $this->publishes([
            __DIR__.'/../schemas/' => storage_path('app/json-schemas'),
        ], 'json-schema-examples');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\MakeSchemaCommand::class,
                Console\ValidateDataCommand::class,
            ]);
        }
    }
}