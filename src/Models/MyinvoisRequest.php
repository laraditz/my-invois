<?php

namespace Laraditz\MyInvois\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyinvoisRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'client_id',
        'action',
        'url',
        'payload',
        'http_code',
        'response',
        'correlation_id',
        'error',
        'error_code',
        'error_message',
        'error_description'
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'json',
            'response' => 'json',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(MyinvoisClient::class);
    }
}
