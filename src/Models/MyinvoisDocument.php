<?php

namespace Laraditz\MyInvois\Models;

use Laraditz\MyInvois\Enums\Format;
use Illuminate\Database\Eloquent\Model;
use Laraditz\MyInvois\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyinvoisDocument extends Model
{
    protected $fillable = [
        'client_id',
        'request_id',
        'code_number',
        'type',
        'format',
        'file_name',
        'file_path',
        'disk',
        'hash',
        'submission_uid',
        'uuid',
        'error',
        'error_code',
        'error_message',
        'accepted_at',
        'rejected_at'
    ];

    protected function casts(): array
    {
        return [
            'type' => InvoiceType::class,
            'format' => Format::class,
            'error' => 'json',
            'accepted_at' => 'timestamp',
            'rejected_at' => 'timestamp',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(MyinvoisRequest::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(MyinvoisClient::class);
    }

    protected function scopeIsAccepted(Builder $query): void
    {
        $query->whereNotNull('accepted_at');
    }
}
