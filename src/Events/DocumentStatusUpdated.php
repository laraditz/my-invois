<?php

namespace Laraditz\MyInvois\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Laraditz\MyInvois\Models\MyinvoisDocument;

class DocumentStatusUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public MyinvoisDocument $myinvoisDocument,
    ) {
        //
    }
}