<?php
declare(strict_types=1);

require_once __DIR__ . '/../../testBaseClass.php';

final class TextToolsTest extends TestBaseClass {

    public function testCapitalization1a(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('Molecular and Cellular Biology', title_capitalization(title_case('Molecular and cellular biology'), true));
    }

    public function testCapitalization1b(): void {
        $this->assertSame('z/Journal', title_capitalization(title_case('z/Journal'), true));
    }

    public function testCapitalization1c(): void {
        $this->assertSame('The Journal of Journals', title_capitalization('The Journal Of Journals', true));
    }

    public function testCapitalization1d(): void {
        $this->assertSame('A Journal of Chemistry A', title_capitalization('A Journal of Chemistry A', true));
    }

    public function testCapitalization1e(): void {
        $this->assertSame('A Journal of Chemistry E', title_capitalization('A Journal of Chemistry E', true));
    }

    public function testCapitalization2a(): void {
        $this->assertSame('This a Journal', title_capitalization('THIS A JOURNAL', true));
    }

    public function testCapitalization2b(): void {
        $this->assertSame('This a Journal', title_capitalization('THIS A JOURNAL', true));
    }

    public function testCapitalization2c(): void {
        $this->assertSame("THIS 'A' JOURNAL mittEilUngen", title_capitalization("THIS `A` JOURNAL mittEilUngen", true));
    }

    public function testCapitalization3(): void {
        $this->assertSame('[Johsnon And me]', title_capitalization('[Johsnon And me]', true)); // Do not touch links
    }

    public function testCapitalization4(): void {
        $this->assertSame('This is robert WWW', title_capitalization('This is robert www', true));
    }

    public function testCapitalization5(): void {
        $this->assertSame('This is robert http://', title_capitalization('This is robert http://', true));
    }

    public function testCapitalization6(): void {
        $this->assertSame('This is robert www.', title_capitalization('This is robert www.', true));
    }

    public function testCapitalization7(): void {
        $this->assertSame('This is robert www-', title_capitalization('This is robert www-', true));
    }

    public function testCapitalization8a(): void {
        $this->assertSame('I the Las Vegas.  Trip.', title_capitalization('I the las Vegas.  Trip.', true));
    }

    public function testCapitalization8b(): void {
        $this->assertSame('I the Las Vegas,  Trip.', title_capitalization('I the las Vegas,  Trip.', true));
    }

    public function testCapitalization8c(): void {
        $this->assertSame('I the Las Vegas:  Trip.', title_capitalization('I the las Vegas:  Trip.', true));
    }

    public function testCapitalization8d(): void {
        $this->assertSame('I the Las Vegas;  Trip.', title_capitalization('I the las Vegas;  Trip.', true));
    }

    public function testCapitalization8e(): void {
        $this->assertSame('I the las Vegas...Trip.', title_capitalization('I the las Vegas...Trip.', true));
    }

    public function testCapitalization9(): void {
        $this->assertSame('SAGE Open', title_capitalization('Sage Open', true));
    }

    public function testCapitalization10(): void {
        $this->assertSame('CA', title_capitalization('Ca', true));
    }

    public function testCapitalization11(): void {
        $this->assertSame('The Series A and B qu', title_capitalization('The Series a and b qu', true));
    }

    public function testCapitalization12(): void {
        $this->assertSame('PEN International', title_capitalization('Pen International', true));
    }

    public function testCapitalization13(): void {
        $this->assertSame('Time Off', title_capitalization('Time off', true));
    }

    public function testCapitalization14(): void {
        $this->assertSame('IT Professional', title_capitalization('It Professional', true));
    }

    public function testCapitalization15(): void {
        $this->assertSame('JOM', title_capitalization('Jom', true));
    }

    public function testFrenchCapitalization1(): void {
        $this->assertSame("L'Aerotecnica", title_capitalization(title_case("L'Aerotecnica"), true));
    }

    public function testFrenchCapitalization2(): void {
        $this->assertSame("PhÃƒÂ©nomÃƒÂ¨nes d'Ãƒâ€°vaporation d'Hydrologie", title_capitalization(title_case("PhÃƒÂ©nomÃƒÂ¨nes d'Ãƒâ€°vaporation dÃ¢â‚¬â„¢hydrologie"), true));
    }

    public function testFrenchCapitalization3(): void {
        $this->assertSame("D'Hydrologie PhÃƒÂ©nomÃƒÂ¨nes d'Ãƒâ€°vaporation d'Hydrologie l'Aerotecnica", title_capitalization("D'Hydrologie PhÃƒÂ©nomÃƒÂ¨nes d&#x2019;Ãƒâ€°vaporation d&#8217;Hydrologie l&rsquo;Aerotecnica", true));
    }

    public function testITS(): void {
        $this->assertSame(                       "Keep case of its Its and ITS",
                            title_capitalization("Keep case of its Its and ITS", true));
        $this->assertSame(                       "ITS Keep case of its Its and ITS",
                            title_capitalization("ITS Keep case of its Its and ITS", true));
    }

    public function testTidyDate1(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('2014', tidy_date('maanantai 14. heinÃƒÂ¤kuuta 2014'));
        $this->assertSame('2012-04-20', tidy_date('2012Ã¥Â¹Â´4Ã¦Å“Ë†20Ã¦â€”Â¥ Ã¦ËœÅ¸Ã¦Å“Å¸Ã¤Âºâ€'));
        $this->assertSame('2011-05-10', tidy_date('2011-05-10T06:34:00-0400'));
        $this->assertSame('July 2014', tidy_date('2014-07-01T23:50:00Z, 2014-07-01'));
        $this->assertSame('', tidy_date('Ã›Â±Ã›Â³Ã›Â¸Ã›Â¶/Ã›Â±Ã›Â°/Ã›Â°Ã›Â´ - Ã›Â±Ã›Â±:Ã›Â³Ã›Â°'));
    }

    public function testTidyDate2(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('2014-01-24', tidy_date('01/24/2014 16:01:06'));
        $this->assertSame('2011-11-30', tidy_date('30/11/2011 12:52:08'));
        $this->assertSame('2011', tidy_date('05/11/2011 12:52:08'));
        $this->assertSame('2011-11-11', tidy_date('11/11/2011 12:52:08'));
        $this->assertSame('2018-10-21', tidy_date('Date published (2018-10-21'));
        $this->assertSame('2008-04-29', tidy_date('07:30 , 04.29.08'));
    }

    public function testTidyDate3(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('', tidy_date('-0001-11-30T00:00:00+00:00'));
        $this->assertSame('', tidy_date('22/22/2010'));  // That is not valid date code
        $this->assertSame('', tidy_date('The date is 88 but not three')); // Not a date, but has some numbers
        $this->assertSame('2016-10-03', tidy_date('3 October, 2016')); // evil comma
    }

    public function testTidyDate4(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('22 October 1999 Ã¢â‚¬â€œ 22 September 2000', tidy_date('1999-10-22 - 2000-09-22'));
        $this->assertSame('22 October Ã¢â‚¬â€œ 22 September 1999', tidy_date('1999-10-22 - 1999-09-22'));
    }

    public function testTidyDate5(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('', tidy_date('Invalid'));
        $this->assertSame('', tidy_date('1/1/0001'));
        $this->assertSame('', tidy_date('0001-01-01'));
        $this->assertSame('', tidy_date('1969-12-31'));
        $this->assertSame('', tidy_date('19xx'));
    }

    public function testTidyDate6(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('', tidy_date('2000 1999-1998'));
        $this->assertSame('', tidy_date('1969-12-31'));
        $this->assertSame('', tidy_date('0011-10-07'));
        $this->assertSame('', tidy_date('4444-10-07'));
    }

    public function testTidyDate7(): void {
        $this->assertSame('1999-09-09', tidy_date('1999-09-09T22:10:11+08:00'));
    }

    public function testTidyDate7b(): void {
        $this->assertSame('2001-11-11', tidy_date('dafdsafsd    2001-11-11'));
    }

    public function testTidyDate8(): void {
        $this->assertSame('2000-03-27', tidy_date('3/27/2000 dafdsafsd dafdsafsd'));
    }

    public function testTidyDate8b(): void {
        $this->assertSame('2000-03-27', tidy_date('dafdsafsd3/27/2000'));
    }

    public function testTidyDate8c(): void {
        $this->assertSame('', tidy_date('23--'));
    }

    public function testTidyDate55(): void {
        $this->assertSame('1800', tidy_date('3 Feb 1800'));
    }

    public function testTidyDate56(): void {
        $this->assertSame('542', tidy_date('3 Feb 0542'));
    }

    public function testTidyDate57(): void {
        $this->assertSame('', tidy_date('-0003-10-22'));
    }

    public function testTidyDateFutureRejection1(): void {
        new TestPage(); // Fill page name with test name for debugging
        // Test date 4 days in the future (should be rejected)
        $future_date = date('Y-m-d', strtotime('+4 days'));
        $this->assertSame('', tidy_date($future_date));
    }

    public function testTidyDateFutureRejection2(): void {
        // Test date 1 year in the future (should be rejected)
        $future_date = date('Y-m-d', strtotime('+1 year'));
        $this->assertSame('', tidy_date($future_date));
    }

    public function testTidyDateFutureAcceptance2(): void {
        // Test date 2 days in the future (should be accepted)
        $two_days_future = date('Y-m-d', strtotime('+2 days'));
        $this->assertNotSame('', tidy_date($two_days_future));
    }

    public function testTidyDateCurrentAcceptance(): void {
        // Test current date (should be accepted)
        $current_date = date('Y-m-d');
        $this->assertNotSame('', tidy_date($current_date));
    }

    public function testTidyDatePastAcceptance(): void {
        // Test past date (should be accepted)
        $this->assertSame('2020-01-15', tidy_date('2020-01-15'));
    }

