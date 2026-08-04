<?php

namespace Laraditz\MyInvois\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laraditz\MyInvois\Data\DebitNote;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\Enums\Format;
use Laraditz\MyInvois\Exceptions\MyInvoisException;
use Laraditz\MyInvois\Models\MyinvoisAccessToken;
use Laraditz\MyInvois\Models\MyinvoisDocument;
use Laraditz\MyInvois\MyInvois;
use Laraditz\MyInvois\Services\DocumentService;
use Laraditz\MyInvois\Tests\TestCase;
use ReflectionMethod;

class DocumentServiceDebitNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_prepares_a_debit_note_for_submission()
    {
        $debitNote = new DebitNote(
            ID: 'DN-100',
            InvoiceTypeCode: new InvoiceTypeCode('03'),
        );

        $myInvois = new MyInvois(client_id: 'test-client', client_secret: 'test-secret');
        $this->cacheAccessTokenFor($myInvois);

        $service = new DocumentService(
            myInvois: $myInvois,
            payload: ['documents' => [$debitNote], 'format' => Format::XML],
        );

        $service->beforeSubmitRequest();

        $payload = $this->getProtectedPayload($service);
        $document = $payload['documents'][0];

        $this->assertSame('DN-100', $document['codeNumber']);
        $this->assertSame('03', $document['invoiceType']);
        $this->assertStringContainsString('<DebitNote', base64_decode($document['document']));
    }

    public function test_it_skips_a_debit_note_that_already_has_a_pending_document_record()
    {
        $myInvois = new MyInvois(client_id: 'test-client', client_secret: 'test-secret');
        $this->cacheAccessTokenFor($myInvois);

        MyinvoisDocument::create([
            'client_id' => $myInvois->getClientId(),
            'request_id' => 1,
            'code_number' => 'DN-100',
            'format' => Format::XML->value,
            'status' => null,
        ]);

        $debitNote = new DebitNote(
            ID: 'DN-100',
            InvoiceTypeCode: new InvoiceTypeCode('03'),
        );

        $service = new DocumentService(
            myInvois: $myInvois,
            payload: ['documents' => [$debitNote], 'format' => Format::XML],
        );

        $this->expectException(MyInvoisException::class);

        $service->beforeSubmitRequest();
    }

    private function getProtectedPayload(DocumentService $service): array
    {
        $method = new ReflectionMethod($service, 'getPayload');

        return $method->invoke($service);
    }

    private function cacheAccessTokenFor(MyInvois $myInvois): void
    {
        // BaseService's constructor eagerly resolves an access token for
        // every service, including DocumentService - seed a cached, non
        // expired token so it's found locally instead of hitting the API.
        MyinvoisAccessToken::create([
            'client_id' => $myInvois->getClientId(),
            'access_token' => 'test-access-token',
            'expires_at' => now()->addHour(),
        ]);
    }
}
