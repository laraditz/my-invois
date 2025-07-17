<?php

namespace Laraditz\MyInvois\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\Format;
use Illuminate\Support\Facades\Storage;
use Laraditz\MyInvois\Enums\DocumentStatus;
use Laraditz\MyInvois\Models\MyinvoisRequest;
use Laraditz\MyInvois\Models\MyinvoisDocument;
use Laraditz\MyInvois\Exceptions\MyInvoisException;
use Laraditz\MyInvois\Models\MyinvoisDocumentHistory;

class DocumentService extends BaseService
{
    public function beforeSubmitRequest()
    {
        $payload = $this->getPayload();
        $documents = data_get($payload, 'documents');
        $format = data_get($payload, 'format');

        if ($format === Format::JSON) { // to be added in future
            throw new MyInvoisException(__('Format not supported'));
        }

        $newDocuments = [];

        // dd($payload, $documents, $format);

        if ($documents && is_array($documents) && count($documents) > 0) {
            foreach ($documents as $document) {
                $codeNumber = $document->getCodeNumber();

                $existingDocument = MyinvoisDocument::query()
                    ->where('client_id', $this->myInvois->getClientId())
                    ->where('code_number', $codeNumber)
                    ->isAccepted()
                    ->count();

                if ($existingDocument > 0) {
                    continue;
                }

                $generatedDocument = $this->myInvois->generateDocument(data: $document, format: $format);

                // $this->displayXML($generatedDocument);

                $newDocuments[] = [
                    'format' => $format?->value,
                    'codeNumber' => $codeNumber,
                    'documentHash' => hash($this->myInvois->getHashAlgorithm(), $generatedDocument),
                    'document' => base64_encode($generatedDocument),
                    'invoiceType' => $document->getInvoiceTypeCode(),

                ];
            }
        }

        throw_if(count($newDocuments) <= 0, MyInvoisException::class, __('Need to have at least one document.'));

        $sensitiveParams = [];
        $removeParams = [];
        for ($i = 0; $i < count($newDocuments); $i++) {
            $sensitiveParams[] = 'documents.' . $i . '.document';
            $sensitiveParams[] = 'documents.' . $i . '.invoiceType';
            $removeParams[] = 'documents.' . $i . '.invoiceType';
        }

        $this->setSensitiveParams($sensitiveParams);
        $this->setRemoveParams($removeParams);
        $this->setPayload(['documents' => $newDocuments]);
    }

    public function afterSubmitRequest(MyinvoisRequest $request)
    {
        $payload = $this->getPayload();
        $documents = data_get($payload, 'documents');

        if (is_array($documents) && count($documents) > 0) {

            foreach ($documents as $document) {

                $codeNumber = data_get($document, 'codeNumber');
                $format = data_get($document, 'format');
                $hash = data_get($document, 'documentHash');
                $invoiceType = data_get($document, 'invoiceType');
                $documentContent = data_get($document, 'document');

                $ext = strtolower($format);
                $file_name = (string) Str::ulid() . '.' . $ext;
                $file_path = $this->myInvois->getDocumentPath() . $file_name;
                $disk = $this->myInvois->getDisk();

                try {
                    $file = Storage::disk($disk)->put($file_path, base64_decode($documentContent));
                } catch (\Throwable $th) {
                    //throw $th;
                    $file_name = null;
                    $file_path = null;
                }

                $client_id = $request->client_id;

                $myInvoisDocument = MyinvoisDocument::query()
                    ->where('client_id', $client_id)
                    ->where('code_number', $codeNumber)
                    ->first();

                $updateData = [
                    'request_id' => $request->id,
                    'type' => $invoiceType,
                    'format' => $format,
                    'file_name' => $file_name,
                    'file_path' => $file_path,
                    'disk' => $disk,
                    'hash' => $hash,
                    'submission_uid' => null,
                    'uuid' => null,
                    'error' => null,
                    'accepted_at' => null,
                    'rejected_at' => null,
                ];

                if (!$myInvoisDocument) {
                    $myInvoisDocument = MyinvoisDocument::create([
                        ...[
                            'client_id' => $client_id,
                            'code_number' => $codeNumber,
                        ],
                        ...$updateData
                    ]);
                } else {
                    // Add to histories table before replacing
                    $this->addHistory($myInvoisDocument);

                    $myInvoisDocument = tap($myInvoisDocument)->update($updateData);
                }
            }
        }
    }

