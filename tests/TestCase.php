<?php

namespace Laraditz\MyInvois\Tests;

use Laraditz\MyInvois\MyInvoisServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            MyInvoisServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        // Excludes 2025_07_08_124725_create_myinvois_document_histories_table.php:
        // it runs a raw `CREATE TABLE ... LIKE ...` statement that is MySQL-only
        // and fails under SQLite. Not needed by this package's test suite; left
        // as a pre-existing issue outside this work's scope.
        $migrations = [
            '2025_06_24_125038_create_myinvois_clients_table.php',
            '2025_06_24_125156_create_myinvois_access_tokens_table.php',
            '2025_06_24_132939_create_myinvois_requests_table.php',
            '2025_07_07_052321_create_myinvois_documents_table.php',
            '2025_07_13_203021_create_myinvois_msic_codes_table.php',
            '2025_07_17_224050_create_myinvois_measure_units_table.php',
            '2025_08_20_131624_add_soft_deletes_to_myinvois_documents_table.php',
            '2025_08_20_162647_add_on_behalf_of_to_myinvois_requests_table.php',
        ];

        foreach ($migrations as $file) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/' . $file);
        }
    }
}
