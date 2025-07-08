<?php

namespace Laraditz\MyInvois\Services;

use Laraditz\MyInvois\Exceptions\MyInvoisException;

class TaxpayerService extends BaseService
{
    public function beforeValidateRequest()
    {
        $payload = $this->getPayload();

        $tin = data_get($payload, 'tin');
        $idType = data_get($payload, 'idType');
        $idValue = data_get($payload, 'idValue');

        throw_if(!($tin && $idType && $idValue), MyInvoisException::class, __('Missing one or more parameters.'));

        $this->setParams(['tin' => $tin]);
        $this->setQueryString(['idType' => $idType, 'idValue' => $idValue]);
    }
}