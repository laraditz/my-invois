<?php

namespace Laraditz\MyInvois;

use LogicException;
use BadMethodCallException;
use Illuminate\Support\Str;

class MyInvois
{
    private $services = ['auth'];

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
}
