<?php

namespace Laraditz\MyInvois\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyinvoisAccessToken extends Model
{
    protected $fillable = ['client_id', 'access_token', 'expires_at', 'type', 'scopes'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(MyinvoisClient::class);
    }

    protected function scopeHasExpired(Builder $query): void
    {
        $query->where('expires_at', '<', now());
    }

    protected function scopeHasNotExpired(Builder $query): void
    {
        $query->where('expires_at', '>=', now());
    }
}
