<?php

namespace Laraditz\MyInvois\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;
use Laraditz\MyInvois\Models\MyinvoisMeasureUnit;

class MyinvoisMeasureUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitTypes = File::json(__DIR__ . '/../data/UnitTypes.json');

        LazyCollection::make($unitTypes)
            ->chunk(500)
            ->each(function ($chunk) {
                MyinvoisMeasureUnit::insert(
                    $chunk->map(function ($item) {

                        return [
                            'code' => data_get($item, 'Code'),
                            'name' => data_get($item, 'Name'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })->toArray()
                );
            });
    }
}
