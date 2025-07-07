<?php

namespace Laraditz\MyInvois\Services;

use Illuminate\Support\Str;
use Laraditz\MyInvois\Enums\Format;
use Illuminate\Support\Facades\Storage;
use Laraditz\MyInvois\Models\MyinvoisRequest;
use Laraditz\MyInvois\Models\MyinvoisDocument;
use Laraditz\MyInvois\Exceptions\MyInvoisException;

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

                $generatedDocument = $this->myInvois->generateDocument(data: $document, format: $format);

                // $this->displayXML($generatedDocument);

                $newDocuments[] = [
                    'format' => $format?->value,
                    'codeNumber' => $document->getCodeNumber(),
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

                $myInvoisDocument = MyinvoisDocument::create([
                    'request_id' => $request->id,
                    'code_number' => $codeNumber,
                    'type' => $invoiceType,
                    'format' => $format,
                    'file_name' => $file_name,
                    'file_path' => $file_path,
                    'disk' => $disk,
                    'hash' => $hash,
                ]);
            }
        }
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