# Invoice Submission Guide

An Invoice is the standard e-Invoice document type - LHDN type code `01`. This guide walks through building a complete one and submitting it, alongside the Self-Billed variant and the parts of the flow that are easy to miss.

`Invoice` is the base document type every other type in this package builds on - `DebitNote` (see [docs/debit-note.md](debit-note.md)) extends it directly and shares its entire field set.

## Contents

- [Self-Billed Invoice](#self-billed-invoice)
- [Complete example](#complete-example)
- [Checking submission status](#checking-submission-status)
- [Digital signatures](#digital-signatures)
- [Things to watch for](#things-to-watch-for)

## Self-Billed Invoice

Unlike Debit Note, there's no separate `SelfBilledInvoice` class - self-billing an Invoice only changes two things:

- `InvoiceTypeCode` is `new InvoiceTypeCode('11')` (Self-Billed Invoice) instead of `'01'`
- You set `onbehalfof` on the submission call, since your system is issuing it on behalf of the supplier's TIN:

```php
$result = MyInvois::document(onbehalfof: 'C25845632020')->submit(
    documents: [$invoice],
    format: Format::XML
);
```

Everything else - building the `Invoice` object itself - is identical to a standard invoice.

## Complete Example

```php
use Laraditz\MyInvois\Facades\MyInvois;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Data\AccountingSupplierParty;
use Laraditz\MyInvois\Data\AccountingCustomerParty;
use Laraditz\MyInvois\Data\Party;
use Laraditz\MyInvois\Data\PostalAddress;
use Laraditz\MyInvois\Data\PartyIdentification;
use Laraditz\MyInvois\Data\PartyLegalEntity;
use Laraditz\MyInvois\Data\Contact;
use Laraditz\MyInvois\Data\InvoiceLine;
use Laraditz\MyInvois\Data\Item;
use Laraditz\MyInvois\Data\Price;
use Laraditz\MyInvois\Data\TaxCategory;
use Laraditz\MyInvois\Data\TaxScheme;
use Laraditz\MyInvois\Data\TaxSubtotal;
use Laraditz\MyInvois\Data\TaxTotal;
use Laraditz\MyInvois\Data\LegalMonetaryTotal;
use Laraditz\MyInvois\Data\Money;
use Laraditz\MyInvois\Data\Country;
use Laraditz\MyInvois\Data\Data;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\Enums\Format;

// Supplier - the party issuing the invoice
$supplierParty = new Party(
    PartyIdentification: [new PartyIdentification('123456789012')],
    PartyName: [new Data('ABC Company Sdn Bhd')],
    PostalAddress: new PostalAddress(
        StreetName: '123 Main Street',
        CityName: 'Kuala Lumpur',
        PostalZone: '50000',
        Country: new Country('MY')
    ),
    PartyLegalEntity: [new PartyLegalEntity(
        RegistrationName: 'ABC Company Sdn Bhd'
    )],
    Contact: new Contact(
        Name: 'John Doe',
        Telephone: '+60123456789',
        Email: 'john@abc.com'
    )
);

// Customer - the party being billed
$customerParty = new Party(
    PartyIdentification: [new PartyIdentification('987654321098')],
    PartyName: [new Data('XYZ Corporation')],
    PostalAddress: new PostalAddress(
        StreetName: '456 Business Ave',
        CityName: 'Petaling Jaya',
        PostalZone: '46100',
        Country: new Country('MY')
    ),
    PartyLegalEntity: [new PartyLegalEntity(
        RegistrationName: 'XYZ Corporation Sdn Bhd'
    )],
    Contact: new Contact(
        Name: 'Jane Smith',
        Telephone: '+60987654321',
        Email: 'jane@xyz.com'
    )
);

// One line item - repeat this shape for each product/service on the invoice
$invoiceLine = new InvoiceLine(
    ID: '1',
    InvoicedQuantity: 2,
    LineExtensionAmount: new Money(200.00, 'MYR'),
    Item: new Item(
        Name: 'Product A',
        Description: 'High quality product',
        SellersItemIdentification: new Data('PROD-001')
    ),
    Price: new Price(
        PriceAmount: new Money(100.00, 'MYR')
    ),
    TaxTotal: new TaxTotal(
        TaxAmount: new Money(12.00, 'MYR'),
        TaxSubtotal: [new TaxSubtotal(
            TaxableAmount: new Money(200.00, 'MYR'),
            TaxAmount: new Money(12.00, 'MYR'),
            TaxCategory: new TaxCategory(
                ID: 'S',
                Percent: 6.0,
                TaxScheme: new TaxScheme('SST')
            )
        )]
    )
);

// Document-level tax total - usually the sum across all lines
$taxTotal = new TaxTotal(
    TaxAmount: new Money(12.00, 'MYR'),
    TaxSubtotal: [new TaxSubtotal(
        TaxableAmount: new Money(200.00, 'MYR'),
        TaxAmount: new Money(12.00, 'MYR'),
        TaxCategory: new TaxCategory(
            ID: 'S',
            Percent: 6.0,
            TaxScheme: new TaxScheme('SST')
        )
    )]
);

// Document-level monetary total
$legalMonetaryTotal = new LegalMonetaryTotal(
    LineExtensionAmount: new Money(200.00, 'MYR'),
    TaxExclusiveAmount: new Money(200.00, 'MYR'),
    TaxInclusiveAmount: new Money(212.00, 'MYR'),
    PayableAmount: new Money(212.00, 'MYR')
);

$invoice = new Invoice(
    ID: 'INV-2025-001',
    IssueDate: now(),
    IssueTime: now(),
    InvoiceTypeCode: new InvoiceTypeCode('01'), // Invoice
    DocumentCurrencyCode: 'MYR',
    AccountingSupplierParty: new AccountingSupplierParty($supplierParty),
    AccountingCustomerParty: new AccountingCustomerParty($customerParty),
    InvoiceLine: [$invoiceLine],
    TaxTotal: $taxTotal,
    LegalMonetaryTotal: $legalMonetaryTotal
);

try {
    $result = MyInvois::document()->submit(
        documents: [$invoice],
        format: Format::XML
    );

    if ($result['success']) {
        echo "Invoice submitted successfully!";
        echo "Request ID: " . $result['request_id'];
        echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT);
    }
} catch (\Laraditz\MyInvois\Exceptions\MyInvoisApiError $e) {
    echo "Error: " . $e->getMessage();
}
```

## Checking Submission Status

MyInvois doesn't push status updates - poll `details()` after submitting, until the status settles to `Valid` or `Invalid`:

```php
$uuid = data_get($result, 'data.acceptedDocuments.0.uuid');

// Automatically updates the local myinvois_documents record (status, long_id, etc.)
$details = MyInvois::document()->details($uuid);
```

Hold on to the `uuid` you get back here - it's what you'll need later if this invoice ever needs a Debit Note against it (see [docs/debit-note.md](debit-note.md)).

## Digital Signatures

If `MYINVOIS_CERTIFICATE_PATH` and `MYINVOIS_PRIVATE_KEY_PATH` are configured and both files exist, submissions are signed automatically - no extra code needed beyond what's shown above. Without a certificate configured, documents are still generated and submitted, just unsigned (`listVersionID` `1.0` instead of `1.1`).

## Things to Watch For

- **Use a distinct `ID` prefix per document type.** The duplicate-submission check is keyed by `client_id` + the document's `ID` only, not document type - if an `Invoice` and a `DebitNote` ever share the same `ID`, the second submission is silently skipped as a duplicate. Keeping separate sequences (`INV-`, `DN-`, etc.) avoids this.
- **No client-side field validation.** The package doesn't check that required LHDN fields are present or correctly formatted before submitting - errors come back from the MyInvois API itself, via `rejectedDocuments` in the response or a thrown `MyInvoisApiError`.
- **JSON format isn't implemented yet.** Only `Format::XML` is supported for submission today.
