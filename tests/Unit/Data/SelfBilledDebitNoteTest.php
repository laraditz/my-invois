<?php

namespace Laraditz\MyInvois\Tests\Unit\Data;

use Laraditz\MyInvois\Data\DebitNote;
use Laraditz\MyInvois\Data\SelfBilledDebitNote;
use Laraditz\MyInvois\Tests\TestCase;

class SelfBilledDebitNoteTest extends TestCase
{
    public function test_it_is_a_debit_note()
    {
        $selfBilledDebitNote = new SelfBilledDebitNote();

        $this->assertInstanceOf(DebitNote::class, $selfBilledDebitNote);
    }

    public function test_it_shares_debit_notes_root_element_and_namespace()
    {
        $selfBilledDebitNote = new SelfBilledDebitNote();

        $this->assertSame('DebitNote', $selfBilledDebitNote->getRootElement());
        $this->assertSame(
            'urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2',
            $selfBilledDebitNote->getDocumentNamespace()
        );
    }
}