    public function testTidyDateMultipleFormats(): void {
        // Test that various date formats are parsed correctly and future dates rejected
        // Future dates (>3 days) should be rejected regardless of format
        $this->assertSame('', tidy_date(date('j F Y', strtotime('+1 year'))));
        $this->assertSame('', tidy_date(date('F j, Y', strtotime('+1 year'))));
        $this->assertSame('', tidy_date(date('j M Y', strtotime('+1 year'))));

        // Past dates should be accepted and normalized to ISO format
        $this->assertSame('2020-01-15', tidy_date('15 January 2020'));  // UK format
        $this->assertSame('2020-01-15', tidy_date('January 15, 2020')); // US format
        $this->assertSame('2020-01-15', tidy_date('15 Jan 2020'));      // UK short
        $this->assertSame('2020-01-15', tidy_date('Jan 15, 2020'));     // US short
    }

    public function testRemoveComments(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('ABC', remove_comments('A<!-- -->B# # # CITATION_BOT_PLACEHOLDER_COMMENT 33 # # #C'));
    }

    public function test_titles_are_dissimilar_LONG(): void {
        new TestPage(); // Fill page name with test name for debugging
        $big1 = "asdfgtrewxcvbnjy67rreffdsffdsgfbdfni goreinagoidfhgaodusfhaoleghwc89foxyehoif2faewaeifhajeowhf;oaiwehfa;ociboes;";
        $big1 = $big1 . $big1 . $big1 . $big1 . $big1;
        $big2 = $big1 . "X"; // stuff...X
        $big1 = $big1 . "Y"; // stuff...Y
        $big3 = $big1 . $big1; // stuff...Xstuff...X
        $this->assertTrue(titles_are_similar($big1, $big2));
        $this->assertTrue(titles_are_dissimilar($big1, $big3));
    }

    public function test_titles_are_similar_ticks(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('ejscriptgammaramshg', strip_diacritics('Ã‰Å¾Ã‰Å¸Ã‰Â¡Ã‰Â£Ã‰Â¤Ã‰Â¥Ã‰Â '));
        $this->assertTrue(titles_are_similar('Ã‰Å¾Ã‰Å¸Ã‰Â¡Ã‰Â£Ã‰Â¤Ã‰Â¥Ã‰Â ', 'ejscriptgammaramshg'));
    }

    public function test_titles_are_similar_series(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertTrue(titles_are_similar('ABC  (clifton, n j ) ', 'ABC  '));
    }

    public function test_titles_are_similar_junk(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertTrue(titles_are_similar('DSFrHdseyJhgdtyhTSFDhge5safdsfasdfa', 'Ã¯Â¿Â½Ã¯Â¿Â½DÃ¯Â¿Â½Ã¯Â¿Â½SÃ¯Â¿Â½Ã¯Â¿Â½FÃ¯Â¿Â½Ã¯Â¿Â½rÃ¯Â¿Â½Ã¯Â¿Â½HÃ¯Â¿Â½Ã¯Â¿Â½dÃ¯Â¿Â½Ã¯Â¿Â½sÃ¯Â¿Â½Ã¯Â¿Â½eÃ¯Â¿Â½Ã¯Â¿Â½yÃ¯Â¿Â½Ã¯Â¿Â½JÃ¯Â¿Â½Ã¯Â¿Â½hÃ¯Â¿Â½Ã¯Â¿Â½gÃ¯Â¿Â½Ã¯Â¿Â½dÃ¯Â¿Â½Ã¯Â¿Â½tÃ¯Â¿Â½Ã¯Â¿Â½yÃ¯Â¿Â½Ã¯Â¿Â½hÃ¯Â¿Â½Ã¯Â¿Â½TÃ¯Â¿Â½Ã¯Â¿Â½SÃ¯Â¿Â½Ã¯Â¿Â½FÃ¯Â¿Â½Ã¯Â¿Â½DÃ¯Â¿Â½Ã¯Â¿Â½hÃ¯Â¿Â½Ã¯Â¿Â½gÃ¯Â¿Â½Ã¯Â¿Â½eÃ¯Â¿Â½Ã¯Â¿Â½5Ã¯Â¿Â½Ã¯Â¿Â½sÃ¯Â¿Â½Ã¯Â¿Â½aÃ¯Â¿Â½Ã¯Â¿Â½fÃ¯Â¿Â½Ã¯Â¿Â½dÃ¯Â¿Â½Ã¯Â¿Â½sÃ¯Â¿Â½Ã¯Â¿Â½fÃ¯Â¿Â½Ã¯Â¿Â½aÃ¯Â¿Â½Ã¯Â¿Â½sÃ¯Â¿Â½Ã¯Â¿Â½dÃ¯Â¿Â½Ã¯Â¿Â½fÃ¯Â¿Â½Ã¯Â¿Â½aÃ¯Â¿Â½Ã¯Â¿Â½'));
    }

    public function test_titles_are_similar_junk2(): void {
        $x = 'Eulerian Numbers';
        $this->assertFalse(str_i_same($x, $x));
    }

    public function test_chapters_are_simple(): void {
        $this->assertSame('zbcder', titles_simple('Chapter 3 - Zbcder'));
    }

    public function testArrowAreQuotes1(): void {
        $text = "This Ã‚Â» That";
        $this->assertSame($text, straighten_quotes($text, true));
    }

    public function testArrowAreQuotes2(): void {
        $text = "XÃ‚Â«YÃ‚Â»Z";
        $this->assertSame('X"Y"Z', straighten_quotes($text, true));
    }

    public function testArrowAreQuotes3(): void {
        $text = "This Ã¢â‚¬Âº That";
        $this->assertSame($text, straighten_quotes($text, true));
    }

    public function testArrowAreQuotes4(): void {
        $text = "XÃ¢â‚¬Â¹YÃ¢â‚¬ÂºZ";
        $this->assertSame("X'Y'Z", straighten_quotes($text, true));
    }

    public function testArrowAreQuotes5(): void {
        $text = "This Ã‚Â» That";
        $this->assertSame($text, straighten_quotes($text, false));
    }

    public function testArrowAreQuotes6(): void {
        $text = "XÃ‚Â«YÃ‚Â»Z";
        $this->assertSame($text, straighten_quotes($text, false));
    }

    public function testArrowAreQuotes7(): void {
        $text = "This Ã¢â‚¬Âº That";
        $this->assertSame($text, straighten_quotes($text, false));
    }

    public function testArrowAreQuotes8(): void {
        $text = "XÃ¢â‚¬Â¹YÃ¢â‚¬ÂºZ";
        $this->assertSame("X'Y'Z", straighten_quotes($text, false));
    }

    public function testArrowAreQuotes9(): void {
        $text = "Ã‚Â«XYÃ‚Â»Z";
        $this->assertSame($text, straighten_quotes($text, false));
    }

    public function testArrowAreQuotes10(): void {
        $text = "Ã‚Â«XYÃ‚Â»Z";
        $this->assertSame('"XY"Z', straighten_quotes($text, true));
    }

    public function testArrowAreQuotes11(): void {
        $text = "Ã‚Â«YÃ‚Â»";
        $this->assertSame('"Y"', straighten_quotes($text, true));
    }

    public function testArrowAreQuotes12(): void {
        $text = "Ã¢â‚¬Â¹YÃ¢â‚¬Âº";
        $this->assertSame("'Y'", straighten_quotes($text, true));
    }

    public function testArrowAreQuotes13(): void {
        $text = "Ã‚Â«YÃ‚Â»";
        $this->assertSame('"Y"', straighten_quotes($text, false));
    }

    public function testArrowAreQuotes14(): void {
        $text = "Ã¢â‚¬Â¹YÃ¢â‚¬Âº";
        $this->assertSame("'Y'", straighten_quotes($text, false));
    }

    public function testArrowAreQuotes15(): void {
        new TestPage(); // Fill page name with test name for debugging
        $text = 'Ã‚Â«LastronauteÃ‚Â» du vox pop de Guy Nantel ÃƒÂ©tait candidat aux ÃƒÂ©lections fÃƒÂ©dÃƒÂ©rales... et a perdu';
        $this->assertSame($text, straighten_quotes($text, false));
    }

    public function testArrowAreQuotes16(): void {
        $text = 'Ã‚Â«LastronauteÃ‚Â» du vox pop de Guy Nantel ÃƒÂ©tait candidat aux ÃƒÂ©lections fÃƒÂ©dÃƒÂ©rales... et a perdu';
        $this->assertSame('"Lastronaute" du vox pop de Guy Nantel ÃƒÂ©tait candidat aux ÃƒÂ©lections fÃƒÂ©dÃƒÂ©rales... et a perdu', straighten_quotes($text, true));
    }

    public function testC1QuoteNormalization(): void {
        // Raw C1 bytes normalized to ASCII quotes
        $this->assertSame("'smart' and \"test\"", straighten_quotes("\x91smart\x92 and \x93test\x94", true));
        $this->assertSame("'test'", straighten_quotes("\x91test\x92", false));
        $this->assertSame("Text 'with C1' bytes", normalize_c1_quotes("Text \x91with C1\x92 bytes"));
    }

    public function testC1PreservesValidUTF8(): void {
        // Valid UTF-8 multibyte sequences preserved (en-dashes, CJK, accented chars)
        $this->assertSame("HartreeÃ¢â‚¬â€œFock Method", straighten_quotes("HartreeÃ¢â‚¬â€œFock Method", true));
        $this->assertSame("Ã¥Â¤Â§Ã¥Â­Â¦Ã£ÂÂ«Ã£ÂÅ Ã£Ââ€˜Ã£â€šâ€¹Ã§Â â€Ã§Â©Â¶", straighten_quotes("Ã¥Â¤Â§Ã¥Â­Â¦Ã£ÂÂ«Ã£ÂÅ Ã£Ââ€˜Ã£â€šâ€¹Ã§Â â€Ã§Â©Â¶", true));
        $this->assertSame("Ãƒâ€˜Ãƒâ€™Ãƒâ€œÃƒâ€", straighten_quotes("Ãƒâ€˜Ãƒâ€™Ãƒâ€œÃƒâ€", true));
    }

    public function testC1EmptyString(): void {
        $this->assertSame('', straighten_quotes('', true));
        $this->assertSame('', normalize_c1_quotes(''));
    }

