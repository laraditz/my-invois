<?php

namespace Laraditz\MyInvois\Services;

use Laraditz\MyInvois\Models\MyinvoisClient;
use Laraditz\MyInvois\Models\MyinvoisRequest;
use Illuminate\Support\Facades\DB;

class AuthService extends BaseService
{
    public function beforeTokenRequest()
    {
        $payload = $this->getPayload();

        $data = [
            'client_id' => data_get($payload, 'client_id') ?? $this->myInvois->getClientId(),
            'client_secret' => data_get($payload, 'client_secret') ?? $this->myInvois->getClientSecret(),
            'grant_type' => data_get($payload, 'grant_type') ?? 'client_credentials',
            'scope' => data_get($payload, 'scope') ?? 'InvoicingAPI',
        ];

        $this->setPayload($data);
    }

    public function afterTokenResponse(MyinvoisRequest $request, array $result = []): void
    {
        DB::transaction(function () use ($request, $result) {
            $access_token = data_get($result, 'access_token');
            $expires_in = data_get($result, 'expires_in') ?? 0;
            $token_type = data_get($result, 'token_type');
            $scope = data_get($result, 'scope');

            $client = MyinvoisClient::where('id', $this->myInvois->getClientId())->first();

            if (!$client) {
                $client = MyinvoisClient::create([
                    'id' => $this->myInvois->getClientId(),
                    'secret' => $this->myInvois->getClientSecret(),
                ]);
            }

            if ($client) {
                $client->accessToken()->updateOrCreate([], [
                    'access_token' => $access_token,
                    'expires_at' => now()->addSeconds($expires_in),
                    'type' => $token_type,
                    'scopes' => $scope
                ]);
            }
        });

    }
}