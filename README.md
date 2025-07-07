# Laravel x MyInvois

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laraditz/my-invois.svg?style=flat-square)](https://packagist.org/packages/laraditz/my-invois)
[![Total Downloads](https://img.shields.io/packagist/dt/laraditz/my-invois.svg?style=flat-square)](https://packagist.org/packages/laraditz/my-invois)
![GitHub Actions](https://github.com/laraditz/my-invois/actions/workflows/main.yml/badge.svg)

Laravel package for interacting with MyInvois (e-Invoice) API.

## Installation

You can install the package via composer:

```bash
composer require laraditz/my-invois
```

## Before Start

Configure your variables in your `.env` (recommended) or you can publish the config file and change it there.

```
MYINVOIS_CLIENT_ID="<your_myinvois_client_id>"
MYINVOIS_CLIENT_SECRET="<your_myinvois_client_secret>"
MYINVOIS_PASSPHRASE="<your_myinvois_passpharase>" // if applicatble
```

(Optional) You can publish the config file via this command:

```bash
php artisan vendor:publish --provider="Laraditz\MyInvois\MyInvoisServiceProvider" --tag="config"
```

Run the migration command to create the necessary database table.

```bash
php artisan migrate
```

## Available Methods

Below are all methods available under this SDK. Refer to [Platform API](https://sdk.myinvois.hasil.gov.my/api/) and [E-Invoice API](https://sdk.myinvois.hasil.gov.my/einvoicingapi/).

| Service name   | Method name | Description                                                                |
| -------------- | ----------- | -------------------------------------------------------------------------- |
| auth()         | token()     | Generate access token for API call.                                        |
| documentType() | all()       | Retrieve list of document types published by the MyInvois System.          |
|                | get()       | Returns document type object with additional details.                      |
|                | versions()  | Returns full document type version object.                                 |
| document()     | submit()    | Allows taxpayer to submit one or more signed documents to MyInvois System. |

## Usage

```php
use Laraditz\MyInvois\Facades\MyInvois;

$accessToken = MyInvois::auth()->token();

$documentType = MyInvois::documentType()->params(['id' => 1])->get();
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email raditzfarhan@gmail.com instead of using the issue tracker.

## Credits

- [Raditz Farhan](https://github.com/laraditz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