    public function testC1UnicodeControlChars(): void {
        // Unicode control characters U+0091-U+0094 normalized
        $this->assertSame("'dynamic-lanes'", normalize_c1_quotes("Ã‚â€˜dynamic-lanesÃ‚â€™"));
        $this->assertSame('"test"', normalize_c1_quotes("Ã‚â€œtestÃ‚â€"));
    }

    /**
     * This MML code comes from a real CrossRef search of DOI 10.1016/j.newast.2009.05.001
     *
     * @todo - should do more than just give up and wrap in nowiki
     */
    public function testMathInTitle1(): void {
        $text_math = 'Spectroscopic analysis of the candidate <math><mrow>ÃƒÅ¸</mrow></math> Cephei star <math><mrow>s</mrow></math> Cas: Atmospheric characterization and line-profile variability';
        $this->assertSame($text_math, sanitize_string($text_math));
    }

    public function testMathInTitle2(): void {
        $text_math = 'Spectroscopic analysis of the candidate <math><mrow>ÃƒÅ¸</mrow></math> Cephei star <math><mrow>s</mrow></math> Cas: Atmospheric characterization and line-profile variability';
        // After MathML conversion, <mrow> tags are stripped, leaving just the content
        // Note: title_capitalization doesn't capitalize normal sentence case text, only ALL CAPS
        $expected = 'Spectroscopic analysis of the candidate <math>ÃƒÅ¸</math> Cephei star <math>s</math> Cas: Atmospheric characterization and line-profile variability';
        $this->assertSame($expected, wikify_external_text($text_math));
    }

    public function testMathInTitle3(): void {
        $text_mml = 'Spectroscopic analysis of the candidate <mml:math altimg="si37.gif" overflow="scroll" xmlns:xocs="http://www.elsevier.com/xml/xocs/dtd" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.elsevier.com/xml/ja/dtd" xmlns:ja="http://www.elsevier.com/xml/ja/dtd" xmlns:mml="http://www.w3.org/1998/Math/MathML" xmlns:tb="http://www.elsevier.com/xml/common/table/dtd" xmlns:sb="http://www.elsevier.com/xml/common/struct-bib/dtd" xmlns:ce="http://www.elsevier.com/xml/common/dtd" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:cals="http://www.elsevier.com/xml/common/cals/dtd"><mml:mrow><mml:mi>ÃƒÅ¸</mml:mi></mml:mrow></mml:math> Cephei star <mml:math altimg="si37.gif" overflow="scroll" xmlns:xocs="http://www.elsevier.com/xml/xocs/dtd" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.elsevier.com/xml/ja/dtd" xmlns:ja="http://www.elsevier.com/xml/ja/dtd" xmlns:mml="http://www.w3.org/1998/Math/MathML" xmlns:tb="http://www.elsevier.com/xml/common/table/dtd" xmlns:sb="http://www.elsevier.com/xml/common/struct-bib/dtd" xmlns:ce="http://www.elsevier.com/xml/common/dtd" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:cals="http://www.elsevier.com/xml/common/cals/dtd"><mml:mrow><mml:mi>s</mml:mi></mml:mrow></mml:math> Cas: Atmospheric characterization and line-profile variability';
        // After MathML conversion, content is properly converted to LaTeX
        // Note: title_capitalization doesn't capitalize normal sentence case text, only ALL CAPS
        $expected = 'Spectroscopic analysis of the candidate <math>ÃƒÅ¸</math> Cephei star <math>s</math> Cas: Atmospheric characterization and line-profile variability';
        $this->assertSame($expected, wikify_external_text($text_mml));
    }

    public function testMathTagSpacesAddedWhenMissing(): void {
        // Regression test: CrossRef sometimes omits the spaces between math markup and surrounding
        // text, e.g. "Using<math>...</math>Decays" instead of "Using <math>...</math> Decays".
        $input = 'Something<math>X</math>follows';
        $result = wikify_external_text($input);
        $this->assertStringContainsString('<math>X</math> follows', $result);
        $this->assertStringNotContainsString('<math>X</math>follows', $result);
        $this->assertStringContainsString('something <math>', mb_strtolower($result));
    }

    public function testMathTagExistingSpacesPreserved(): void {
        // Existing spaces around math tags must not be doubled.
        $input = 'Something <math>X</math> follows';
        $result = wikify_external_text($input);
        $this->assertStringNotContainsString('  <math>', $result);
        $this->assertStringNotContainsString('</math>  ', $result);
    }

    public function testMathTagNoSpaceBeforeDigit(): void {
        // A digit immediately after </math> does not need a separating space.
        // The closing-tag rule only applies to letters, so "math>2" stays unchanged.
        $input = '<math>X</math>2';
        $result = wikify_external_text($input);
        $this->assertStringNotContainsString('</math> 2', $result);
    }

    public function testURLInTitle(): void {
        $text = '[http://dfadfd]';
        $this->assertSame($text, sanitize_string($text));
    }

    public function testTrailingPeriods1(): void {
        $this->assertSame('In the X.Y.', wikify_external_text('In the X.Y.'));
    }

    public function testTrailingPeriods2(): void {
        $this->assertSame('In the X. Y.', wikify_external_text('In the X. Y.'));
    }

    public function testTrailingPeriods3(): void {
        $this->assertSame('In the X. And Y', wikify_external_text('In the X. and Y.'));
    }

    public function testTrailingPeriods4(): void {
        $this->assertSame('A.B.C.', wikify_external_text('A.B.C.'));
    }

    public function testTrailingPeriods5(): void {
        $this->assertSame('Blahy', wikify_external_text('Blahy.'));
    }

    public function testTrailingPeriods6(): void {
        $this->assertSame('Blahy', wikify_external_text('Blahy............'));
    }

    public function testTrailingPeriods7(): void {
        $this->assertSame('Blahy.', wikify_external_text('Blahy....... ....'));
    }

    public function testTrailingPeriods8(): void {
        $this->assertSame('Dfadsfds Hoser......', wikify_external_text('Dfadsfds Hoser..... . .'));
    }

    public function testTrailingNbsp(): void {
        $this->assertSame('Dfadsfds', wikify_external_text('Dfadsfds&nbsp;'));
        $this->assertSame('Dfadsfds', wikify_external_text('Dfadsfds&amp;nbsp;'));
    }

    public function testItal(): void {
        $this->assertSame("''A''", wikify_external_text('<italics>A</italics>'));
    }

    public function testEm(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame("'''A'''", wikify_external_text('<Emphasis Type="Bold">A</Emphasis>'));
    }

    public function testEm2(): void {
        $this->assertSame("''A''", wikify_external_text('<em>A</em>'));
    }

    public function testEmIt(): void {
        $this->assertSame("''A''", wikify_external_text('<Emphasis Type="Italic">A</Emphasis>'));
    }

    public function testDollarMath(): void {
        $this->assertSame("<math>Abs</math>", wikify_external_text('$$Abs$$'));
    }

    public function testBrackets(): void {
        $this->assertSame("ABC", remove_brackets('{}{}{A[][][][][]B()(){}[]][][[][C][][][[()()'));
    }

    public function testStrong(): void {
        $this->assertSame('A new genus and two new species of Apomecynini, a new species of Desmiphorini, and new records in Lamiinae and Disteniidae (Coleoptera)', wikify_external_text('. <strong>A new genus and two new species of Apomecynini, a new species of Desmiphorini, and new records in Lamiinae and Disteniidae (Coleoptera)</strong>.'));
    }

    /** The X prevents first character caps */
    public function testCapitalization_lots_more(): void { // Double check that constants are in order when we sort - paranoid
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('X BJPsych', title_capitalization(title_case('X Bjpsych'), true));
        $this->assertSame('X delle', title_capitalization(title_case('X delle'), true));
        $this->assertSame('X IEEE', title_capitalization(title_case('X Ieee'), true));
        $this->assertSame('X NASA', title_capitalization(title_case('X Nasa'), true));
        $this->assertSame('X over', title_capitalization(title_case('X Over'), true));
        $this->assertSame('X und', title_capitalization(title_case('X Und'), true));
        $this->assertSame('X within', title_capitalization(title_case('X Within'), true));
        $this->assertSame('X AAPS', title_capitalization(title_case('X Aaps'), true));
        $this->assertSame('X BJOG', title_capitalization(title_case('X Bjog'), true));
    }

    public function testCapitalization_lots_more2(): void {
        $this->assertSame('X e-Neuroforum', title_capitalization(title_case('X E-Neuroforum'), true));
        $this->assertSame('X eGEMs', title_capitalization(title_case('X Egems'), true));
        $this->assertSame('X eFood', title_capitalization(title_case('X Efood'), true));
        $this->assertSame('X eJHaem', title_capitalization(title_case('X Ejhaem'), true));
        $this->assertSame('X eNeuro', title_capitalization(title_case('X Eneuro'), true));
        $this->assertSame('X eVolo', title_capitalization(title_case('X EVolo'), true));
        $this->assertSame('X HannahArendt.net', title_capitalization(title_case('X hannaharendt.net'), true));
        $this->assertSame('X iJournal', title_capitalization(title_case('X IJournal'), true));
        $this->assertSame('X JABS : Journal of Applied Biological Sciences', title_capitalization(title_case('X Jabs : Journal of Applied Biological Sciences'), true));
    }

    public function testCapitalization_lots_more3(): void {
        $this->assertSame('X La Trobe', title_capitalization(title_case('X La Trobe'), true));
        $this->assertSame('X MERIP', title_capitalization(title_case('X Merip'), true));
        $this->assertSame('X mSystems', title_capitalization(title_case('X MSystems'), true));
        $this->assertSame('X PhytoKeys', title_capitalization(title_case('X Phytokeys'), true));
        $this->assertSame('X PNAS', title_capitalization(title_case('X Pnas'), true));
    }

