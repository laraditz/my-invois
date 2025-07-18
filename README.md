# Laravel x MyInvois

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laraditz/my-invois.svg?style=flat-square)](https://packagist.org/packages/laraditz/my-invois)
[![Total Downloads](https://img.shields.io/packagist/dt/laraditz/my-invois.svg?style=flat-square)](https://packagist.org/packages/laraditz/my-invois)
[![License](https://img.shields.io/packagist/l/laraditz/my-invois.svg?style=flat-square)](./LICENSE.md)

### A Developer-Friendly Laravel SDK for MyInvois e-Invoicing

Easily integrate with **MyInvois**, the official e-Invoicing platform by **Lembaga Hasil Dalam Negeri Malaysia (LHDNM)**, using this powerful Laravel SDK. MyInvois enables taxpayers to seamlessly submit issued documents to the tax authority and receive real-time updates on document statuses.

This package provides a clean, object-oriented interface for creating, managing, and sending e-Invoices—helping you stay compliant with Malaysia’s digital tax regulations while keeping your codebase elegant and maintainable.

> [!WARNING]  
> This SDK is still actively under development and may contain bugs. Use at your own risk.

<a href="https://www.buymeacoffee.com/raditzfarhan" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 50px !important;width: 200px !important;" ></a>

## Installation

### Requirements

- PHP >= 8.2
- Laravel >= 11.0
- Composer

## Quick Start

Here's a complete guide to get started with this package:

### 1. Install Package

```bash
composer require laraditz/my-invois
```

### 2. Configure Environment Variables

Configure your variables in your `.env` file (recommended) or publish the config file and change it there.

**Required Variables:**

```
MYINVOIS_CLIENT_ID="<your_client_id>"
MYINVOIS_CLIENT_SECRET="<your_client_secret>"
```

**Optional Variables:**

```
MYINVOIS_PASSPHRASE="<your_passpharase>" // if required
MYINVOIS_SANDBOX=true // set true for testing, false for production
MYINVOIS_DISK="local" // disk for storing documents
MYINVOIS_DOCUMENT_PATH="myinvois/" // path for storing documents
MYINVOIS_CERTIFICATE_PATH="/path/to/certificate.p12" // path to certificate file
MYINVOIS_PRIVATE_KEY_PATH="/path/to/private_key.pem" // path to private key file
MYINVOIS_ON_BEHALF_OF=C258456320XX // Taxpayer's TIN or ROB number
```

### 3. Publish Config (Optional)

You can publish the config file via this command:

```bash
php artisan vendor:publish --provider="Laraditz\MyInvois\MyInvoisServiceProvider" --tag="config"
```

### 4. Publish Migration

You can publish the migration file via this command:

```bash
php artisan vendor:publish --provider="Laraditz\MyInvois\MyInvoisServiceProvider" --tag="migrations"
```

### 5. Run Migration

Run the migration command to create the necessary database tables:

```bash
php artisan migrate
```

### 6. Run Seeder

Run the seeder to furnish necessary data:

```bash
php artisan db:seed --class=Laraditz\\MyInvois\\Database\\Seeders\\DatabaseSeeder
```

### 7. Test Connection

Test your setup with a simple authentication call:

```php
use Laraditz\MyInvois\Facades\MyInvois;

// Test authentication
$token = MyInvois::auth()->token(); // will throw an error if failed
echo "Connection successful!";
```

## Available Methods

Below are all methods available under this SDK. Refer to [Platform API](https://sdk.myinvois.hasil.gov.my/api/) and [E-Invoice API](https://sdk.myinvois.hasil.gov.my/einvoicingapi/) for more information.

### Authentication Service `auth()`

| Method    | Description                        | Parameters                                                        |
| --------- | ---------------------------------- | ----------------------------------------------------------------- |
| `token()` | Generate access token for API call | `client_id`, `client_secret`, `grant_type`, `scope`, `onbehalfof` |

### Document Type Service `documentType()`

| Method      | Description                    | Parameters  |
| ----------- | ------------------------------ | ----------- |
| `all()`     | Get list of all document types | -           |
| `get()`     | Get document type by ID        | `id`        |
| `version()` | Get document type version      | `id`, `vid` |

### Document Service `document()`

| Method      | Description                                                                                | Parameters                      |
| ----------- | ------------------------------------------------------------------------------------------ | ------------------------------- |
| `submit()`  | Submit one or more signed documents                                                        | `documents[]`, `format`         |
| `recent()`  | Return documents that are issued within the last 31 days                                   | Refer doc                       |
| `search()`  | Query the system and return list of documents that have been received or issued            | `submissionDateFrom`. Refer doc |
| `get()`     | Get raw details of the document                                                            | `uuid`                          |
| `details()` | Get full details of the document                                                           | `uuid`                          |
| `cancel()`  | Allows issuer to cancel previously issued document                                         | `uuid`, `reason`                |
| `reject()`  | Allows a buyer that received an invoice to reject it and request the supplier to cancel it | `uuid`, `reason`                |

### Document Submission Service `documentSubmission()`

| Method  | Description                                                                                     | Parameters      |
| ------- | ----------------------------------------------------------------------------------------------- | --------------- |
| `get()` | Get details of a single submission to check its processing status after initially submitting it | `submissionUid` |

### Notification Service `notification()`

| Method  | Description                        | Parameters |
| ------- | ---------------------------------- | ---------- |
| `all()` | Get all notifications for taxpayer | -          |

### Taxpayer Service `taxpayer()`

| Method          | Description                                           | Parameters                          |
| --------------- | ----------------------------------------------------- | ----------------------------------- |
| `validateTin()` | Validate TIN (Tax Identification Number)              | `tin`, `idType`, `idValue`          |
| `searchTin()`   | Search for a specific Tax Identification Number (TIN) | `idType`, `idValue`, `taxpayerName` |

### Document Generation Methods

| Method               | Description                             | Parameters                        | Return type |
| -------------------- | --------------------------------------- | --------------------------------- | ----------- |
| `generateDocument()` | Generate document in XML or JSON format | `Invoice $data`, `Format $format` | `string`    |

## Usage

### Basic Authentication

You do not need to perform the authentication manually. The SDK will know when to get and append the access token based on your API request.

```php
use Laraditz\MyInvois\Facades\MyInvois;

// Get access token
$accessToken = MyInvois::auth()->token();

// Or with specific parameters
$accessToken = MyInvois::auth()->token(
    client_id: 'your_client_id',
    client_secret: 'your_client_secret',
    grant_type: 'client_credentials',
    scope: 'InvoicingAPI'
);

// Get access token on behalf of
$accessToken = MyInvois::auth()->token(onbehalfof: 'C25845632020'); // taxpayer's TIN or ROB number
```

### Document Types

```php
use Laraditz\MyInvois\Facades\MyInvois;

// Get all document types
$documentTypes = MyInvois::documentType()->all();

// Get document type by ID
$documentType = MyInvois::documentType()->get(1);

// Get document type version
$version = MyInvois::documentType()->version(id: 1, vid: 2);
```

### Document Submission

```php
use Laraditz\MyInvois\Facades\MyInvois;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Enums\Format;

// Create Invoice object
$invoice = new Invoice(
    ID: 'INV-001',
    IssueDate: now(),
    IssueTime: now(),
    InvoiceTypeCode: new Data('380'), // Standard Invoice
    DocumentCurrencyCode: 'MYR',
    // ... add other required data
);

// Submit document
$result = MyInvois::document()->submit(
    documents: [$invoice], // can submit multiple invoices
    format: Format::XML
);
```

### Document Details

```php
use Laraditz\MyInvois\Facades\MyInvois;

$uuid = 'JEEA7W331XXXNBAXXX71880XXX';
// Automatically update the record in myinvois_documents table such as status, long_id etc.
$details = MyInvois::document()->details($uuid);
```

### Taxpayer Validation

```php
use Laraditz\MyInvois\Facades\MyInvois;

// Validate TIN
$validation = MyInvois::taxpayer()->validateTin(tin: 'AB123456789012', idType: 'NRIC', idValue: '200101011234');
```

### Notification Retrieval

```php
use Laraditz\MyInvois\Facades\MyInvois;

// Get all notifications
$notifications = MyInvois::notification()->all();
```

### Document Generation

Typically you won't need to generate the document as you will be using the Document Submission service. But if you want to manually generate the document for debugging or other purposes, you able to do so using below code.

```php
use Laraditz\MyInvois\Facades\MyInvois;
use Laraditz\MyInvois\Data\Invoice;
use Laraditz\MyInvois\Enums\Format;

// Create Invoice object
$invoice = new Invoice(
    ID: 'INV-001',
    IssueDate: now(),
    IssueTime: now(),
    InvoiceTypeCode: new Data('380'),
    DocumentCurrencyCode: 'MYR',
    // ... other data
);

// Generate XML document
$xmlDocument = MyInvois::generateDocument($invoice, Format::XML);

// Then, to display the xml on browser
// MyInvois::helper()->displayXml($xmlDocument);
```

### Advanced Usage with Query String, Payload and Params

The service offers a flexible, fluent interface that lets you dynamically configure parameters on the fly—**right before the HTTP request is sent**. Effortlessly chain methods like `payload()` for the request body, `queryString()` for URL queries, and `params()` for path parameters, all after invoking the service method.

```php
use Laraditz\MyInvois\Facades\MyInvois;

// Using query string
$result = MyInvois::documentType()
    ->queryString(['page' => 1, 'limit' => 10])
    ->all();

// Using params
$result = MyInvois::documentType()
    ->params(['id' => 1, 'vid' => 2])
    ->version();
```

### Error Handling

```php
use Laraditz\MyInvois\Facades\MyInvois;
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;

try {
    $result = MyInvois::document()->submit(
        documents: [$invoice],
        format: Format::XML
    );

    if ($result['success']) {
        echo "Document submitted successfully. Request ID: " . $result['request_id'];
    }
} catch (MyInvoisApiError $e) {
    echo "Error: " . $e->getMessage();
} catch (\Throwable $th) {
    throw $th;
}
```

### Sandbox Mode

For testing, you can use sandbox mode:

```php
// In .env (Recommended)
MYINVOIS_SANDBOX=true

// Or in config (Not recommended)
'MYINVOIS_SANDBOX' => true
```

### Certificate and Signature

For documents that require digital signature:

```php
// Make sure certificate and private key paths are correct in .env. The path can be absolute path or relative to your application.
MYINVOIS_CERTIFICATE_PATH="/path/to/certificate.p12"
MYINVOIS_PRIVATE_KEY_PATH="/path/to/private_key.pem"
MYINVOIS_PASSPHRASE="your_passphrase" // if needed

// Package will automatically add signature if certificate exists
```

More information on Signature: [Signature](https://sdk.myinvois.hasil.gov.my/signature/) | [Signature Creation](https://sdk.myinvois.hasil.gov.my/signature-creation/)

### Complete Example: Creating and Submitting Invoice

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
use Laraditz\MyInvois\Enums\Format;

// Create supplier party
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

// Create customer party
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

// Create invoice line
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

// Create tax total
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

// Create legal monetary total
$legalMonetaryTotal = new LegalMonetaryTotal(
    LineExtensionAmount: new Money(200.00, 'MYR'),
    TaxExclusiveAmount: new Money(200.00, 'MYR'),
    TaxInclusiveAmount: new Money(212.00, 'MYR'),
    PayableAmount: new Money(212.00, 'MYR')
);

// Create invoice
$invoice = new Invoice(
    ID: 'INV-2025-001',
    IssueDate: now(),
    IssueTime: now(),
    InvoiceTypeCode: new Data('380'), // Standard Invoice
    DocumentCurrencyCode: 'MYR',
    AccountingSupplierParty: new AccountingSupplierParty($supplierParty),
    AccountingCustomerParty: new AccountingCustomerParty($customerParty),
    InvoiceLine: [$invoiceLine],
    TaxTotal: $taxTotal,
    LegalMonetaryTotal: $legalMonetaryTotal
);

// Submit invoice
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
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Supported Data Structures

This package supports UBL (Universal Business Language) data structures for e-invoice:

- **Invoice**: Main invoice document
- **Party**: Supplier and customer information
- **Address**: Postal address
- **Contact**: Contact information
- **InvoiceLine**: Invoice line items
- **Item**: Product/service information
- **Price**: Item pricing
- **TaxCategory**: Tax categories
- **TaxTotal**: Tax totals
- **LegalMonetaryTotal**: Legal monetary totals
- **Money**: Monetary values with currency

### Environment Variables Reference

| Variable                    | Description                            | Default                  | Required |
| --------------------------- | -------------------------------------- | ------------------------ | -------- |
| `MYINVOIS_CLIENT_ID`        | Client ID from MyInvois                | -                        | Yes      |
| `MYINVOIS_CLIENT_SECRET`    | Client Secret from MyInvois            | -                        | Yes      |
| `MYINVOIS_PASSPHRASE`       | Passphrase for certificate             | -                        | No       |
| `MYINVOIS_SANDBOX`          | Sandbox mode for testing               | false                    | No       |
| `MYINVOIS_DISK`             | Disk for storing documents             | local                    | No       |
| `MYINVOIS_DOCUMENT_PATH`    | Path for storing documents             | myinvois/                | No       |
| `MYINVOIS_CERTIFICATE_PATH` | Path to certificate file               | storage/app/myinvois.p12 | No       |
| `MYINVOIS_PRIVATE_KEY_PATH` | Path to private key file               | storage/app/myinvois.pem | No       |
| `MYINVOIS_ON_BEHALF_OF`     | Taxpayer's TIN for intermediary system | null                     | No       |

### Best Practices

1. **Error Handling**: Always use try-catch to handle errors
2. **Validation**: Validate data before sending to API
3. **Logging**: Use logging for tracking requests and responses
4. **Testing**: Use sandbox mode for testing
5. **Security**: Ensure certificates and private keys are stored securely
6. **Monitoring**: Monitor request history for debugging

### Troubleshooting

**Error: Missing Client ID/Secret**

- Ensure `MYINVOIS_CLIENT_ID` and `MYINVOIS_CLIENT_SECRET` are set in `.env`

**Error: Certificate not found**

- Ensure certificate and private key paths are correct
- Check file permissions

**Error: Invalid document format**

- Ensure document follows correct UBL format
- Check all required fields

**Error: API timeout**

- Check internet connection
- Try again after a few minutes

### Database Tables

This package will create the following tables when migration is run:

- `myinvois_clients` - Store client information
- `myinvois_access_tokens` - Store access tokens
- `myinvois_requests` - Store all requests and responses
- `myinvois_documents` - Store submitted documents
- `myinvois_document_histories` - Store previously submitted documents
- `myinvois_msic_codes` - Store MSIC codes

### Exception Handling

This package provides several exceptions for error handling:

#### MyInvoisApiError

Exception for MyInvois API errors:

```php
use Laraditz\MyInvois\Exceptions\MyInvoisApiError;

try {
    $result = MyInvois::document()->submit($data);
} catch (MyInvoisApiError $e) {
    // Handle API error
    Log::error('MyInvois API Error: ' . $e->getMessage());
} catch (\Throwable $th) {
    // Handle other errors
    throw $th;
}
```

#### MyInvoisException

Exception for general package errors:

```php
use Laraditz\MyInvois\Exceptions\MyInvoisException;

try {
    $result = MyInvois::document()->submit($data);
} catch (MyInvoisException $e) {
    // Handle general package error
    Log::error('MyInvois Error: ' . $e->getMessage());
} catch (\Throwable $th) {
    // Handle other errors
    throw $th;
}
```

### To Do

- [ ] Add all APIs
- [ ] Add support for JSON document
- [ ] Add complete example for creating invoice
- [ ] Convert some enum into DB table + seeder?
- [ ] Add documentation
- [ ] Add test

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Support

If you encounter issues or have questions:

1. **Documentation**: Refer to official [MyInvois documentation](https://sdk.myinvois.hasil.gov.my/)
2. **Issues**: Open an issue on GitHub repository
3. **Email**: Send email to raditzfarhan@gmail.com

### Security

If you discover any security related issues, please email raditzfarhan@gmail.com instead of using the issue tracker.

## Credits

- [Raditz Farhan](https://github.com/laraditz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
