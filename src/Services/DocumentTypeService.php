<?php

namespace Laraditz\MyInvois\Services;

use Laraditz\MyInvois\Exceptions\MyInvoisException;

class DocumentTypeService extends BaseService
{
    public function beforeGetRequest()
    {
        $payload = $this->getPayload();
        $params = $this->getParams();
        $id = null;

        if (count($payload) > 0) {
            $id = data_get($payload, 0) ?? data_get($payload, 'id');

            if ($id) {
                $this->setParams(['id' => $id]);
            }
        }

        throw_if(!($id || data_get($params, 'id')), MyInvoisException::class, __('Missing id parameter.'));
    }

    public function beforeVersionRequest()
    {
        $payload = $this->getPayload();
        $params = $this->getParams();
        $id = data_get($params, 'id');
        $version = data_get($params, 'vid');

        if (count($payload) === 2) {
            $id = data_get($payload, 0) ?? data_get($payload, 'id');
            $version = data_get($payload, 1) ?? data_get($payload, 'vid');

            if ($id && $version) {
                $this->setParams(['id' => $id, 'vid' => $version]);
            }
        }

        throw_if(!($id && $version), MyInvoisException::class, __('Missing id or vid parameter.'));
    }
}