    public function testCapitalization_lots_more4(): void {
        $this->assertSame('X Srp Arh Celok Lek', title_capitalization(title_case('X SRP Arh Celok Lek'), true));
        $this->assertSame('X Time Out London', title_capitalization(title_case('X Time out London'), true));
        $this->assertSame('X z/Journal', title_capitalization(title_case('X Z/journal'), true));
        $this->assertSame('X ZooKeys', title_capitalization(title_case('X zookeys'), true));
    }

    public function testCapitalization_lots_more5(): void {
        $this->assertSame('Www', title_case('www'));
        $this->assertSame('www.', title_case('www.'));
        $this->assertSame('http://', title_case('http://'));
        $this->assertSame('abx www-x', title_case('abx www-x'));
        $this->assertSame('Hello There', title_case('hello there'));
    }

    public function testCapitalization_lots_more6(): void {
        $this->assertSame('The DOS is Faster', title_capitalization('The DOS is Faster', true));
        $this->assertSame('The dos is Faster', title_capitalization('The dos is Faster', true));
        $this->assertSame('The DoS is Faster', title_capitalization('The DoS is Faster', true));
        $this->assertSame('The dOs is Faster', title_capitalization('The dOs is Faster', true));
        $this->assertSame('The DOS Dos dOs is dos Faster', title_capitalization('The DOS Dos dOs is dos Faster', true));
        $this->assertSame('The DOS Dos dOs is dos Faster', title_capitalization('The DOS Dos dOs is dos Faster', false));
        $this->assertSame('DOS', title_capitalization('DOS', true));
        $this->assertSame('dos', title_capitalization('dos', true));
        $this->assertSame('DoS', title_capitalization('DoS', true));
        $this->assertSame('dOs', title_capitalization('dOs', true));
    }

    public function testCapitalization_lots_more7(): void {
        $this->assertSame('AIDS', title_capitalization('Aids', true));
        $this->assertSame('BioScience', title_capitalization('Bioscience', true));
        $this->assertSame('BioMedical Engineering OnLine', title_capitalization('Biomedical Engineering Online', true));
    }

    public function testRestorItalicsRegex1(): void {
        $text = "{{cite journal|doi=10.7717/peerj.7240 }}";
        $template = $this->process_citation($text);
        $this->assertSame("''Ngwevu intloko'': A new early sauropodomorph dinosaur from the Lower Jurassic Elliot Formation of South Africa and comments on cranial ontogeny in ''Massospondylus carinatus''", $template->get2('title'));
    }

    public function testRestorItalicsRegex2(): void {
        $text = "{{cite journal|doi=10.7717/peerj.4224 }}";
        $template = $this->process_citation($text);
        $this->assertSame("A revised cranial description of ''Massospondylus carinatus'' Owen (Dinosauria: Sauropodomorpha) based on computed tomographic scans and a review of cranial characters for basal Sauropodomorpha", $template->get2('title'));
    }

    public function testVariousEncodes2(): void {
        $test = "Ã£â€šÂ·Ã£Æ’Â§Ã£Æ’Æ’Ã£Æ’â€Ã£Æ’Â³Ã£â€šÂ°";
        $decoded = smart_decode($test, 'UTF-8', '');
        $this->assertSame($test, $decoded);
    }

    public function testVariousEncodes3(): void {
        $test = "Ã£â€šÂ·Ã£Æ’Â§Ã£Æ’Æ’Ã£Æ’â€Ã£Æ’Â³Ã£â€šÂ°";
        $decoded = smart_decode($test, "iso-8859-11", '');
        $this->assertSame('Ã Â¹Æ’Ã‚â€šÃ Â¸â€”Ã Â¹Æ’Ã‚Æ’Ã Â¸â€¡Ã Â¹Æ’Ã‚Æ’Ã‚Æ’Ã Â¹Æ’Ã‚Æ’Ã‚â€Ã Â¹Æ’Ã‚Æ’Ã Â¸â€œÃ Â¹Æ’Ã‚â€šÃ Â¸Â', $decoded); // Clearly random junk
    }

    public function testVariousEncodes1(): void {
        $input = "\xe3\x82\xb7\xe3\x83\xa7\xe3\x83\x83\xe3\x83\x94\xe3\x83\xb3\xe3\x82\xb0";
        $sample = 'Ã£â€šÂ·Ã£Æ’Â§Ã£Æ’Æ’Ã£Æ’â€Ã£Æ’Â³Ã£â€šÂ°';
        $decoded = convert_to_utf8($input);
        $this->assertSame($sample, $decoded);
    }

    public function testVariousEncodes4(): void {
        $sample = "2xSP!#$%&'()*+,-./3x0123456789:;<=>?4x@ABCDEFGHIJKLMNO5xPQRSTUVWXYZ[\]^_6x`abcdefghijklmno7xpqrstuvwxyz{|}~8x9xAxNBSPÃ‚Â¡Ã‚Â¢Ã‚Â£Ã‚Â¤Ã‚Â¥Ã‚Â¦Ã‚Â§Ã‚Â¨Ã‚Â©Ã‚ÂªÃ‚Â«Ã‚Â¬SHYÃ‚Â®Ã‚Â¯BxÃ‚Â°Ã‚Â±Ã‚Â²Ã‚Â³Ã‚Â´Ã‚ÂµÃ‚Â¶Ã‚Â·Ã‚Â¸Ã‚Â¹Ã‚ÂºÃ‚Â»Ã‚Â¼Ã‚Â½Ã‚Â¾Ã‚Â¿CxÃƒâ‚¬ÃƒÂÃƒâ€šÃƒÆ’Ãƒâ€žÃƒâ€¦Ãƒâ€ Ãƒâ€¡ÃƒË†Ãƒâ€°ÃƒÅ Ãƒâ€¹ÃƒÅ’ÃƒÂÃƒÅ½ÃƒÂDxÃƒÂÃƒâ€˜Ãƒâ€™Ãƒâ€œÃƒâ€Ãƒâ€¢Ãƒâ€“Ãƒâ€”ÃƒËœÃƒâ„¢ÃƒÅ¡Ãƒâ€ºÃƒÅ“ÃƒÂÃƒÅ¾ÃƒÅ¸ExÃƒÂ ÃƒÂ¡ÃƒÂ¢ÃƒÂ£ÃƒÂ¤ÃƒÂ¥ÃƒÂ¦ÃƒÂ§ÃƒÂ¨ÃƒÂ©ÃƒÂªÃƒÂ«ÃƒÂ¬ÃƒÂ­ÃƒÂ®ÃƒÂ¯FxÃƒÂ°ÃƒÂ±ÃƒÂ²ÃƒÂ³ÃƒÂ´ÃƒÂµÃƒÂ¶ÃƒÂ·ÃƒÂ¸ÃƒÂ¹ÃƒÂºÃƒÂ»ÃƒÂ¼ÃƒÂ½ÃƒÂ¾ÃƒÂ¿";
        $urlencoded_iso_8859_1 = '2xSP%21%23%24%25%26%27%28%29%2A%2B%2C-.%2F3x0123456789%3A%3B%3C%3D%3E%3F4x%40ABCDEFGHIJKLMNO5xPQRSTUVWXYZ%5B%5C%5D%5E_6x%60abcdefghijklmno7xpqrstuvwxyz%7B%7C%7D%7E8x9xAxNBSP%A1%A2%A3%A4%A5%A6%A7%A8%A9%AA%AB%ACSHY%AE%AFBx%B0%B1%B2%B3%B4%B5%B6%B7%B8%B9%BA%BB%BC%BD%BE%BFCx%C0%C1%C2%C3%C4%C5%C6%C7%C8%C9%CA%CB%CC%CD%CE%CFDx%D0%D1%D2%D3%D4%D5%D6%D7%D8%D9%DA%DB%DC%DD%DE%DFEx%E0%E1%E2%E3%E4%E5%E6%E7%E8%E9%EA%EB%EC%ED%EE%EFFx%F0%F1%F2%F3%F4%F5%F6%F7%F8%F9%FA%FB%FC%FD%FE%FF';
        $decoded = mb_convert_encoding(urldecode($urlencoded_iso_8859_1), "UTF-8", "iso-8859-1");
        $this->assertSame($sample, $decoded);
    }

