<?php

namespace Laraditz\MyInvois\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laraditz\MyInvois\Models\MyinvoisAccessToken;
use Laraditz\MyInvois\MyInvois;
use Laraditz\MyInvois\Tests\TestCase;

class AccessTokenOnBehalfOfTest extends TestCase
{
    use RefreshDatabase;

    protected function defineEnvironment($app)
    {
        parent::defineEnvironment($app);

        $app['config']->set('myinvois.client_id', 'test-client');
        $app['config']->set('myinvois.client_secret', 'test-secret');
        $app['config']->set('myinvois.on_behalf_of', null);
    }

    private function seedToken(?string $onBehalfOf, string $accessToken): void
    {
        MyinvoisAccessToken::create([
            'client_id' => 'test-client',
            'access_token' => $accessToken,
            'expires_at' => now()->addHour(),
            'on_behalf_of' => $onBehalfOf,
        ]);
    }

    private function fakeTokenEndpoint(string $accessToken): void
    {
        Http::fake([
            '*/connect/token' => Http::response([
                'access_token' => $accessToken,
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'InvoicingAPI',
            ]),
        ]);
    }

    public function test_it_returns_the_cached_token_for_the_taxpayer_it_was_issued_for()
    {
        $this->seedToken('IG111', 'partner-token');
        $this->seedToken(null, 'own-token');

        $this->assertSame('own-token', app('myinvois')->getAccessToken());

        app('myinvois')->setOnBehalfOf('IG111');

        $this->assertSame('partner-token', app('myinvois')->getAccessToken());
    }

    public function test_a_token_for_another_taxpayer_is_not_reused()
    {
        $this->seedToken(null, 'own-token');
        $this->fakeTokenEndpoint('partner-token');

        app('myinvois')->setOnBehalfOf('IG111');

        $this->assertSame('partner-token', app('myinvois')->getAccessToken());

        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), '/connect/token')
            && $request->header('onbehalfof') === ['IG111']);
    }

    public function test_fetching_a_taxpayer_token_does_not_overwrite_the_own_token()
    {
        $this->seedToken(null, 'own-token');
        $this->fakeTokenEndpoint('partner-token');

        app('myinvois')->setOnBehalfOf('IG111');
        app('myinvois')->getAccessToken();

        app('myinvois')->setOnBehalfOf(null);

        $this->assertSame('own-token', app('myinvois')->getAccessToken());
        $this->assertSame(2, MyinvoisAccessToken::count());
    }

    public function test_refreshing_a_token_replaces_the_expired_one_for_the_same_taxpayer()
    {
        MyinvoisAccessToken::create([
            'client_id' => 'test-client',
            'access_token' => 'old-partner-token',
            'expires_at' => now()->subMinute(),
            'on_behalf_of' => 'IG111',
        ]);
        $this->fakeTokenEndpoint('new-partner-token');

        app('myinvois')->setOnBehalfOf('IG111');

        $this->assertSame('new-partner-token', app('myinvois')->getAccessToken());
        $this->assertSame(1, MyinvoisAccessToken::where('on_behalf_of', 'IG111')->count());
    }
}
