<?php

namespace Laraditz\MyInvois;

use LogicException;
use BadMethodCallException;
use Illuminate\Support\Str;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Enums\Format;
use Laraditz\MyInvois\Models\MyinvoisAccessToken;
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;

class MyInvois
{
    private $services = ['auth', 'document_type', 'taxpayer', 'notification', 'document'];

    public function __construct(
        private string $client_id,
        private string $client_secret,
    ) {
    }

    public function __call($method, $arguments)
    {
        throw_if(!$this->getClientId(), LogicException::class, __('Missing Client ID.'));
        throw_if(!$this->getClientSecret(), LogicException::class, __('Missing Client Secret.'));

        if (count($arguments) > 0) {
            $argumentCollection = collect($arguments);

            try {
                $argumentCollection->keys()->ensure('string');
            } catch (\Throwable $th) {
                // throw $th;
                throw new LogicException(__('Please pass a named arguments in :method method.', ['method' => $method]));
            }
        }

        $property_name = Str::of($method)->snake()->lower()->value;

        if (in_array($property_name, $this->services)) {
            $reformat_property_name = ucfirst(Str::camel($method));

            $service_name = 'Laraditz\\MyInvois\\Services\\' . $reformat_property_name . 'Service';

            return new $service_name(app('myinvois'));
        } else {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.',
                get_class(),
                $method
            ));
        }
    }

    public function getClientId(): string
    {
        return $this->client_id;
    }

    public function getClientSecret(): string
    {
        return $this->client_secret;
    }

    public function isSandbox(): bool
    {
        return $this->config('sandbox.mode');
    }

    public function config(string $name): array|string|int|bool
    {
        return config('myinvois.' . $name);
    }

    public function getAccessToken(): ?string
    {
        $accessTokenModel = MyinvoisAccessToken::query()
            ->where('client_id', $this->getClientId())
            ->hasNotExpired()
            ->first();



        if ($accessTokenModel) {
            return $accessTokenModel->access_token;
        } else {
            $myinvois = MyInvois::auth()->token(
                client_id: $this->getClientId(),
                client_secret: $this->getClientSecret(),
                grant_type: 'client_credentials',
                scope: 'InvoicingAPI'
            );

            $accessToken = data_get($myinvois, 'access_token');

            if (!$accessToken) {
                throw new MyInvoisApiError($result ?? ['code' => __('Missing an access token')]);
            }

            return $accessToken;
        }
    }

    public function generateDocument($data, Format $format)
    {
        return match ($format) {
            Format::XML => $this->generateXMLDocument($data),
            Format::JSON => $this->generateJSONDocument($data),
        };
    }

    public function generateXMLDocument($data)
    {
        $service = new \Sabre\Xml\Service();

        $service->namespaceMap = [
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2' => '',
            XMLNS::CAC->getNamespace() => XMLNS::CAC(),
            XMLNS::CBC->getNamespace() => XMLNS::CBC(),
        ];

        return $service->write('Invoice', $data->toXmlArray());
    }

    public function generateJSONDocument($data)
    {

    }
}
