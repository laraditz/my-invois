<?php

namespace Laraditz\MyInvois\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MyinvoisRequest extends Model
{
    use HasUuids;

    protected $fillable = ['action', 'url', 'payload', 'http_code', 'response', 'error', 'error_code', 'error_message', 'error_description'];

    protected function casts(): array
    {
        return [
            'payload' => 'json',
            'response' => 'json',
        ];
    }
}