    public function testVariousEncodes5(): void {
        $test = "2xSP!#$%&'()*+,-./3x0123456789:;<=>?4x@ABCDEFGHIJKLMNO5xPQRSTUVWXYZ[\]^_6x`abcdefghijklmno7xpqrstuvwxyz{|}~8x9xAxNBSPÃ‚Â¡Ã‚Â¢Ã‚Â£Ã¢â€šÂ¬20ACÃ‚Â¥Ã…Â 0160Ã‚Â§Ã…Â¡0161Ã‚Â©Ã‚ÂªÃ‚Â«Ã‚Â¬SHYÃ‚Â®Ã‚Â¯BxÃ‚Â°Ã‚Â±Ã‚Â²Ã‚Â³Ã…Â½017DÃ‚ÂµÃ‚Â¶Ã‚Â·Ã…Â¾017EÃ‚Â¹Ã‚ÂºÃ‚Â»Ã…â€™0152Ã…â€œ0153Ã…Â¸0178Ã‚Â¿CxÃƒâ‚¬ÃƒÂÃƒâ€šÃƒÆ’Ãƒâ€žÃƒâ€¦Ãƒâ€ Ãƒâ€¡ÃƒË†Ãƒâ€°ÃƒÅ Ãƒâ€¹ÃƒÅ’ÃƒÂÃƒÅ½ÃƒÂDxÃƒÂÃƒâ€˜Ãƒâ€™Ãƒâ€œÃƒâ€Ãƒâ€¢Ãƒâ€“Ãƒâ€”ÃƒËœÃƒâ„¢ÃƒÅ¡Ãƒâ€ºÃƒÅ“ÃƒÂÃƒÅ¾ÃƒÅ¸ExÃƒÂ ÃƒÂ¡ÃƒÂ¢ÃƒÂ£ÃƒÂ¤ÃƒÂ¥ÃƒÂ¦ÃƒÂ§ÃƒÂ¨ÃƒÂ©ÃƒÂªÃƒÂ«ÃƒÂ¬ÃƒÂ­ÃƒÂ®ÃƒÂ¯FxÃƒÂ°ÃƒÂ±ÃƒÂ²ÃƒÂ³ÃƒÂ´ÃƒÂµÃƒÂ¶ÃƒÂ·ÃƒÂ¸ÃƒÂ¹ÃƒÂºÃƒÂ»ÃƒÂ¼ÃƒÂ½ÃƒÂ¾ÃƒÂ¿";
        $string_utf8_urlencoded = "2xSP%21%23%24%25%26%27%28%29%2A%2B%2C-.%2F3x0123456789%3A%3B%3C%3D%3E%3F4x%40ABCDEFGHIJKLMNO5xPQRSTUVWXYZ%5B%5C%5D%5E_6x%60abcdefghijklmno7xpqrstuvwxyz%7B%7C%7D%7E8x9xAxNBSP%C2%A1%C2%A2%C2%A3%E2%82%AC20AC%C2%A5%C5%A00160%C2%A7%C5%A10161%C2%A9%C2%AA%C2%AB%C2%ACSHY%C2%AE%C2%AFBx%C2%B0%C2%B1%C2%B2%C2%B3%C5%BD017D%C2%B5%C2%B6%C2%B7%C5%BE017E%C2%B9%C2%BA%C2%BB%C5%920152%C5%930153%C5%B80178%C2%BFCx%C3%80%C3%81%C3%82%C3%83%C3%84%C3%85%C3%86%C3%87%C3%88%C3%89%C3%8A%C3%8B%C3%8C%C3%8D%C3%8E%C3%8FDx%C3%90%C3%91%C3%92%C3%93%C3%94%C3%95%C3%96%C3%97%C3%98%C3%99%C3%9A%C3%9B%C3%9C%C3%9D%C3%9E%C3%9FEx%C3%A0%C3%A1%C3%A2%C3%A3%C3%A4%C3%A5%C3%A6%C3%A7%C3%A8%C3%A9%C3%AA%C3%AB%C3%AC%C3%AD%C3%AE%C3%AFFx%C3%B0%C3%B1%C3%B2%C3%B3%C3%B4%C3%B5%C3%B6%C3%B7%C3%B8%C3%B9%C3%BA%C3%BB%C3%BC%C3%BD%C3%BE%C3%BF";
        $string_utf8 = urldecode($string_utf8_urlencoded);
        $string_windows1252_urlencoded = "2xSP%21%23%24%25%26%27%28%29%2A%2B%2C-.%2F3x0123456789%3A%3B%3C%3D%3E%3F4x%40ABCDEFGHIJKLMNO5xPQRSTUVWXYZ%5B%5C%5D%5E_6x%60abcdefghijklmno7xpqrstuvwxyz%7B%7C%7D%7E8x9xAxNBSP%A1%A2%A3%8020AC%A5%8A0160%A7%9A0161%A9%AA%AB%ACSHY%AE%AFBx%B0%B1%B2%B3%8E017D%B5%B6%B7%9E017E%B9%BA%BB%8C0152%9C0153%9F0178%BFCx%C0%C1%C2%C3%C4%C5%C6%C7%C8%C9%CA%CB%CC%CD%CE%CFDx%D0%D1%D2%D3%D4%D5%D6%D7%D8%D9%DA%DB%DC%DD%DE%DFEx%E0%E1%E2%E3%E4%E5%E6%E7%E8%E9%EA%EB%EC%ED%EE%EFFx%F0%F1%F2%F3%F4%F5%F6%F7%F8%F9%FA%FB%FC%FD%FE%FF";
        $string_windows1252 = urldecode($string_windows1252_urlencoded);
        $string_windows1252_converted_to_utf8 = mb_convert_encoding($string_windows1252, "UTF-8", "WINDOWS-1252");
        $string_utf8_coverted_to_windows1252 = mb_convert_encoding($string_utf8, "WINDOWS-1252", "UTF-8");

        $this->assertSame($test, $string_utf8);
        $this->assertSame($test, $string_windows1252_converted_to_utf8);
        $this->assertSame($string_utf8_coverted_to_windows1252, $string_windows1252);
    }

    public function testVariousEncodes6(): void {
        $test = "Ã£â€šÂ¢ Ã£â€šÂ¤ Ã£â€šÂ¦ Ã£â€šÂ¨ Ã£â€šÂª Ã£â€šÂ« Ã£â€šÂ­ Ã£â€šÂ¯ Ã£â€šÂ± Ã£â€šÂ³ Ã£â€šÂ¬ Ã£â€šÂ® Ã£â€šÂ° Ã£â€šÂ² Ã£â€šÂ´ Ã£â€šÂµ Ã£â€šÂ· Ã£â€šÂ¹ Ã£â€šÂ» Ã£â€šÂ½ Ã£â€šÂ¶ Ã£â€šÂ¸ Ã£â€šÂº Ã£â€šÂ¼ Ã£â€šÂ¾ Ã£â€šÂ¿ Ã£Æ’Â Ã£Æ’â€ž Ã£Æ’â€  Ã£Æ’Ë† Ã£Æ’â‚¬ Ã£Æ’â€š Ã£Æ’â€¦ Ã£Æ’â€¡ Ã£Æ’â€° Ã£Æ’Å  Ã£Æ’â€¹ Ã£Æ’Å’ Ã£Æ’Â Ã£Æ’Å½ Ã£Æ’Â Ã£Æ’â€™ Ã£Æ’â€¢ Ã£Æ’Ëœ Ã£Æ’â€º Ã£Æ’Â Ã£Æ’â€œ Ã£Æ’â€“ Ã£Æ’â„¢ Ã£Æ’Å“ Ã£Æ’â€˜ Ã£Æ’â€ Ã£Æ’â€” Ã£Æ’Å¡ Ã£Æ’Â Ã£Æ’Å¾ Ã£Æ’Å¸ Ã£Æ’Â  Ã£Æ’Â¡ Ã£Æ’Â¢ Ã£Æ’Â¤ Ã£Æ’Â¦ Ã£Æ’Â¨ Ã£Æ’Â© Ã£Æ’Âª Ã£Æ’Â« Ã£Æ’Â¬ Ã£Æ’Â­ Ã£Æ’Â¯ Ã£Æ’Â° Ã£Æ’Â± Ã£Æ’Â²";
        $this->assertSame($test, convert_to_utf8(mb_convert_encoding($test, "ISO-2022-JP", "UTF-8")));
    }

