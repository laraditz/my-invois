# Debit Note Submission Guide

A Debit Note is how you correct or increase the amount owed on an e-Invoice that MyInvois has already validated. Once LHDN accepts a document, it can't be edited — a Debit Note is the mechanism for adjustments discovered afterward (a missed charge, a price correction, an under-billed line item), issued against the original invoice rather than in place of it.

This package supports two Debit Note types:

| Class | LHDN Type Code | Use when |
|---|---|---|
| `DebitNote` | `03` | You're adjusting your own previously-submitted invoice |
| `SelfBilledDebitNote` | `13` | Your system issues the Debit Note on behalf of the supplier |

Both classes accept the exact same fields as `Invoice` — `DebitNote` extends `Invoice` directly, so anything you already know about building an `Invoice` (parties, lines, tax totals, monetary totals) applies unchanged. The one addition that matters here is `BillingReference`, which links the Debit Note back to the original invoice.

## Contents

- [How BillingReference works](#how-billingreference-works)
- [Complete example: invoice, then a debit note against it](#complete-example-invoice-then-a-debit-note-against-it)
- [Self-Billed Debit Note](#self-billed-debit-note)
- [Checking submission status](#checking-submission-status)
- [Things to watch for](#things-to-watch-for)

## How BillingReference Works

`BillingReference` is an array of `BillingReference` objects, each wrapping an array of `InvoiceDocumentReference` objects. In practice you'll set exactly one:

```php
use Laraditz\MyInvois\Data\BillingReference;
use Laraditz\MyInvois\Data\InvoiceDocumentReference;

$billingReference = [
    new BillingReference(
        InvoiceDocumentReference: [
            new InvoiceDocumentReference(
                ID: 'INV-2025-001',                              // the original invoice's own ID (its `ID` field)
                UUID: 'JEEA7W331XXXNBAXXX71880XXX',               // the original invoice's MyInvois UUID
            ),
        ],
    ),
];
```

The `UUID` is what MyInvois assigns after the original invoice is accepted — you get it back from `MyInvois::document()->submit()`'s response, or by looking up the stored `MyinvoisDocument` record's `uuid` column afterward. The package does not look this up for you; you're expected to have it on hand (e.g. from your own invoice/document tracking) before building the Debit Note.

## Complete Example: Invoice, Then a Debit Note Against It

This mirrors the [Complete Example](../README.md#complete-example-creating-and-submitting-invoice) in the README, extended to show the realistic end-to-end flow: submit an invoice, then later submit a Debit Note correcting it.

```php
use Laraditz\MyInvois\Facades\MyInvois;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Data\DebitNote;
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
use Laraditz\MyInvois\Data\BillingReference;
use Laraditz\MyInvois\Data\InvoiceDocumentReference;
use Laraditz\MyInvois\Data\InvoiceTypeCode;
use Laraditz\MyInvois\Enums\Format;

// --- Step 1: build and submit the original invoice ---

$supplierParty = new Party(
    PartyIdentification: [new PartyIdentification('123456789012')],
    PartyName: [new \Laraditz\MyInvois\Data\Data('ABC Company Sdn Bhd')],
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

$customerParty = new Party(
    PartyIdentification: [new PartyIdentification('987654321098')],
    PartyName: [new \Laraditz\MyInvois\Data\Data('XYZ Corporation')],
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

$invoiceLine = new InvoiceLine(
    ID: '1',
    InvoicedQuantity: 2,
    LineExtensionAmount: new Money(200.00, 'MYR'),
    Item: new Item(
        Name: 'Product A',
        Description: 'High quality product',
        SellersItemIdentification: new \Laraditz\MyInvois\Data\Data('PROD-001')
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

$invoiceResult = MyInvois::document()->submit(
    documents: [$invoice],
    format: Format::XML
);

// After LHDN validates it, look up its UUID (e.g. via MyinvoisDocument, or
// MyInvois::document()->details($uuid) once you have it from your own tracking).
$originalInvoiceUuid = \Laraditz\MyInvois\Models\MyinvoisDocument::query()
    ->where('code_number', 'INV-2025-001')
    ->value('uuid');

// --- Step 2: weeks later, a pricing error is found - issue a Debit Note ---

// The correction itself: an extra RM50 that should have been on the
// original invoice.
$debitNoteLine = new InvoiceLine(
    ID: '1',
    InvoicedQuantity: 1,
    LineExtensionAmount: new Money(50.00, 'MYR'),
    Item: new Item(
        Name: 'Product A - price correction',
        Description: 'Shortfall on original invoice INV-2025-001',
        SellersItemIdentification: new \Laraditz\MyInvois\Data\Data('PROD-001')
    ),
    Price: new Price(
        PriceAmount: new Money(50.00, 'MYR')
    ),
    TaxTotal: new TaxTotal(
        TaxAmount: new Money(3.00, 'MYR'),
        TaxSubtotal: [new TaxSubtotal(
            TaxableAmount: new Money(50.00, 'MYR'),
            TaxAmount: new Money(3.00, 'MYR'),
            TaxCategory: new TaxCategory(
                ID: 'S',
                Percent: 6.0,
                TaxScheme: new TaxScheme('SST')
            )
        )]
    )
);

$debitNoteTaxTotal = new TaxTotal(
    TaxAmount: new Money(3.00, 'MYR'),
    TaxSubtotal: [new TaxSubtotal(
        TaxableAmount: new Money(50.00, 'MYR'),
        TaxAmount: new Money(3.00, 'MYR'),
        TaxCategory: new TaxCategory(
            ID: 'S',
            Percent: 6.0,
            TaxScheme: new TaxScheme('SST')
        )
    )]
);

$debitNoteLegalMonetaryTotal = new LegalMonetaryTotal(
    LineExtensionAmount: new Money(50.00, 'MYR'),
    TaxExclusiveAmount: new Money(50.00, 'MYR'),
    TaxInclusiveAmount: new Money(53.00, 'MYR'),
    PayableAmount: new Money(53.00, 'MYR')
);

$debitNote = new DebitNote(
    ID: 'DN-2025-001', // distinct prefix from the invoice sequence - see "Things to watch for" below
    IssueDate: now(),
    IssueTime: now(),
    InvoiceTypeCode: new InvoiceTypeCode('03'), // Debit Note
    DocumentCurrencyCode: 'MYR',
    BillingReference: [
        new BillingReference(
            InvoiceDocumentReference: [
                new InvoiceDocumentReference(
                    ID: 'INV-2025-001',
                    UUID: $originalInvoiceUuid,
                ),
            ],
        ),
    ],
    AccountingSupplierParty: new AccountingSupplierParty($supplierParty),
    AccountingCustomerParty: new AccountingCustomerParty($customerParty),
    InvoiceLine: [$debitNoteLine],
    TaxTotal: $debitNoteTaxTotal,
    LegalMonetaryTotal: $debitNoteLegalMonetaryTotal
);

try {
    $result = MyInvois::document()->submit(
        documents: [$debitNote],
        format: Format::XML
    );

    if ($result['success']) {
        echo "Debit Note submitted successfully!";
        echo "Request ID: " . $result['request_id'];
        echo "Response: " . json_encode($result['data'], JSON_PRETTY_PRINT);
    }
} catch (\Laraditz\MyInvois\Exceptions\MyInvoisApiError $e) {
    echo "Error: " . $e->getMessage();
}
```

## Self-Billed Debit Note

Identical to the above, except:

- Use `SelfBilledDebitNote` instead of `DebitNote`
- `InvoiceTypeCode` is `new InvoiceTypeCode('13')` instead of `'03'`
- You'll typically set `onbehalfof` on the submission call, since a self-billed document is issued by your system on behalf of the supplier's TIN:

```php
use Laraditz\MyInvois\Data\SelfBilledDebitNote;

$selfBilledDebitNote = new SelfBilledDebitNote(
    ID: 'DN-2025-002',
    IssueDate: now(),
    IssueTime: now(),
    InvoiceTypeCode: new InvoiceTypeCode('13'), // Self-Billed Debit Note
    DocumentCurrencyCode: 'MYR',
    BillingReference: [
        new BillingReference(
            InvoiceDocumentReference: [
                new InvoiceDocumentReference(
                    ID: 'INV-2025-001',
                    UUID: $originalInvoiceUuid,
                ),
            ],
        ),
    ],
    AccountingSupplierParty: new AccountingSupplierParty($supplierParty),
    AccountingCustomerParty: new AccountingCustomerParty($customerParty),
    InvoiceLine: [$debitNoteLine],
    TaxTotal: $debitNoteTaxTotal,
    LegalMonetaryTotal: $debitNoteLegalMonetaryTotal
);

$result = MyInvois::document(onbehalfof: 'C25845632020')->submit(
    documents: [$selfBilledDebitNote],
    format: Format::XML
);
```

## Checking Submission Status

Same as any other document type - poll `details()` until the status settles:

```php
$uuid = data_get($result, 'data.acceptedDocuments.0.uuid');

// Automatically updates the local myinvois_documents record (status, long_id, etc.)
$details = MyInvois::document()->details($uuid);
```

## Things to Watch For

- **Use a distinct `ID` prefix per document type.** The package's duplicate-submission check is keyed by `client_id` + the document's `ID` only - it does not take document type into account. If an `Invoice` and a `DebitNote` ever share the same `ID`, the second submission is silently treated as an existing document and skipped. Keeping separate sequences (`INV-`, `DN-`, etc.) avoids this entirely.
- **`BillingReference` isn't validated by the package.** If you omit it, or if the `UUID`/`ID` don't correspond to a real, accepted document, MyInvois will reject the submission at the API level - the error surfaces through the normal `rejectedDocuments` handling, not a package-level exception.
- **The original invoice must already be `Valid`** before you can reference it - MyInvois will reject a Debit Note that references a document still pending validation, or one that was rejected/cancelled.
