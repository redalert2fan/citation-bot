<?php
declare(strict_types=1);

/*
 * Manual test for issue #4830 - Testing real cite book examples
 */

require_once __DIR__ . '/testBaseClass.php';

final class ManualCiteBookTest extends testBaseClass {

    public function testExample1AgrippaVonNettesheim(): void {
        $text = '{{cite book |last1=Agrippa von Nettesheim |first1=Heinrich Cornelius |title=De occulta philosophia libri tres |date=1533 |location=Cologne |pages=160, 163, 276-277 |url=https://www.loc.gov/resource/rbc0001.2009gen12345/?sp=280 |access-date=28 November 2024 }}';
        
        echo "\n=== Test 1: Agrippa von Nettesheim ===\n";
        echo "Original: $text\n";
        
        $expanded = $this->process_citation($text);
        
        echo "After processing: " . $expanded->parsed_text() . "\n";
        echo "Template name: " . $expanded->wikiname() . "\n";
        
        // Verify unsupported parameters were NOT added
        $this->assertNull($expanded->get2('work'), 'work parameter should not be added');
        $this->assertNull($expanded->get2('journal'), 'journal parameter should not be added');
        $this->assertNull($expanded->get2('website'), 'website parameter should not be added');
        $this->assertNull($expanded->get2('newspaper'), 'newspaper parameter should not be added');
        $this->assertNull($expanded->get2('magazine'), 'magazine parameter should not be added');
        
        echo "✓ No unsupported parameters added\n";
    }

    public function testExample2WinterOfYaluWithoutWork(): void {
        $text = '{{cite book |url=https://www.americanheritage.com/winter-yalu|author1=Dill, James|title=Winter of the Yalu|publisher=Changjin Journal|date=December 1982|quote=A soldier remembers the freezing, fearful retreat down the Korean Peninsula after the Chinese armies smashed across the border|archive-url=https://web.archive.org/web/20230407083459/https://www.americanheritage.com/winter-yalu|archive-date= 7 April 2023}}';
        
        echo "\n=== Test 2: Winter of the Yalu (without work) ===\n";
        echo "Original: $text\n";
        
        $expanded = $this->process_citation($text);
        
        echo "After processing: " . $expanded->parsed_text() . "\n";
        echo "Template name: " . $expanded->wikiname() . "\n";
        
        // Verify unsupported parameters were NOT added
        $this->assertNull($expanded->get2('work'), 'work parameter should not be added');
        $this->assertNull($expanded->get2('journal'), 'journal parameter should not be added');
        $this->assertNull($expanded->get2('website'), 'website parameter should not be added');
        $this->assertNull($expanded->get2('newspaper'), 'newspaper parameter should not be added');
        $this->assertNull($expanded->get2('magazine'), 'magazine parameter should not be added');
        
        echo "✓ No unsupported parameters added\n";
    }

    public function testExample3WinterOfYaluWithWork(): void {
        $text = '{{cite book |url=https://www.americanheritage.com/winter-yalu|author1=Dill, James|title=Winter of the Yalu|work=AMERICAN HERITAGE |publisher=Changjin Journal|date=December 1982|quote=A soldier remembers the freezing, fearful retreat down the Korean Peninsula after the Chinese armies smashed across the border|archive-url=https://web.archive.org/web/20230407083459/https://www.americanheritage.com/winter-yalu|archive-date= 7 April 2023}}';
        
        echo "\n=== Test 3: Winter of the Yalu (with work=AMERICAN HERITAGE) ===\n";
        echo "Original: $text\n";
        
        $expanded = $this->process_citation($text);
        
        echo "After processing: " . $expanded->parsed_text() . "\n";
        echo "Template name: " . $expanded->wikiname() . "\n";
        
        // Verify work parameter is PRESERVED (respecting human edit)
        $work_value = $expanded->get2('work');
        echo "work parameter: " . ($work_value ? "'$work_value'" : 'NOT PRESENT') . "\n";
        
        $this->assertNotNull($work_value, 'work parameter should be preserved when already present');
        $this->assertSame('AMERICAN HERITAGE', trim($work_value), 'work parameter value should be preserved');
        
        echo "✓ Existing work parameter preserved\n";
    }

    public function testExample4ProgressInOptics(): void {
        $text = '{{cite book |last1=Berry |first1=M. V. |title=Progress in Optics Volume 50 |date=2007-01-01 |volume=50 |pages=13–50 |editor-last=Wolf |editor-first=E. |chapter-url=https://www.sciencedirect.com/science/article/pii/S0079663807500028 |access-date=2024-04-23 |publisher=Elsevier |last2=Jeffrey |first2=M. R. |chapter=Chapter 2 Conical diffraction: Hamilton\'s diabolical point at the heart of crystal optics |doi=10.1016/S0079-6638(07)50002-8 |bibcode=2007PrOpt..50...13B |isbn=978-0-444-53023-3 }}';
        
        echo "\n=== Test 4: Progress in Optics (Berry & Jeffrey) ===\n";
        echo "Original: $text\n";
        
        $expanded = $this->process_citation($text);
        
        echo "After processing: " . $expanded->parsed_text() . "\n";
        echo "Template name: " . $expanded->wikiname() . "\n";
        
        // This is a book chapter with DOI and bibcode
        // Verify unsupported parameters were NOT added even with external identifiers
        $this->assertNull($expanded->get2('work'), 'work parameter should not be added');
        $this->assertNull($expanded->get2('journal'), 'journal parameter should not be added');
        $this->assertNull($expanded->get2('website'), 'website parameter should not be added');
        $this->assertNull($expanded->get2('newspaper'), 'newspaper parameter should not be added');
        $this->assertNull($expanded->get2('magazine'), 'magazine parameter should not be added');
        
        // Verify it stays as cite book (has chapter parameter)
        $this->assertSame('cite book', $expanded->wikiname(), 'should remain as cite book');
        $this->assertNotNull($expanded->get2('chapter'), 'should have chapter parameter');
        
        echo "✓ No unsupported parameters added to book chapter with DOI/bibcode\n";
    }
}
