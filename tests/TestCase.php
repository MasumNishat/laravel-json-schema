<?php

namespace Masum\JsonSchema\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Masum\JsonSchema\SchemaServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SchemaServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'JsonSchema' => \Masum\JsonSchema\Facades\Schema::class,
        ];
    }
}