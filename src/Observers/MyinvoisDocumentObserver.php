<?php

namespace Laraditz\MyInvois\Observers;

use Laraditz\MyInvois\Events\DocumentStatusUpdated;
use Laraditz\MyInvois\Models\MyinvoisDocument;

class MyinvoisDocumentObserver
{
    public function updated(MyinvoisDocument $myinvoisDocument): void
    {
        if ($myinvoisDocument->wasChanged('status')) {
            event(new DocumentStatusUpdated($myinvoisDocument));
        }
    }
}