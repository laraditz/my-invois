<?php

namespace Laraditz\MyInvois\Services;

use Laraditz\MyInvois\Enums\Format;
use Laraditz\MyInvois\Models\MyinvoisRequest;
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

                ];
            }
        }

        throw_if(count($newDocuments) <= 0, MyInvoisException::class, __('Need to have at least one document.'));

        $sensitiveParams = [];
        for ($i = 0; $i < count($newDocuments); $i++) {
            $sensitiveParams[] = 'documents.' . $i . '.document';
        }

        $this->setSensitiveParams($sensitiveParams);
        $this->setPayload(['documents' => $newDocuments]);
    }

    public function afterSubmitRequest(MyinvoisRequest $request)
    {

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