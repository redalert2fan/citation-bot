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

    public function testBioRxivConversionWithVauthors(): void {
        $text = '{{cite journal | vauthors = Watanabe Y, Mendonça L, Allen ER, Howe A, Lee M, Allen JD, Chawla H, Pulido D, Donnellan F, Davies H, Ulaszewska M, Belij-Rammerstorfer S, Morris S, Krebs AS, Dejnirattisai W, Mongkolsapaya J, Supasa P, Screaton GR, Green CM, Lambe T, Zhang P, Gilbert SC, Crispin M | title = Native-like SARS-CoV-2 spike glycoprotein expressed by ChAdOx1 nCoV-19/AZD1222 vaccine | journal = bioRxiv | article-number = 2021.01.15.426463 | date = January 2021 | pmid = 33501433 | pmc = 7836103 | doi = 10.1101/2021.01.15.426463 }}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/2021.01.15.426463', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('doi'));
        $this->assertNull($prepared->get2('journal'));
        $this->assertSame('2021.01.15.426463', $prepared->get2('article-number'));
        $this->assertSame('Watanabe Y, Mendonça L, Allen ER, Howe A, Lee M, Allen JD, Chawla H, Pulido D, Donnellan F, Davies H, Ulaszewska M, Belij-Rammerstorfer S, Morris S, Krebs AS, Dejnirattisai W, Mongkolsapaya J, Supasa P, Screaton GR, Green CM, Lambe T, Zhang P, Gilbert SC, Crispin M', $prepared->get2('vauthors'));
    }

    public function testBioRxivConversionWithLongJournalName(): void {
        $text = '{{cite journal | vauthors = Lyu J, Kapolka N, Gumpper R, Alon A, Wang L, Jain MK, Barros-Álvarez X, Sakamoto K, Kim Y, DiBerto J, Kim K, Tummino TA, Huang S, Irwin JJ, Tarkhanova OO, Moroz Y, Skiniotis G, Kruse AC, Shoichet BK, Roth BL | title = AlphaFold2 structures template ligand discovery | journal = BioRxiv: The Preprint Server for Biology | date = December 2023 | pmid = 38187536 | pmc = 10769324 | doi = 10.1101/2023.12.20.572662 }}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('cite biorxiv', $prepared->wikiname());
        $this->assertSame('10.1101/2023.12.20.572662', $prepared->get2('biorxiv'));
        $this->assertNull($prepared->get2('doi'));
        $this->assertNull($prepared->get2('journal'));
        $this->assertSame('Lyu J, Kapolka N, Gumpper R, Alon A, Wang L, Jain MK, Barros-Álvarez X, Sakamoto K, Kim Y, DiBerto J, Kim K, Tummino TA, Huang S, Irwin JJ, Tarkhanova OO, Moroz Y, Skiniotis G, Kruse AC, Shoichet BK, Roth BL', $prepared->get2('vauthors'));
    }
}
