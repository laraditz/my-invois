<?php

namespace Laraditz\MyInvois\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;
use Laraditz\MyInvois\Models\MyinvoisMsicCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MyinvoisMsicCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $MSICCodes = File::json(__DIR__ . '/../data/MSICSubCategoryCodes.json');

        LazyCollection::make($MSICCodes)
            ->chunk(500)
            ->each(function ($chunk) {
                MyinvoisMsicCode::insert(
                    $chunk->map(function ($item) {
                        $category = data_get($item, 'MSIC Category Reference');

                        return [
                            'code' => data_get($item, 'Code'),
                            'description' => data_get($item, 'Description'),
                            'category' => $category && $category !== '' ? $category : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })->toArray()
                );
            });

    }
}
