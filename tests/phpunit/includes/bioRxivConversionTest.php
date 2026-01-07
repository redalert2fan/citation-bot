<?php
declare(strict_types=1);

/*
 * Tests for bioRxiv citation conversion (cite journal only)
 */

require_once __DIR__ . '/../../testBaseClass.php';

final class bioRxivConversionTest extends testBaseClass {

    public function testBioRxivConversionSimple(): void {
        $text = '{{cite journal |last=Smith |first=John |title=Test Paper |journal=bioRxiv |doi=10.1101/123456 |year=2023}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/123456', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('doi'));
        $this->assertNull($prepared->get2('journal'));
    }

    public function testBioRxivConversionCaseInsensitive(): void {
        $text = '{{cite journal |title=Test |journal=BioRxiv |doi=10.1101/999999}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/999999', $prepared->get2('biorxiv'));
    }

    public function testBioRxivConversionAlternateDOI(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv |doi=10.64898/abc123}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.64898/abc123', $prepared->get2('biorxiv'));
    }

    public function testBioRxivConversionParameterFiltering(): void {
        $text = '{{cite journal |last1=Larivière |first1=Vincent |title=A simple proposal |journal=bioRxiv |doi=10.1101/062109 |volume=10 |issue=3 |pages=1-10 |hdl=1866/23301 |s2cid=64293941}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/062109', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('volume'));
        $this->assertNull($prepared->get2('issue'));
        $this->assertNull($prepared->get2('hdl'));
        $this->assertNull($prepared->get2('s2cid'));
    }

    public function testBioRxivNoConversionWrongDOI(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv |doi=10.1234/wrong}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite journal', $prepared->wikiname());
        $this->assertSame('10.1234/wrong', $prepared->get2('doi'));
    }

    public function testBioRxivNoConversionNoDOI(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite journal', $prepared->wikiname());
        $this->assertNull($prepared->get2('doi'));
    }
}
