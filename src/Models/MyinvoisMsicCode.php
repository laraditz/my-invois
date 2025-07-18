<?php

namespace Laraditz\MyInvois\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class MyinvoisMsicCode extends Model
{
    protected $fillable = [
        'code',
        'description',
        'category',
    ];

    protected function code(string $code): ?self
    {
        $key = __FUNCTION__ . $code;
        $seconds = 60 * 60 * 24; // Cache for 1 day

        return Cache::remember($key, $seconds, function () use ($code) {
            return $this->where('code', 'LIKE', $code)->sole();
        });
    }
}
