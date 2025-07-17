<?php

namespace Laraditz\MyInvois\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the package's database tables.
     */
    public function run(): void
    {
        $this->call(MyinvoisMsicCodeSeeder::class);
        $this->call(MyinvoisMeasureUnitSeeder::class);
    }
}
