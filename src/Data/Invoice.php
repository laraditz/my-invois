<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;

class Invoice extends AbstractData
{
    public function __construct(
        public string $ID,
        public Carbon|string $IssueDate,
        public Carbon|string $IssueTime,
        public Data $InvoiceTypeCode,
        public string $DocumentCurrencyCode,
        public string $TaxCurrencyCode,
        public ?DatePeriod $InvoicePeriod = null,
        public array $BillingReference = [],
        public ?array $AdditionalDocumentReference = [],
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
        public ?array $UBLExtensions = [],
        public ?array $Signature = [],
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
            'AllowanceCharge', 'TaxTotal', 'LegalMonetaryTotal', 'InvoiceLine' => XMLNS::CAC,
            default => null
        };
    }
}