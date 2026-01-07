<?php
declare(strict_types=1);

/*
 * Tests for bioRxiv citation conversion in Template.php
 */

require_once __DIR__ . '/../../testBaseClass.php';

final class bioRxivConversionTest extends testBaseClass {

    public function testBioRxivConversionSimple(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv |doi=10.1101/123456}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/123456', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('doi'));
        $this->assertNull($prepared->get2('journal'));
    }

    public function testBioRxivConversionFromCitation(): void {
        $text = '{{citation |title=Test |journal=bioRxiv |doi=10.1101/234567}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/234567', $prepared->get2('biorxiv'));
        $this->assertSame('cs2', $prepared->get2('mode'));
        $this->assertNull($prepared->get2('doi'));
        $this->assertNull($prepared->get2('journal'));
    }
}