    public function testVariousEncodes7(): void {
        $test = "Ã¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã§Â®â‚¬Ã§Â§Â°Ã¨Â¯Â´Ã¦â€“â€¡Ã¦ËœÂ¯Ã§â€Â±Ã¤Â¸Å“Ã¦Â±â€°Ã§Â»ÂÃ¥Â­Â¦Ã¥Â®Â¶Ã¦â€“â€¡Ã¥Â­â€”Ã¥Â­Â¦Ã¥Â®Â¶Ã¨Â®Â¸Ã¦â€¦Å½Ã§Â¼â€“Ã¨â€˜â€”Ã§Å¡â€žÃ¨Â¯Â­Ã¦â€“â€¡Ã¥Â·Â¥Ã¥â€¦Â·Ã¤Â¹Â¦Ã¨â€˜â€”Ã¤Â½Å“Ã¦ËœÂ¯Ã¤Â¸Â­Ã¥â€ºÂ½Ã¦Å“â‚¬Ã¦â€”Â©Ã§Å¡â€žÃ§Â³Â»Ã§Â»Å¸Ã¥Ë†â€ Ã¦Å¾ÂÃ¦Â±â€°Ã¥Â­â€”Ã¥Â­â€”Ã¥Â½Â¢Ã¥â€™Å’Ã¨â‚¬Æ’Ã§Â©Â¶Ã¥Â­â€”Ã¦ÂºÂÃ§Å¡â€žÃ¨Â¯Â­Ã¦â€“â€¡Ã¨Â¾Å¾Ã¤Â¹Â¦Ã¤Â¹Å¸Ã¦ËœÂ¯Ã¤Â¸â€“Ã§â€¢Å’Ã¤Â¸Å Ã¦Å“â‚¬Ã¦â€”Â©Ã§Å¡â€žÃ¥Â­â€”Ã¥â€¦Â¸Ã¤Â¹â€¹Ã¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã¥â€ â€¦Ã¥Â®Â¹Ã¥â€¦Â±Ã¥ÂÂÃ¤Âºâ€Ã¥ÂÂ·Ã¥â€¦Â¶Ã¤Â¸Â­Ã¥â€°ÂÃ¥ÂÂÃ¥â€ºâ€ºÃ¥ÂÂ·Ã¤Â¸ÂºÃ¦â€“â€¡Ã¥Â­â€”Ã¨Â§Â£Ã¨Â¯Â´Ã¥Â­â€”Ã¥Â¤Â´Ã¤Â»Â¥Ã¥Â°ÂÃ§Â¯â€ Ã¤Â¹Â¦Ã¥â€ â„¢Ã¦Â­Â¤Ã¤Â¹Â¦Ã§Â¼â€“Ã¨â€˜â€”Ã¦â€”Â¶Ã©Â¦â€“Ã¦Â¬Â¡Ã¥Â¯Â¹Ã¢â‚¬Å“Ã¥â€¦Â­Ã¤Â¹Â¦Ã¥ÂÅ¡Ã¥â€¡ÂºÃ¤Âºâ€ Ã¥â€¦Â·Ã¤Â½â€œÃ§Å¡â€žÃ¨Â§Â£Ã©â€¡Å Ã©â‚¬ÂÃ¥Â­â€”Ã¨Â§Â£Ã©â€¡Å Ã¥Â­â€”Ã¤Â½â€œÃ¦ÂÂ¥Ã¦ÂºÂÃ§Â¬Â¬Ã¥ÂÂÃ¤Âºâ€Ã¥ÂÂ·Ã¤Â¸ÂºÃ¥Ââ„¢Ã§â€ºÂ®Ã¨Â®Â°Ã¥Â½â€¢Ã¦Â±â€°Ã¥Â­â€”Ã§Å¡â€žÃ¤ÂºÂ§Ã§â€Å¸Ã¥Ââ€˜Ã¥Â±â€¢Ã¥Å Å¸Ã§â€Â¨Ã§Â»â€œÃ¦Å¾â€žÃ§Â­â€°Ã¦â€“Â¹Ã©ÂÂ¢Ã§Å¡â€žÃ©â€”Â®Ã©Â¢ËœÃ¤Â»Â¥Ã¥ÂÅ Ã¤Â½Å“Ã¨â‚¬â€¦Ã¥Ë†â€ºÃ¤Â½Å“Ã§Å¡â€žÃ§â€ºÂ®Ã§Å¡â€žÃ¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã¦ËœÂ¯Ã¦Å“â‚¬Ã¦â€”Â©Ã§Å¡â€žÃ¦Å’â€°Ã©Æ’Â¨Ã©Â¦â€“Ã§Â¼â€“Ã¦Å½â€™Ã§Å¡â€žÃ¦Â±â€°Ã¨Â¯Â­Ã¥Â­â€”Ã¥â€¦Â¸Ã¥â€¦Â¨Ã¤Â¹Â¦Ã¥â€¦Â±Ã¥Ë†â€ Ã¤Â¸ÂªÃ©Æ’Â¨Ã©Â¦â€“Ã¦â€Â¶Ã¥Â­â€”9353Ã¥ÂÂ¦Ã¦Å“â€°Ã¢â‚¬Å“Ã©â€¡ÂÃ¦â€“â€¡Ã¥ÂÂ³Ã¥Â¼â€šÃ¤Â½â€œÃ¥Â­â€”Ã¤Â¸ÂªÃ¥â€¦Â±10516Ã¥Â­â€”Ã¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã¥Å½Å¸Ã¤Â¹Â¦Ã¤Â½Å“Ã¤ÂºÅ½Ã¦Â±â€°Ã¥â€™Å’Ã¥Â¸ÂÃ¦Â°Â¸Ã¥â€¦Æ’Ã¥ÂÂÃ¤ÂºÅ’Ã¥Â¹Â´100Ã¥Ë†Â°Ã¥Â®â€°Ã¥Â¸ÂÃ¥Â»ÂºÃ¥â€¦â€°Ã¥â€¦Æ’Ã¥Â¹Â´Ã¯Â¼Ë†121Ã¥Â¹Â´Ã¯Â¼â€°Ã¥Â®â€¹Ã¥Â¤ÂªÃ¥Â®â€”Ã©â€ºÂÃ§â€ â„¢Ã¤Â¸â€°Ã¥Â¹Â´Ã¥Â¹Â´Ã¥Â®â€¹Ã¥Â¤ÂªÃ¥Â®â€”Ã¥â€˜Â½Ã¥Â¾ÂÃ©â€œâ€°Ã¥ÂÂ¥Ã¤Â¸Â­Ã¦Â­Â£Ã¨â€˜â€ºÃ¦Â¹ÂÃ§Å½â€¹Ã¦Æ’Å¸Ã¦ÂÂ­Ã§Â­â€°Ã¥ÂÅ’Ã¦Â Â¡Ã¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã¥Ë†â€ Ã¦Ë†ÂÃ¤Â¸Å Ã¤Â¸â€¹Ã¥â€¦Â±Ã¤Â¸â€°Ã¥ÂÂÃ¥ÂÂ·Ã¥Â¥â€°Ã¦â€¢â€¢Ã©â€ºâ€¢Ã§â€°Ë†Ã¦ÂµÂÃ¥Â¸Æ’Ã¥ÂÅ½Ã¤Â»Â£Ã§Â â€Ã§Â©Â¶Ã¨Â¯Â´Ã¦â€“â€¡Ã¥Â¤Å¡Ã¤Â»Â¥Ã¦Â­Â¤Ã§â€°Ë†Ã¤Â¸ÂºÃ¨â€œÂÃ¦Å“Â¬Ã¥Â¦â€šÃ¦Â¸â€¦Ã¤Â»Â£Ã¦Â®ÂµÃ§Å½â€°Ã¨Â£ÂÃ¦Â³Â¨Ã©â€¡Å Ã¦Å“Â¬Ã¥ÂÂ³Ã§â€Â¨Ã¦Â­Â¤Ã§â€°Ë†Ã¨Â¯Â´Ã¦â€“â€¡Ã¤Â¸ÂºÃ¥Âºâ€¢Ã§Â¨Â¿Ã¨â‚¬Å’Ã¥Å Â Ã¤Â»Â¥Ã¦Â³Â¨Ã©â€¡Å [1]Ã¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã¦ËœÂ¯Ã§Â§â€˜Ã¥Â­Â¦Ã¦â€“â€¡Ã¥Â­â€”Ã¥Â­Â¦Ã¥â€™Å’Ã¦â€“â€¡Ã§Å’Â®Ã¨Â¯Â­Ã¨Â¨â‚¬Ã¥Â­Â¦Ã§Å¡â€žÃ¥Â¥Â Ã¥Å¸ÂºÃ¤Â¹â€¹Ã¤Â½Å“Ã¥Å“Â¨Ã¤Â¸Â­Ã¥â€ºÂ½Ã¨Â¯Â­Ã¨Â¨â‚¬Ã¥Â­Â¦Ã¥ÂÂ²Ã¤Â¸Å Ã¦Å“â€°Ã©â€¡ÂÃ¨Â¦ÂÃ§Å¡â€žÃ¥Å“Â°Ã¤Â½ÂÃ¥Å½â€ Ã¤Â»Â£Ã¥Â¯Â¹Ã¤ÂºÅ½Ã¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã©Æ’Â½Ã¦Å“â€°Ã¨Â®Â¸Ã¥Â¤Å¡Ã¥Â­Â¦Ã¨â‚¬â€¦Ã§Â â€Ã§Â©Â¶Ã¦Â¸â€¦Ã¦Å“ÂÃ¦â€”Â¶Ã§Â â€Ã§Â©Â¶Ã¦Å“â‚¬Ã¤Â¸ÂºÃ¥â€¦Â´Ã§â€ºâ€ºÃ¦Â®ÂµÃ§Å½â€°Ã¨Â£ÂÃ§Å¡â€žÃ¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã¦Â³Â¨Ã¦Å“Â±Ã©ÂªÂÃ¥Â£Â°Ã§Å¡â€žÃ¨Â¯Â´Ã¦â€“â€¡Ã©â‚¬Å¡Ã¨Â®Â­Ã¥Â®Å¡Ã¥Â£Â°Ã¦Â¡â€šÃ©Â¦Â¥Ã§Å¡â€žÃ¨Â¯Â´Ã¦â€“â€¡Ã¨Â§Â£Ã¥Â­â€”Ã¤Â¹â€°Ã¨Â¯ÂÃ§Å½â€¹Ã§Â­Â Ã§Å¡â€žÃ¨Â¯Â´Ã¦â€“â€¡Ã©â€¡Å Ã¤Â¾â€¹Ã¨Â¯Â´Ã¦â€“â€¡Ã¥ÂÂ¥Ã¨Â¯Â»Ã¥Â°Â¤Ã¥Â¤â€¡Ã¦Å½Â¨Ã¥Â´â€¡Ã¥â€ºâ€ºÃ¤ÂºÂºÃ¤Â¹Å¸Ã¨Å½Â·Ã¥Â°Å Ã§Â§Â°Ã¤Â¸ÂºÃ¨Â¯Â´Ã¦â€“â€¡Ã¥â€ºâ€ºÃ¥Â¤Â§Ã¥Â®Â¶";
        $this->assertSame($test, convert_to_utf8(mb_convert_encoding($test, "EUC-CN", "UTF-8")));
    }

    public function testVariousEncodes8(): void {
        $test = "Ã«â€¹Â¹Ã¬â€¹Â  Ã¬ÂÂ´Ã«Â¦â€žÃ¬ÂÂ´ Ã«Â¬Â´Ã¬â€”â€¡Ã¬Å¾â€¦Ã«â€¹Ë†ÃªÂ¹Å’ Ã¬ÂÂ´Ã«Â¦â€žÃ¬ÂÂ´ Ã­â€šÂ¤Ã¬â€“â‚¬Ã¬ÂÂ¸ Ã¬â€“Â´Ã«Â¦Â° Ã¬â€ Å’Ã«â€¦â€žÃ¬Ââ€ž Ã«Â§Å’Ã«â€šËœÃ«Â³Â´Ã¬â€žÂ¸Ã¬Å¡â€. ÃªÂ·Â¸Ã«Å¸Â¬Ã«â€šËœ ÃªÂ·Â¸Ã«Å â€ Ã«â€¹Â¤Ã«Â¥Â¸ Ã«Â§Å½Ã¬Ââ‚¬ Ã¬ÂÂ´Ã«Â¦â€žÃ«Ââ€ž ÃªÂ°â‚¬Ã¬Â§â‚¬ÃªÂ³Â  Ã¬Å¾Ë†Ã¬Å ÂµÃ«â€¹Ë†Ã«â€¹Â¤. Ã«â€¹Â¹Ã¬â€¹Â Ã¬Ââ‚¬ Ã¬â€“Â¼Ã«Â§Ë†Ã«â€šËœ Ã«Â§Å½Ã¬Ââ‚¬ Ã¬ÂÂ´Ã«Â¦â€žÃ¬Ââ€ž ÃªÂ°â‚¬Ã¬Â§â‚¬ÃªÂ³Â  Ã¬Å¾Ë†Ã¬Å ÂµÃ«â€¹Ë†ÃªÂ¹Å’?";
        $this->assertSame($test, convert_to_utf8(mb_convert_encoding($test, "EUC-KR", "UTF-8")));
    }

    public function testVariousEncodes9(): void {
        $this->assertSame('', smart_decode('test', 'utf-8-sig', 'http'));
        $this->assertSame('', smart_decode('test', 'x-user-defined', 'http'));
    }

