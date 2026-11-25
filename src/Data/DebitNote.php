<?php

namespace Laraditz\MyInvois\Data;

/**
 * Debit Note (LHDN e-Invoice type code 03).
 *
 * MyInvois validates every document type against the UBL Invoice schema, so a
 * debit note keeps the <Invoice> root element and the Invoice-2 namespace
 * inherited from Invoice. Submitting a <DebitNote> root (DebitNote-2) is
 * rejected by the structure validator. The document type is set by
 * InvoiceTypeCode, and the original invoice is linked through BillingReference.
 *
 * @see https://sdk.myinvois.hasil.gov.my/documents/debit-v1-2/
 */
class DebitNote extends Invoice
{
}
