<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;

class Invoice extends AbstractData
{
    public function __construct(
        public ?UBLExtensions $UBLExtensions = null,
        public ?string $ID = null,
        public null|Carbon|string $IssueDate = null,
        public null|Carbon|string $IssueTime = null,
        public ?InvoiceTypeCode $InvoiceTypeCode = null,
        public ?string $DocumentCurrencyCode = null,
        public ?string $TaxCurrencyCode = null,
        public ?DatePeriod $InvoicePeriod = null,
        public ?array $BillingReference = [],
        public ?array $AdditionalDocumentReference = [],
        public ?Signature $Signature = null,
        public ?AccountingSupplierParty $AccountingSupplierParty = null,
        public ?AccountingCustomerParty $AccountingCustomerParty = null,
        public ?Delivery $Delivery = null,
        public ?PaymentMeans $PaymentMeans = null,
        public ?PaymentTerms $PaymentTerms = null,
        public ?PrepaidPayment $PrepaidPayment = null,
        public ?array $AllowanceCharge = [],
        public ?TaxTotal $TaxTotal = null,
        public ?LegalMonetaryTotal $LegalMonetaryTotal = null,
        public ?array $InvoiceLine = [],
    ) {
    }

    public function getValue(string $name): mixed
    {
        return match ($name) {
            'IssueDate' => $this->$name instanceof Carbon ? $this->$name?->toDateString() : $this->$name,
            'IssueTime' => $this->$name instanceof Carbon ? $this->$name?->format('H:i:s\Z') : $this->$name,
            default => $this->$name
        };
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID', 'IssueDate', 'IssueTime', 'InvoiceTypeCode', 'DocumentCurrencyCode', 'TaxCurrencyCode' => XMLNS::CBC,
            'InvoicePeriod', 'BillingReference', 'AdditionalDocumentReference', 'AccountingSupplierParty',
            'AccountingCustomerParty', 'Delivery', 'PaymentMeans', 'PaymentTerms', 'PrepaidPayment',
            'AllowanceCharge', 'TaxTotal', 'LegalMonetaryTotal', 'InvoiceLine', 'Signature' => XMLNS::CAC,
            'UBLExtensions' => XMLNS::NONE,
            default => null
        };
    }

    public function getCodeNumber()
    {
        return $this->ID;
    }

    public function getInvoiceTypeCode()
    {
        return $this->InvoiceTypeCode?->value;
    }
}