<?php

namespace Laraditz\MyInvois\Data;

use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;

class Invoice extends AbstractData
{
    public function __construct(
        public string $ID,
        public Carbon $IssueDate,
        public Carbon $IssueTime,
        public array $InvoiceTypeCode,
        public string $DocumentCurrencyCode,
        public string $TaxCurrencyCode,
        public ?DatePeriod $InvoicePeriod = null,
        public array $BillingReference = [],
        public ?array $AdditionalDocumentReference = [],
        public ?AccountingSupplierParty $AccountingSupplierParty = null,
        public ?AccountingCustomerParty $AccountingCustomerParty = null,
        public ?array $Delivery = [],
        public ?array $PaymentMeans = [],
        public ?array $PaymentTerms = [],
        public ?array $PrepaidPayment = [],
        public ?array $AllowanceCharge = [],
        public ?array $TaxTotal = [],
        public ?array $LegalMonetaryTotal = [],
        public ?array $InvoiceLine = [],
        public ?array $UBLExtensions = [],
        public ?array $Signature = [],
    ) {
    }

    public function getValue(string $name): mixed
    {
        return match ($name) {
            'IssueDate' => $this->$name?->toDateString(),
            'IssueTime' => $this->$name?->toTimeString(),
            default => $this->$name
        };
    }

    public function ns(string $name): ?XMLNS
    {
        return match ($name) {
            'ID', 'IssueDate', 'IssueTime', 'InvoiceTypeCode', 'DocumentCurrencyCode', 'TaxCurrencyCode' => XMLNS::CBC,
            'InvoicePeriod', 'BillingReference', 'AdditionalDocumentReference', 'AccountingSupplierParty', 'AccountingCustomerParty' => XMLNS::CAC,
            default => null
        };
    }
}