    public function afterSubmitResponse(MyinvoisRequest $request, array $result = []): void
    {
        $acceptedDocuments = data_get($result, 'acceptedDocuments');
        $rejectedDocuments = data_get($result, 'rejectedDocuments');
        $submissionUid = data_get($result, 'submissionUid');

        if ($acceptedDocuments && is_array($acceptedDocuments) && count($acceptedDocuments) > 0) {
            foreach ($acceptedDocuments as $acceptedDocument) {
                $invoiceCodeNumber = data_get($acceptedDocument, 'invoiceCodeNumber');
                $uuid = data_get($acceptedDocument, 'uuid');

                if ($invoiceCodeNumber) {
                    $myInvoisDocument = MyinvoisDocument::query()
                        ->where('request_id', $request->id)
                        ->where('code_number', $invoiceCodeNumber)
                        ->first();

                    if ($myInvoisDocument) {
                        $myInvoisDocument->update([
                            'submission_uid' => $submissionUid,
                            'uuid' => $uuid,
                            'accepted_at' => now(),
                        ]);
                    }
                }
            }
        }

        if ($rejectedDocuments && is_array($rejectedDocuments) && count($rejectedDocuments) > 0) {
            foreach ($rejectedDocuments as $rejectedDocument) {
                $invoiceCodeNumber = data_get($rejectedDocument, 'invoiceCodeNumber');
                $error = data_get($rejectedDocument, 'error');
                $errorCode = data_get($error, 'code');
                $errorMessage = data_get($error, 'message');
                $errorDetails = data_get($error, 'details');

                if ($invoiceCodeNumber) {
                    $myInvoisDocument = MyinvoisDocument::query()
                        ->where('request_id', $request->id)
                        ->where('code_number', $invoiceCodeNumber)
                        ->first();

                    if ($myInvoisDocument) {
                        $myInvoisDocument->update([
                            'error' => $errorDetails && is_array($errorDetails) ? $errorDetails : null,
                            'error_code' => $errorCode,
                            'error_message' => $errorMessage,
                            'rejected_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    public function beforeDetailsRequest()
    {
        $payload = $this->getPayload();
        $params = $this->getParams();
        $uuid = null;

        if (count($payload) > 0) {
            $uuid = data_get($payload, 0) ?? data_get($payload, 'uuid');

            if ($uuid) {
                $this->setParams(['uuid' => $uuid]);
            }
        }

        throw_if(!($uuid || data_get($params, 'uuid')), MyInvoisException::class, __('Missing uuid parameter.'));
    }

    public function afterDetailsResponse(MyinvoisRequest $request, array $result = []): void
    {
        $uuid = data_get($result, 'uuid');
        $longId = data_get($result, 'longId');
        $dateTimeValidated = data_get($result, 'dateTimeValidated');
        $status = data_get($result, 'status');
        $documentStatusReason = data_get($result, 'documentStatusReason');
        $cancelDateTime = data_get($result, 'cancelDateTime');
        $rejectRequestDateTime = data_get($result, 'rejectRequestDateTime');
        $dateTimeIssued = data_get($result, 'dateTimeIssued');
        $appTimezone = config('app.timezone'); // get app timezone

        $document = MyinvoisDocument::query()->where('uuid', $uuid)->first();

        if ($document) {
            $document->update([
                'long_id' => $longId,
                'status' => $status ? DocumentStatus::tryFromName($status) : null,
                'issued_at' => $dateTimeIssued ? Carbon::parse($dateTimeIssued)->timezone($appTimezone) : null,
                'validated_at' => $dateTimeValidated ? Carbon::parse($dateTimeValidated)->timezone($appTimezone) : null,
                'cancel_at' => $cancelDateTime ? Carbon::parse($cancelDateTime)->timezone($appTimezone) : null,
                'reject_request_at' => $rejectRequestDateTime ? Carbon::parse($rejectRequestDateTime)->timezone($appTimezone) : null,
                'status_reason' => $documentStatusReason,
            ]);
        }
    }

    private function addHistory(MyinvoisDocument $myInvoisDocument): void
    {
        $attributes = [
            ...$myInvoisDocument->replicate()->getAttributes(),
            ...['error' => $myInvoisDocument->error] // need to reassign so that json in proper format when save
        ];

        MyinvoisDocumentHistory::create(attributes: $attributes);
    }

    private function displayXML(string $document)
    {
        $dom = new \DOMDocument;
        $dom->loadXML($document);
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = true;

        header("Content-type: text/xml");
        echo $dom->saveXML();
        exit;
    }
}