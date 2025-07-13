<?php

namespace Laraditz\MyInvois\Data;

use Laraditz\MyInvois\Traits\HasAttributes;
use Laraditz\MyInvois\Contracts\WithAttributes;

class IdentificationCode implements WithAttributes
{
    use HasAttributes;

    public function __construct(
        public string $value,
        public ?string $listID = null,
        public ?string $listAgencyID = null,
    ) {
        $attributes = [];

        if ($listID) {
            $attributes['listID'] = $listID;
        }

        if ($listAgencyID) {
            $attributes['listAgencyID'] = $listAgencyID;
        }

        if (count($attributes) > 0) {
            $this->setAttributes($attributes);
        }

    }
}
