<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Boot every test with an in-memory database, even when a Docker service
     * exports development database variables to the PHP process.
     */
    public function createApplication(): Application
    {
        $app = parent::createApplication();

        $app->detectEnvironment(static fn (): string => 'testing');
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('database.connections.sqlite.url', null);

        return $app;
    }

    /**
     * Refuse to run database traits unless PHPUnit booted the isolated
     * in-memory connection. This runs before RefreshDatabase can migrate.
     *
     * @return array<class-string, int>
     */
    protected function setUpTraits(): array
    {
        $connection = (string) config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        if (! app()->environment('testing') || $connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                'Tests must use the SQLite in-memory database; refusing to run against a configured application database.',
            );
        }

        return parent::setUpTraits();
    }
}
