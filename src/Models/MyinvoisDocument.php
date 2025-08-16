<?php

namespace Laraditz\MyInvois\Models;

use Laraditz\MyInvois\Enums\Format;
use Illuminate\Database\Eloquent\Model;
use Laraditz\MyInvois\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Builder;
use Laraditz\MyInvois\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Laraditz\MyInvois\Observers\MyinvoisDocumentObserver;

#[ObservedBy([MyinvoisDocumentObserver::class])]
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
        'long_id',
        'status',
        'error',
        'error_code',
        'error_message',
        'accepted_at',
        'rejected_at',
        'issued_at',
        'validated_at',
        'cancel_at',
        'reject_request_at',
        'status_reason'
    ];

    protected function casts(): array
    {
        return [
            'type' => InvoiceType::class,
            'format' => Format::class,
            'status' => DocumentStatus::class,
            'error' => 'json',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'issued_at' => 'datetime',
            'validated_at' => 'datetime',
            'cancel_at' => 'datetime',
            'reject_request_at' => 'datetime',
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