    public function testRomanNumbers(): void {
        $this->assertSame('MMCCCXXXI', numberToRomanRepresentation(2331));
    }

    public function testRestoreItalics1(): void {
        $this->assertSame('Buitreraptor gonzalezorum', restore_italics('Buitreraptor gonzalezorum'));
    }

    public function testRestoreItalics2(): void {
        $this->assertSame("Ca ''Buitreraptor gonzalezorum'' X", restore_italics('CaBuitreraptor gonzalezorumX'));
    }

    public function testRestoreItalics3(): void {
        $this->assertSame('Buitreraptor gonzalezorumXXXX', restore_italics('Buitreraptor gonzalezorumXXXX'));
    }

    public function testRestoreItalics4(): void {
        $this->assertSame("To a ''Tyrannotitan chubutensis''-", restore_italics("To aTyrannotitan chubutensis-"));
    }

    public function testCleanDates1(): void {
        new TestPage(); // Fill page name with test name for debugging
        $this->assertSame('', clean_dates(''));
    }

    public function testCleanDates2(): void {
        $this->assertSame('AprilÃ¢â‚¬â€œMay 1995', clean_dates('April-May 1995'));
    }

    public function testCleanDates3(): void {
        $this->assertSame('December 7, 2023', clean_dates('December 7 2023'));
    }

    public function testCleanDates4(): void {
        $this->assertSame('8 December 2022', clean_dates('8 December 2022.'));
    }

    public function testCleanDates5(): void {
        $this->assertSame('8 December 2022', clean_dates('08 December 2022'));
    }

    public function testCleanDates6(): void {
        $this->assertSame('November 2, 1981', clean_dates('Monday, November 2, 1981'));
    }

    public function testOur_mb_substr_replace(): void {
        $in = "Ã£â€šÂ·Ã£Æ’Â§Ã£Æ’Æ’Ã£Æ’â€Ã£Æ’Â³Ã£â€šÂ°";
        $out = "Ã£â€šÂ·Ã£Æ’Â§Ã£Æ’Æ’XÃ£Æ’Â³Ã£â€šÂ°";
        $this->assertSame($out, mb_substr_replace($in, 'X', 3, 1));
    }

    public function testTitles10(): void {
        $junk = "(ab)(cd) (ef)";
        $this->assertSame('(ab)(cd) (Ef)', title_capitalization($junk, true));
    }

    public function testTitles11(): void {
        $junk = "ac's";
        $this->assertSame("Ac's", title_capitalization($junk, true));
    }

    public function testTitles12(): void {
        $junk = "This Des Doggy Des";
        $this->assertSame("This des Doggy Des", title_capitalization($junk, true));
    }

    public function testTitles13(): void {
        $junk = "Now and Then";
        $this->assertSame("Now and Then", title_capitalization($junk, true));
    }

    public function testTitleRoman1(): void {
        $junk = 'A Part xvi: Dogs';
        $this->assertSame('A Part XVI: Dogs', title_capitalization($junk, true));
    }

    public function testTitleRoman2(): void {
        $junk = 'A Part xvi Dogs';
        $this->assertSame('A Part XVI Dogs', title_capitalization($junk, true));
    }

    public function testTitleRoman3(): void {
        $junk = 'Dogs Vii';
        $this->assertSame('Dogs VII', title_capitalization($junk, true));
    }

    public function testTitleRoman4(): void {
        $junk = 'Vii: Dogs';
        $this->assertSame('VII: Dogs', title_capitalization($junk, true));
    }

    public function testTitleProc(): void {
        $junk = 'This is Proceedings a Dog';
        $this->assertSame('This is Proceedings A Dog', title_capitalization($junk, true));
    }

    public function testTitleVar(): void {
        $junk = 'This is var. abc';
        $this->assertSame('This is var. abc', title_capitalization($junk, true));
    }

    public function testTitlePPM(): void {
        $junk = 'This PPM Code';
        $this->assertSame('This ppm Code', title_capitalization($junk, true));
    }

    public function testStringEquSer(): void {
        $s1 = 'advances in anatomy embryology and cell biology';
        $s2 = 'adv anat embryol cell biol';
        $this->assertTrue(str_equivalent($s1, $s2));
    }

    public function testRubbishISBN(): void {
        $junk = "12342";
        $this->assertSame($junk, addISBNdashes($junk));
    }

    public function testCleanDatesXtra1(): void {
        $text = '{{cite journal|date=FebruARY 2000}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('February 2000', $prepared->get2('date'));
    }

    public function testCleanDatesXtra2(): void {
        $text = '{{cite journal|date=1800-2000}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('1800Ã¢â‚¬â€œ2000', $prepared->get2('date'));
    }

    public function testCleanDatesXtra3(): void {
        $text = '{{cite journal|date=January-FEBRUARY 2001}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('JanuaryÃ¢â‚¬â€œFebruary 2001', $prepared->get2('date'));
    }

    public function testCleanDatesXtra4(): void {
        $text = '{{cite journal|date=January 1999-February 2000}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('January 1999 Ã¢â‚¬â€œ February 2000', $prepared->get2('date'));
    }

    public function testCleanDatesXtra5(): void {
        $text = '{{cite journal|date=Spring, 2000}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('Spring 2000', $prepared->get2('date'));
    }

    public function testCleanDatesXtra6(): void {
        $text = '{{cite journal|date=May 03 2000}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('May 3, 2000', $prepared->get2('date'));
    }

    public function testCleanDatesXtra6b(): void {
        $text = '{{cite journal|date=May 03, 2000}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('May 3, 2000', $prepared->get2('date'));
    }

    public function testCleanDatesXtra7(): void {
        $text = '{{cite journal|date=May 3 1980}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('May 3, 1980', $prepared->get2('date'));
    }

    public function testCleanDatesXtra8(): void {
        $text = '{{cite journal|date=Collected 2010}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('2010', $prepared->get2('date'));
    }

    public function testCleanDatesXtra9(): void {
        $text = '{{cite journal|date=1980-03}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('March 1980', $prepared->get2('date'));
    }

    public function testCleanDatesXtra10(): void {
        $text = '{{cite journal|date=1999-13}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('1999-13', $prepared->get2('date'));
    }

    public function testCleanDatesXtra11(): void {
        $text = '{{cite journal|date=0001-11-30}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('', $prepared->get2('date'));
    }

    public function testCleanDatesXtra12(): void {
        $text = '{{cite journal|date=1960/ed}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('1960', $prepared->get2('date'));
    }

    public function testCleanDatesXtra13a(): void {
        $text = '{{cite journal|date=First published 1960}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('1960', $prepared->get2('date'));
    }

    public function testCleanDatesXtra13b(): void {
        $text = '{{cite journal|date=First published in 1960}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('1960', $prepared->get2('date'));
    }

    public function testCleanDatesXtra13c(): void {
        $text = '{{cite journal|date=First published in: 1960}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('1960', $prepared->get2('date'));
    }

    public function testCleanDatesXtra14(): void {
        $text = '{{cite journal|date=Effective spring 2021}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('Spring 2021', $prepared->get2('date'));
    }

    public function testCleanDatesXtra15a(): void {
        $text = '{{cite journal|date=2001 & 2002}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('2001Ã¢â‚¬â€œ2002', $prepared->get2('date'));
    }

    public function testCleanDatesXtra15b(): void {
        $text = '{{cite journal|date=2001 and 2002}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('2001Ã¢â‚¬â€œ2002', $prepared->get2('date'));
    }

    public function testCleanDatesXtra15c(): void {
        $text = '{{cite journal|date=2001 & 2003}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('2001 & 2003', $prepared->get2('date'));
    }

    public function testCleanDatesXtra15d(): void {
        $text = '{{cite journal|date=2001 and 2004}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('2001 and 2004', $prepared->get2('date'));
    }

    public function testCleanDatesXtra16(): void {
        $text = '{{cite journal|date=Summer, 1994-3333}}';
        $prepared = $this->prepare_citation($text);
        $this->assertSame('Summer 1994Ã¢â‚¬â€œ3333', $prepared->get2('date'));
    }

    public function testConvertingISBN10intoISBN13_1(): void {
        $text = "{{cite book|isbn=0-9749009-0-7|url=<!-- -->|year=2019}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('978-0-9749009-0-2', $prepared->get2('isbn'));  // Convert with dashes
    }

    public function testConvertingISBN10intoISBN13_2(): void {
        $text = "{{cite book|isbn=978-0-9749009-0-2|url=<!-- -->|year=2019}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('978-0-9749009-0-2', $prepared->get2('isbn'));  // Unchanged with dashes
    }

    public function testConvertingISBN10intoISBN13_3(): void {
        $text = "{{cite book|isbn=9780974900902|url=<!-- -->|year=2019}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('9780974900902', $prepared->get2('isbn'));    // Unchanged without dashes
    }

    public function testConvertingISBN10intoISBN13_4(): void {
        $text = "{{cite book|isbn=0974900907|url=<!-- -->|year=2019}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('978-0974900902', $prepared->get2('isbn'));   // Convert without dashes
    }

    public function testConvertingISBN10intoISBN13_5(): void {
        $text = "{{cite book|isbn=1-84309-164-X|url=<!-- -->|year=2019}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('978-1-84309-164-6', $prepared->get2('isbn'));  // Convert with dashes and a big X
    }

    public function testConvertingISBN10intoISBN13_6(): void {
        $text = "{{cite book|isbn=184309164x|url=<!-- -->|year=2019}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('978-1843091646', $prepared->get2('isbn'));   // Convert without dashes and a tiny x
    }

    public function testConvertingISBN10intoISBN13_7(): void {
        $text = "{{cite book|isbn=Hello Brother}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('Hello Brother', $prepared->get2('isbn')); // Rubbish unchanged
    }

    public function testConvertingISBN10intoISBN13_8(): void {
        $text = "{{cite book|isbn=184309164x 978324132412}}";
        $prepared = $this->prepare_citation($text);
        $this->assertSame('184309164x 978324132412', $prepared->get2('isbn'));  // Do not dash between multiple ISBNs
    }

