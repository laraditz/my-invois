# Changelog

All notable changes to `laraditz/my-invois` will be documented in this file

## 0.0.4 - 2025-07-16

### Changed

- Fix bug `DigestValue` for Cert Digest need to base64 decode first.
- Fix bug when generating hashed `SignedProperties`. Need extra attributes when hashing as compared to final document.

## 0.0.3 - 2025-07-14

### Added

- Add `InvoiceTypeCode`, `IdentificationCode` data.
- Add `Frequency` enum for date period frequency options (Daily, Weekly, Biweekly, Monthly, Bimonthly, Quarterly, HalfYearly, Yearly, NotApplicable).
- Add `MyinvoisMsicCode` model with migration and seeder for MSIC (Malaysian Standard Industrial Classification) codes.
- Add `MSICSubCategoryCodes.json` data file containing comprehensive MSIC codes and descriptions. Ref: https://sdk.myinvois.hasil.gov.my/codes/msic-codes/
- Add `displayXml()` helper method to display XML content in browser for debugging purposes.
- Add `removeXMLTag()` helper method to remove XML declaration tags from XML strings.
- Add `isAbsolutePath()` helper method to check if a file path is absolute.

### Changed

- Move Certificate initialization from `MyInvoisSignature` to `MyInvois`.
- Auto-convert certificate path to absolute path if path provided is relative.
- Update `DatePeriod` data class to use the new `Frequency` enum for better type safety.
- Improve XML handling utilities with better formatting and validation methods.

### Removed

- Remove `MSIC` enum.

## 0.0.2 - 2025-07-09

### Added

- Add `Classification`, `Country`, `Currency`, `MSIC`, `State`, `TaxType` and `MeasureUnit` enums.

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
