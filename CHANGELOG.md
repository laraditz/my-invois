# Changelog

All notable changes to `laraditz/my-invois` will be documented in this file

## 0.0.1 - 2025-07-08

### Added

- Add `FUNDING.yml`.

### Changed

- Update `README.md`.

## 0.0.0 - 2025-07-08

- initial release

### Added

- Add service provirder, config, main `MyInvois` class and facade.
- Add `BaseService` class to interact with API.
- Add `auth` service with `token` method.
- Add `documentType` service with `all`, `get`, `version` methods.
- Add `document` service with `submit` method.
- Add `notification` service with `all` method.
- Add `taxpayer` service with `validate` method.
- Add `MyinvoisAccessToken`, `MyinvoisClient`, `MyinvoisRequest`, `MyinvoisDocument`, `MyinvoisDocumentHistory` models with associated tables.
- Add `MyInvoisCertificate` class to store certificate info.
- Add `MyInvoisSignature` class to create and manage signature.
- Add `MyInvoisHelper` class for helper methods.
- Add `Format`, `InvoiceType`, `PaymentMode` and `XMLNS` enums.
- Add `HasAttributes` trait and `Attributes` class.
- Add `Invoice` data object and other necessary data class objects to create the Invoice.
- Add `WithAttributes`, `WithNamespace`, `WithValue` interfaces.
- Add `README.md` with some basic info about the package.