    public function testConvertingISBN10intoISBN13_9(): void {
        $text = "{{cite book|isbn=0-9749009-0-7|url=<!-- -->|year=2019}}";
        $page = $this->process_page($text);
        $this->assertSame('Altered isbn. Add: publisher, title, authors 1-2. Upgrade ISBN10 to 13. | [[:en:WP:UCB|Use this bot]]. [[:en:WP:DBUG|Report bugs]]. ', $page->edit_summary());
    }

    public function testJournalCapitalization2(): void {
        $expanded = $this->process_citation("{{Cite journal|journal=eJournal}}");
        $this->assertSame('eJournal', $expanded->get2('journal'));
    }

    public function testJournalCapitalization3(): void {
        $expanded = $this->process_citation("{{Cite journal|journal=EJournal}}");
        $this->assertSame('eJournal', $expanded->get2('journal'));
    }

    public function testJournalCapitalization4(): void {
        $expanded = $this->process_citation("{{Cite journal|journal=ejournal}}");
        $this->assertSame('eJournal', $expanded->get2('journal'));
    }

    public function testDeWikifyWikilink(): void {
        $this->assertSame('test link', de_wikify('[[test link]]'));
    }

    public function testDeWikifyPipedWikilink(): void {
        $this->assertSame('display text', de_wikify('[[target|display text]]'));
    }

    public function testDeWikifyBoldMarkup(): void {
        $this->assertSame("'bold'", de_wikify("'''bold'''"));
    }

    public function testDeWikifyItalicMarkup(): void {
        $this->assertSame("'italic'", de_wikify("''italic''"));
    }

    public function testDeWikifyAmpersandRemoved(): void {
        $this->assertSame('', de_wikify('&'));
    }

    public function testDeWikifyPlainText(): void {
        $this->assertSame('plain text', de_wikify('plain text'));
    }

    public function testTruncatePublisherGroup(): void {
        $this->assertSame('Penguin', truncate_publisher('Penguin Group'));
    }

    public function testTruncatePublisherInc(): void {
        $this->assertSame('Company', truncate_publisher('Company Inc'));
    }

    public function testTruncatePublisherIncDot(): void {
        $this->assertSame('Company', truncate_publisher('Company Inc.'));
    }

    public function testTruncatePublisherLtd(): void {
        $this->assertSame('Company', truncate_publisher('Company Ltd'));
    }

    public function testTruncatePublisherPublishing(): void {
        $this->assertSame('Oxford', truncate_publisher('Oxford Publishing'));
    }

    public function testTruncatePublisherNoSuffix(): void {
        $this->assertSame('Random House', truncate_publisher('Random House'));
    }

    public function testStrRemoveIrrelevantBitsEmpty(): void {
        $this->assertSame('', str_remove_irrelevant_bits(''));
    }

    public function testStrRemoveIrrelevantBitsStripsLeadingThe(): void {
        $result = str_remove_irrelevant_bits('The New York Times');
        $this->assertStringNotContainsString('The ', $result);
    }

    public function testStrRemoveIrrelevantBitsAmpersandToAnd(): void {
        $result = str_remove_irrelevant_bits('Smith & Jones');
        $this->assertStringContainsString('and', $result);
    }

    public function testStrRemoveIrrelevantBitsWikilink(): void {
        $result = str_remove_irrelevant_bits('[[Nature (journal)|Nature]]');
        $this->assertStringNotContainsString('[[', $result);
        $this->assertStringContainsString('Nature', $result);
    }

    public function testMbStrrevBasic(): void {
        $this->assertSame('dcba', mb_strrev('abcd'));
    }

    public function testMbStrrevEmpty(): void {
        $this->assertSame('', mb_strrev(''));
    }

    public function testMbStrrevSingleChar(): void {
        $this->assertSame('a', mb_strrev('a'));
    }

    public function testMbStrrevPalindrome(): void {
        $this->assertSame('racecar', mb_strrev('racecar'));
    }

    public function testMbUcwordsLowercase(): void {
        $this->assertSame('Hello World', mb_ucwords('hello world'));
    }

    public function testMbUcwordsSingleWord(): void {
        $this->assertSame('Hello', mb_ucwords('hello'));
    }

    public function testMbUcwordsAlreadyUppercase(): void {
        $this->assertSame('HELLO', mb_ucwords('HELLO'));
    }

    public function testCanSafelyModifyDashesSimpleRange(): void {
        $this->assertTrue(can_safely_modify_dashes('1-10'));
    }

    public function testCanSafelyModifyDashesWithHttp(): void {
        $this->assertFalse(can_safely_modify_dashes('http://example.com'));
    }

    public function testCanSafelyModifyDashesWithProtocolRelative(): void {
        $this->assertFalse(can_safely_modify_dashes('[//example.com]'));
    }

    public function testCanSafelyModifyDashesWithHtmlTag(): void {
        $this->assertFalse(can_safely_modify_dashes('<span>text</span>'));
    }

    public function testCanSafelyModifyDashesWithPlaceholder(): void {
        $this->assertFalse(can_safely_modify_dashes('CITATION_BOT_PLACEHOLDER'));
    }

    public function testCanSafelyModifyDashesWithParenthesis(): void {
        $this->assertFalse(can_safely_modify_dashes('1-(2)'));
    }

    public function testCanSafelyModifyDashesSpacesAndLetters(): void {
        $this->assertFalse(can_safely_modify_dashes('some text'));
    }

    public function testCanSafelyModifyDashesThreeOrMoreDashes(): void {
        $this->assertFalse(can_safely_modify_dashes('1-2-3-4'));
    }

    public function testCanSafelyModifyDashesAlphaNumericPattern(): void {
        $this->assertFalse(can_safely_modify_dashes('A3-5'));
    }

    public function testCanSafelyModifyDashesYearAlpha(): void {
        $this->assertFalse(can_safely_modify_dashes('2005-A'));
    }

    public function testDoiEncodePreservesSlash(): void {
        $this->assertSame('10.1000/test', doi_encode('10.1000/test'));
    }

    public function testDoiEncodeEncodesSpace(): void {
        $result = doi_encode('10.1000/test value');
        $this->assertStringNotContainsString(' ', $result);
        $this->assertStringContainsString('10.1000', $result);
    }

    public function testDoiEncodeMultipleSlashes(): void {
        $this->assertSame('10.1000/path/to/doi', doi_encode('10.1000/path/to/doi'));
    }

    public function testHdlDecodeBasicHandle(): void {
        $this->assertSame('2027/test', hdl_decode('2027/test'));
    }

    public function testHdlDecodeSemicolonEncoded(): void {
        $this->assertSame('2027/test%3Bvalue', hdl_decode('2027/test;value'));
    }

    public function testHdlDecodeHashEncoded(): void {
        $this->assertSame('2027/test%23value', hdl_decode('2027/test#value'));
    }

    public function testHdlDecodeSpaceEncoded(): void {
        $this->assertSame('2027/test%20value', hdl_decode('2027/test value'));
    }

    public function testSafePregReplaceEmptyInput(): void {
        $this->assertSame('', safe_preg_replace('~a~', 'b', ''));
    }

    public function testSafePregReplaceBasicSubstitution(): void {
        $this->assertSame('hello world', safe_preg_replace('~test~', 'world', 'hello test'));
    }

    public function testSafePregReplaceNoMatch(): void {
        $this->assertSame('hello', safe_preg_replace('~zzz~', 'x', 'hello'));
    }

    public function testSafePregReplaceCallbackEmptyInput(): void {
        $result = safe_preg_replace_callback('~a~', static function (array $_m): string {
            return 'b';
        }, '');
        $this->assertSame('', $result);
    }

    public function testSafePregReplaceCallbackDoubles(): void {
        $result = safe_preg_replace_callback('~\d+~', static function (array $m): string {
            return (string) ((int) $m[0] * 2);
        }, 'test 5 here');
        $this->assertSame('test 10 here', $result);
    }

    public function testWikifyURLEncodesSpace(): void {
        $this->assertSame('http://example.com/my%20page', wikifyURL('http://example.com/my page'));
    }

    public function testWikifyURLEncodesDoubleQuote(): void {
        $this->assertSame('http://example.com/%22test%22', wikifyURL('http://example.com/"test"'));
    }

    public function testWikifyURLEncodesBrackets(): void {
        $this->assertSame('http://example.com/%5Btest%5D', wikifyURL('http://example.com/[test]'));
    }

    public function testWikifyURLEncodesPipe(): void {
        $this->assertSame('http://example.com/%7Ctest', wikifyURL('http://example.com/|test'));
    }

    public function testWikifyURLNoChangeNeeded(): void {
        $this->assertSame('http://example.com/test', wikifyURL('http://example.com/test'));
    }

    public function testEchoableDoiPlain(): void {
        $this->assertSame('10.1000/test', echoable_doi('10.1000/test'));
    }

    public function testEchoableDoiRestoresAngleBrackets(): void {
        // echoable_doi reverses the &lt;/&gt; escaping so < and > appear in output
        $result = echoable_doi('10.1000/<test>');
        $this->assertStringContainsString('<test>', $result);
    }

    public function testCleanVolumeStripsVolDot(): void {
        $this->assertSame('5', clean_volume('Vol. 5'));
    }

    public function testCleanVolumeStripsVolume(): void {
        $this->assertSame('10', clean_volume('Volume 10'));
    }

    public function testCleanVolumeStripsIssue(): void {
        $this->assertSame('3', clean_volume('Issue 3'));
    }

    public function testCleanVolumeNumericOnly(): void {
        $this->assertSame('42', clean_volume('42'));
    }

    public function testCleanVolumeParenthesisReturnsEmpty(): void {
        $this->assertSame('', clean_volume('5(2)'));
    }

    public function testCleanVolumeNovemberReturnsEmpty(): void {
        $this->assertSame('', clean_volume('november'));
    }

    public function testCleanVolumeNostradamusReturnsEmpty(): void {
        $this->assertSame('', clean_volume('nostradamus'));
    }
}
