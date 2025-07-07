<?php

namespace Laraditz\MyInvois\Models;

use Laraditz\MyInvois\Enums\Format;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laraditz\MyInvois\Enums\InvoiceType;

class MyinvoisDocument extends Model
{
    protected $fillable = [
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
}
