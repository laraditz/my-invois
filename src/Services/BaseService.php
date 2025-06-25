<?php

namespace Laraditz\MyInvois\Services;

use BadMethodCallException;
use Illuminate\Support\Str;
use Laraditz\MyInvois\MyInvois;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Laraditz\MyInvois\Models\MyinvoisRequest;
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;
use Laraditz\MyInvois\Services\AuthService;

class BaseService
{
    public string $methodName;

    public string $serviceName;

    public string $fqcn;

    public PendingRequest $client;

    public function __construct(
        public MyInvois $myInvois,
        private ?string $route = '',
        private ?string $method = 'get',
        private ?array $queryString = [], // for url query string
        private ?array $payload = [], // for body payload
        private null|array|string|int $params = null, // for path variables
    ) {

        if ($this instanceof AuthService) {
            $this->client = Http::asForm();
        } else {
            $this->client = Http::withHeaders($this->getHeaders());
        }
    }

    public function __call($methodName, $arguments)
    {
        $oClass = new \ReflectionClass(get_called_class());

        $this->fqcn = $oClass->getName();
        $this->serviceName = $oClass->getShortName();
        $this->methodName = $methodName;

        // if method exists, return
        if (method_exists($this, $methodName)) {
            return $this->$methodName($arguments);
        }


        if (in_array(Str::snake($methodName), $this->getAllowedMethods())) {

            if (count($arguments) > 0) {
                $this->setPayload($arguments);
            }

            $this->setRouteFromConfig($this->fqcn, $methodName);

            return $this->execute();
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            $this->fqcn,
            $methodName
        ));
    }

    protected function execute()
    {
        $this->beforeRequest();

        $method = $this->getMethod();
        $url = $this->getUrl();
        $queryString = $this->getQueryString();

        if ($queryString && count($queryString) > 0) {
            $url = $url . '?' . http_build_query($queryString);
        }

        $payload = $this->getPayload();
        $savePayload = $this->sanitizePayload($payload);

        // dd($url, $savePayload);

        $request = MyinvoisRequest::create([
            'action' => $this->serviceName . '::' . $this->methodName,
            'url' => $url,
            'payload' => $savePayload && count($savePayload) > 0 ? $savePayload : null,
        ]);

        $response = $payload && count($payload) > 0
            ? $this->client->$method($url, $payload)
            : $this->client->$method($url);

        // dd($response->headers(), $response->json());

        $response->throw(function (Response $response, RequestException $e) use ($request) {
            $result = $response->json();
            $headers = $response->headers();
            $error = data_get($result, 'error');
            $errorCode = data_get($result, 'error.errorCode') ?? null;
            $errorMessage = null;
            $errorDescription = null;
            $correlationId = data_get($headers, 'correlationId.0');

            if ($errorCode) {
                $errorMessage = data_get($result, 'error.error');
                $errorDescription = data_get($result, 'error.errorMS');
            } elseif ($error && is_string($error)) {
                $errorMessage = $error;
            }

            $request->update([
                'http_code' => $response->getStatusCode() ?? $response->status(),
                'correlation_id' => $correlationId,
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'error_description' => $errorDescription,
                'error' => Str::limit(trim($e->getMessage()), 255),
            ]);
        });

        // dd($response->body());

        $result = $response->json();
        $headers = $response->headers();

        if ($response->successful()) {
            $http_code = $response->getStatusCode() ?? $response->status();
            $correlationId = data_get($headers, 'correlationId.0');

            $request->update([
                'http_code' => $http_code,
                'correlation_id' => $correlationId,
                'response' => $result,
            ]);

            $this->afterRequest(request: $request, result: $result);

            $return = [
                'success' => $http_code >= 200 && $http_code < 300 ? true : false,
            ];

            if ($result) {
                $return['data'] = $result;
            }

            return $return;
        }

        throw new MyInvoisApiError($result ?? ['code' => __('Error')]);
    }

    public function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Accept-Language' => 'en',
        ];

        if ($this instanceof AuthService) {
            // no need access token for this service
        } else {
            $accessToken = $this->myInvois->getAccessToken();

            if ($accessToken) {
                $headers['Authorization'] = 'Bearer ' . $accessToken;
            }
        }

        return $headers;
    }

    private function setRouteFromConfig(string $fqcn, string $method): void
    {
        $route_prefix = str($fqcn)->afterLast('\\')->remove('Service')->snake()->lower()->value;
        $route_name = str($method)->snake()->value;
        $route_path = '';
        $params = $this->getParams();

        $route = config('myinvois.routes.' . $route_prefix . '.' . $route_name);

        $split = str($route)->explode(' ');

        if (count($split) == 2) {
            $this->setMethod(data_get($split, '0'));
            $route_path = data_get($split, '1');
        } elseif (count($split) == 1) {
            $route_path = data_get($split, '0');
        }

        if ($params) {
            if (is_array($params)) {
                $mappedParams = collect($params)->mapWithKeys(fn($value, $key) => ["{" . $key . "}" => $value]);

                $route_path = Str::swap($mappedParams->toArray(), $route);
            } elseif (is_string($params) || is_numeric($params)) {
                $route_path = str_replace('{id}', $params, $route);
            }
        }

        $this->setRoute($route_path);
    }

    private function sanitizePayload(?array $payload): ?array
    {
        if ($payload && count($payload) > 0) {
            $sensitiveParams = $this->getSensitiveParams();

            $payloadCollection = collect($payload)->reject(function ($value, $key) use ($sensitiveParams) {
                return in_array($key, $sensitiveParams);
            });

            return $payloadCollection->toArray();

        }

        return null;
    }

    protected function getAllowedMethods(): array
    {
        $route_prefix = str($this->serviceName)->remove('Service')->snake()->lower()->value;

        return array_keys(config('myinvois.routes.' . $route_prefix) ?? []);
    }

    protected function getSensitiveParams()
    {
        return ['client_secret'];
    }

    private function beforeRequest(): void
    {
        $methodName = 'before' . Str::studly($this->methodName) . 'Request';

        if (method_exists($this, $methodName)) {
            $this->$methodName();
        }
    }

    private function afterRequest(MyinvoisRequest $request, ?array $result = []): void
    {
        $methodName = 'after' . Str::studly($this->methodName) . 'Request';

        if (method_exists($this, $methodName)) {
            $this->$methodName($request, $result);
        }
    }

    protected function getUrl(): string
    {
        if ($this->myInvois->isSandbox()) {
            $base_url = $this->myInvois->config('sandbox.base_url');
        } else {
            $base_url = $this->myInvois->config('base_url');
        }

        $url = $base_url . $this->getRoute();

        return $url;
    }

    protected function route(string $route): self
    {
        $this->setRoute($route);

        return $this;
    }

    protected function setRoute(string $route): void
    {
        $this->route = $route;
    }

    protected function getRoute(): string
    {
        return $this->route;
    }

    protected function method(string $method): self
    {
        $this->setMethod($method);

        return $this;
    }

    protected function setMethod(string $method): void
    {
        if ($method) {
            $this->method = strtolower($method);
        }
    }

    protected function getMethod(): string
    {
        return $this->method;
    }

    public function payload(array $payload): self
    {
        $this->setPayload($payload);

        return $this;
    }

    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    protected function getPayload(): array
    {
        return $this->payload;
    }

    public function queryString(array $queryString): self
    {
        $this->setQueryString($queryString);

        return $this;
    }

    protected function setQueryString(array $queryString): void
    {
        $this->queryString = $queryString;
    }

    protected function getQueryString(): array
    {
        return $this->queryString;
    }

    public function params(null|array|string|int $params): self
    {
        $this->setParams($params);

        return $this;
    }

    protected function setParams(null|array|string|int $params): void
    {
        $this->params = $params;
    }

    protected function getParams(): null|array|string|int
    {
        return $this->params;
    }
}