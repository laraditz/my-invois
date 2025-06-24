<?php

namespace Laraditz\MyInvois\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MyinvoisClient extends Model
{
    protected $fillable = ['id', 'owner_type', 'owner_id', 'name', 'secret'];

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    protected function casts(): array
    {
        return [
            'secret' => 'hashed',
        ];
    }

    protected $hidden = [
        'secret'
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function accessToken(): HasOne
    {
        return $this->hasOne(MyinvoisAccessToken::class, 'client_id');
    }
}
