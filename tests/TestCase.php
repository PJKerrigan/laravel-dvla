<?php

declare(strict_types=1);

namespace Kerrigan\Dvla\Tests;

use Illuminate\Foundation\Application;
use Kerrigan\Dvla\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            \Spatie\LaravelData\LaravelDataServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     */
    protected function defineEnvironment($app)
    {
        // Include the spatie/laravel-data configuration.
        $app['config']->set('data', require __DIR__ . '/../vendor/spatie/laravel-data/config/data.php');
    }
}