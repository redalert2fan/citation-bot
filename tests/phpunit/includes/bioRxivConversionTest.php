<?php
declare(strict_types=1);

/*
 * Tests for bioRxiv citation conversion in Template.php
 */

require_once __DIR__ . '/../../testBaseClass.php';

final class bioRxivConversionTest extends testBaseClass {

    public function testBioRxivConversionFromCiteJournal(): void {
        $text = '{{cite journal |last1=Larivière |first1=Vincent |last2=Kiermer |first2=Véronique |last3=MacCallum |first3=Catriona J. |last4=McNutt |first4=Marcia |last5=Patterson |first5=Mark |last6=Pulverer |first6=Bernd |last7=Swaminathan |first7=Sowmya |last8=Taylor |first8=Stuart |last9=Curry |first9=Stephen |date=2016-07-05 |title=A simple proposal for the publication of journal citation distributions |journal=bioRxiv |article-number=062109 |url=http://biorxiv.org/lookup/doi/10.1101/062109 |language=en |doi=10.1101/062109 |hdl=1866/23301 |s2cid=64293941 |hdl-access=free}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/062109', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('doi'));
        $this->assertNull($prepared->get2('journal'));
        $this->assertNull($prepared->get2('article-number'));
        $this->assertNull($prepared->get2('url'));
        $this->assertNull($prepared->get2('hdl'));
        $this->assertNull($prepared->get2('hdl-access'));
        $this->assertNull($prepared->get2('s2cid'));
        $this->assertSame('A simple proposal for the publication of journal citation distributions', $prepared->get2('title'));
        $this->assertSame('en', $prepared->get2('language'));
        $this->assertSame('2016-07-05', $prepared->get2('date'));
        $this->assertSame('Larivière', $prepared->get2('last1'));
        $this->assertSame('Vincent', $prepared->get2('first1'));
    }

    public function testBioRxivConversionFromCitation(): void {
        $text = '{{citation |last=Smith |first=John |title=Test Article |journal=bioRxiv |doi=10.1101/123456 |date=2020-01-01}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/123456', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('doi'));
        $this->assertNull($prepared->get2('journal'));
        $this->assertSame('cs2', $prepared->get2('mode'));
        $this->assertSame('Test Article', $prepared->get2('title'));
        $this->assertSame('Smith', $prepared->get2('last'));
        $this->assertSame('John', $prepared->get2('first'));
    }

    public function testBioRxivConversionCaseInsensitive(): void {
        $text = '{{cite journal |title=Test |journal=BioRxiv |doi=10.1101/999999 |year=2021}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/999999', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('journal'));
    }

    public function testBioRxivConversionFullJournalName(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv: The Preprint Server for Biology |doi=10.1101/888888 |year=2021}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/888888', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('journal'));
    }

    public function testBioRxivConversionAlternateDOI(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv |doi=10.64898/test123 |year=2022}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.64898/test123', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('doi'));
    }

    public function testBioRxivNoConversionWrongDOI(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv |doi=10.1234/wrong |year=2021}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite journal', $prepared->wikiname());
        $this->assertSame('10.1234/wrong', $prepared->get2('doi'));
        $this->assertNull($prepared->get2('biorxiv'));
    }

    public function testBioRxivNoConversionNoDOI(): void {
        $text = '{{cite journal |title=Test |journal=bioRxiv |year=2021}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite journal', $prepared->wikiname());
        $this->assertNull($prepared->get2('biorxiv'));
    }

    public function testBioRxivRemovesUnsupportedParameters(): void {
        $text = '{{cite journal |last=Doe |first=Jane |title=Research Paper |journal=bioRxiv |doi=10.1101/555555 |volume=10 |issue=5 |pages=100-200 |publisher=SomePublisher |isbn=123456789 |pmid=12345678}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/555555', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('volume'));
        $this->assertNull($prepared->get2('issue'));
        $this->assertNull($prepared->get2('pages'));
        $this->assertNull($prepared->get2('publisher'));
        $this->assertNull($prepared->get2('isbn'));
        $this->assertNull($prepared->get2('pmid'));
        // Authors should be preserved
        $this->assertSame('Doe', $prepared->get2('last'));
        $this->assertSame('Jane', $prepared->get2('first'));
    }

    public function testBioRxivKeepsAllowedParameters(): void {
        $text = '{{cite journal |last=Author |first=First |title=Title Here |journal=bioRxiv |doi=10.1101/777777 |date=2020-05-15 |language=en |quote=Some quote |ref=myref}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/777777', $prepared->get2('biorxiv'));
        $this->assertSame('Title Here', $prepared->get2('title'));
        $this->assertSame('2020-05-15', $prepared->get2('date'));
        $this->assertSame('en', $prepared->get2('language'));
        $this->assertSame('Some quote', $prepared->get2('quote'));
        $this->assertSame('myref', $prepared->get2('ref'));
        $this->assertSame('Author', $prepared->get2('last'));
        $this->assertSame('First', $prepared->get2('first'));
    }

    public function testBioRxivPreservesMultipleAuthors(): void {
        $text = '{{cite journal |last1=Smith |first1=John |last2=Doe |first2=Jane |last3=Johnson |first3=Bob |title=Collaborative Work |journal=bioRxiv |doi=10.1101/111222 |year=2019}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('Smith', $prepared->get2('last1'));
        $this->assertSame('John', $prepared->get2('first1'));
        $this->assertSame('Doe', $prepared->get2('last2'));
        $this->assertSame('Jane', $prepared->get2('first2'));
        $this->assertSame('Johnson', $prepared->get2('last3'));
        $this->assertSame('Bob', $prepared->get2('first3'));
    }

    public function testBioRxivPreservesEditors(): void {
        $text = '{{cite journal |author=Writer |editor1=Editor1 |editor1-last=EditorLast |editor1-first=EditorFirst |title=Edited Work |journal=bioRxiv |doi=10.1101/333444 |year=2018}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('Editor1', $prepared->get2('editor1'));
        $this->assertSame('EditorLast', $prepared->get2('editor1-last'));
        $this->assertSame('EditorFirst', $prepared->get2('editor1-first'));
    }

    public function testBioRxivPreservesVariousAuthorFormats(): void {
        $text = '{{cite journal |author3=Third |author5-last=Fifth |author-link7=Link7 |last10=Tenth |title=Many Authors |journal=bioRxiv |doi=10.1101/999888 |year=2020}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('Third', $prepared->get2('author3'));
        $this->assertSame('Fifth', $prepared->get2('author5-last'));
        $this->assertSame('Link7', $prepared->get2('author-link7'));
        $this->assertSame('Tenth', $prepared->get2('last10'));
    }

    public function testBioRxivPreservesPageAliases(): void {
        $text = '{{cite journal |last=Author |title=Title |journal=bioRxiv |doi=10.1101/777888 |pp=100-200 |year=2021}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('100-200', $prepared->get2('pp'));
    }
}
