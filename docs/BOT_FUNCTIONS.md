# Citation Bot Editing Functions

This document provides a comprehensive numbered list of ALL editing functions that Citation Bot performs on Wikipedia citations, including detailed descriptions and code locations.

## Overview

Citation Bot is a Wikipedia maintenance tool that automatically expands and formats bibliographic references. It retrieves metadata from authoritative sources (CrossRef, PubMed, arXiv, JSTOR, etc.) and generates properly formatted Wikipedia citation templates.

---

## Complete List of Bot Functions

### IDENTIFIER EXPANSION & EXTRACTION

1. **DOI (Digital Object Identifier)**
   - **Description**: Queries CrossRef API to retrieve complete citation metadata including title, authors, journal, volume, issue, pages, and publication dates. Validates DOI resolution and marks broken DOIs with `doi-broken-date`. Automatically sets `doi-access=free` for open access articles.
   - **Code Location**: `src/includes/api/APIdoi.php` - `expand_by_doi()` function (line 19)
   - **Related**: `Template::add_if_new()` calls this expansion (line 637)

2. **PMID (PubMed ID)**
   - **Description**: Retrieves citation data from PubMed/Entrez E-utilities API including authors, title, journal, volume, issue, pages, DOI, and PMC ID. Triggers automatic expansion of related identifiers. Handles both individual PMIDs and batch queries for efficiency.
   - **Code Location**: `src/includes/api/APIPubMed.php` - `query_pmid_api()` (line 9), `entrez_api()` (line 25)
   - **Related**: `find_pmid()` function searches for missing PMIDs (line 200)

3. **PMC (PubMed Central ID)**
   - **Description**: Fetches metadata from PubMed Central for open-access articles. Often found alongside PMID. Extracts full citation details and links to freely accessible full-text articles.
   - **Code Location**: `src/includes/api/APIPubMed.php` - `query_pmc_api()` (line 17), `entrez_api()` (line 25)
   - **Related**: PMC and PMID share the same Entrez API backend

4. **arXiv ID**
   - **Description**: Queries arXiv.org API to retrieve preprint metadata including authors, title, abstract, and categories. Extracts arXiv IDs from URLs. Can upgrade citations to published versions when DOI becomes available. Supports both old (arXiv:1234.5678) and new (arXiv:2301.12345) identifier formats.
   - **Code Location**: `src/includes/api/APIarXiv.php` - `expand_arxiv_templates()` (line 8), `arxiv_api()` (line 30)
   - **Related**: `Template::get_identifiers_from_url()` extracts arXiv IDs from URLs (line 2326)

5. **ISSN (International Standard Serial Number)**
   - **Description**: Validates ISSN format (8-digit number with hyphen: 1234-5678). Checks ISSN checksum for validity. Used to identify and validate journal identifiers.
   - **Code Location**: `src/includes/api/APIissn.php`
   - **Related**: ISSN validation in parameter handling

6. **ISBN (International Standard Book Number)**
   - **Description**: Converts ISBN-10 to ISBN-13 format automatically. Adds proper hyphenation (978-0-123-45678-9). Validates ISBN checksums. Extracts ISBNs from Google Books URLs and converts ASIN to ISBN when applicable.
   - **Code Location**: `src/includes/Template.php` - `add_if_new()` with ISBN formatting (line 637), ISBN conversion logic throughout
   - **Related**: `src/includes/constants/isbn.php` contains ISBN lookup tables

7. **JSTOR ID**
   - **Description**: Queries JSTOR API using RIS (Research Information Systems) format to retrieve complete citation metadata. Extracts JSTOR IDs from jstor.org URLs. Can find DOIs for JSTOR articles. Handles both numeric JSTOR IDs and stable URLs.
   - **Code Location**: `src/includes/api/APIjstor.php` - `expand_by_jstor()` (line 15)
   - **Related**: `URLtools::find_indentifiers_in_urls()` extracts JSTOR IDs from URLs (line 834)

8. **Bibcode**
   - **Description**: Queries NASA Astrophysics Data System (ADS) for astronomical and physics papers. Retrieves complete bibliographic data including authors, title, journal, year. Only runs in "slow mode" due to API response time. Critical for astronomy/astrophysics citations.
   - **Code Location**: `src/includes/api/APIBibCode.php`
   - **Related**: Bibcode searches are disabled in fast mode (gadget)

9. **S2CID (Semantic Scholar Corpus ID)**
   - **Description**: Uses Semantic Scholar API to find and verify academic paper identifiers. Can discover DOIs for papers indexed in Semantic Scholar. Links papers across multiple databases.
   - **Code Location**: `src/includes/api/APIS2.php`
   - **Related**: S2CID to DOI conversion

10. **LCCN (Library of Congress Control Number)**
    - **Description**: Converts {{LCCN}} template format to standard lccn= parameter. Extracts and formats Library of Congress Control Numbers for books and other library materials.
    - **Code Location**: `src/includes/Template.php` - template conversion logic
    - **Related**: Parameter normalization in `add_if_new()`

11. **ASIN (Amazon Standard Identification Number)**
    - **Description**: Amazon product identifier that's converted to ISBN when the ASIN is actually a valid ISBN-10. Retains ASIN when it's not convertible (e.g., for non-book products or international editions).
    - **Code Location**: `src/includes/Template.php` - `add_if_new()` ASIN handling (line 637)
    - **Related**: ISBN conversion logic

12. **HDL (Handle System Identifier)**
    - **Description**: Extracts Handle identifiers from hdl.handle.net URLs or DOI URLs containing Handle references. Handle System is a persistent identifier system used by many institutions.
    - **Code Location**: `src/includes/Template.php` - `get_identifiers_from_url()` (line 2326)
    - **Related**: URL parsing in `URLtools::find_indentifiers_in_urls()` (line 834)

13. **CiteSeerX ID**
    - **Description**: Converts {{CiteSeerX}} template citations to standard parameters. Extracts CiteSeerX identifiers and expands with available metadata from the CiteSeerX scientific literature digital library.
    - **Code Location**: `src/includes/Template.php` - template conversion logic in `get_identifiers_from_url()` (line 2326)
    - **Related**: Template type conversion

14. **eprint (arXiv parameter)**
    - **Description**: Legacy arXiv preprint identifier parameter. Renamed to `arxiv=` for {{cite arxiv}} templates. Maintains compatibility with older citation formats while standardizing to modern parameter names.
    - **Code Location**: `src/includes/api/APIarXiv.php` - parameter handling in `expand_arxiv_templates()` (line 8)
    - **Related**: Template parameter normalization

### AUTHOR/EDITOR PARAMETERS

15. **Author names** (last#, first#, author#, surname, given, forename)
    - **Description**: Retrieves author names from external APIs (CrossRef, PubMed, arXiv, JSTOR) and adds them using standardized parameter names. Handles multiple parameter formats (last1/first1, surname1/given1, author1) and normalizes to the appropriate format. Validates author data and ensures proper structured formatting.
    - **Code Location**: `src/includes/Template.php` - `add_if_new()` (line 637), `validate_and_add()` (line 551)
    - **Related**: API files populate author data; `NameTools.php` handles formatting

16. **Author name formatting**
    - **Description**: Parses "Lastname, Firstname" format and splits into separate `last=` and `first=` parameters. Handles complex names with hyphens, apostrophes, and multiple parts. Preserves name integrity while improving citation structure.
    - **Code Location**: `src/includes/NameTools.php` - `split_author()` (line 30)
    - **Related**: Called during author parameter processing in `Template::add_if_new()`

17. **Surname formatting**
    - **Description**: Applies proper capitalization rules to surnames including handling of Irish/Scottish names (O'Brien → O'Brien, not O'brien), McDonald/MacDonald patterns, and all-caps names. Preserves intentional capitalization while fixing common errors.
    - **Code Location**: `src/includes/NameTools.php` - `format_surname()` (line 82), `format_surname_2()` (line 109)
    - **Related**: Applied to all author and editor surnames

18. **Forename formatting**
    - **Description**: Adds periods to single-letter initials (J → J.), properly spaces multiple initials (JK → J. K.), and handles complex first names. Preserves full first names while standardizing initials.
    - **Code Location**: `src/includes/NameTools.php` - name cleaning functions throughout the file
    - **Related**: `split_author()` applies forename formatting

19. **Jr/Sr/III suffixes**
    - **Description**: Detects and properly extracts generational suffixes (Jr., Sr., II, III, IV) from author names. Preserves suffix formatting and placement according to citation style standards.
    - **Code Location**: `src/includes/NameTools.php` - `junior_test()` (line 13)
    - **Related**: Applied during name parsing in `split_author()`

20. **Display-authors limit**
    - **Description**: When more than 30 authors are present, automatically sets `display-authors=1` to show only the first author with "et al." in Wikipedia display. Prevents excessively long citation lists while maintaining full author data in parameters.
    - **Code Location**: `src/includes/Template.php` - author limit logic in template processing
    - **Related**: `Template::tidy()` applies display limits (line 5950)

21. **Authors parameter flattening**
    - **Description**: Converts the `authors=` parameter (which contains all authors in a single string) or `vauthors=` parameter into individual numbered author parameters (author1, author2, etc.) for better structure and processing.
    - **Code Location**: `src/includes/Template.php` - parameter conversion in `add_if_new()` (line 637)
    - **Related**: `NameTools::split_authors()` (line 367)

22. **vauthors conversion**
    - **Description**: Converts Vancouver-style author lists (vauthors=Smith J, Jones K) to individual last/first parameters following CS1 citation format. Parses Vancouver format and creates structured author parameters.
    - **Code Location**: `src/includes/Template.php` - Vancouver author processing
    - **Related**: `NameTools::split_authors()` handles parsing (line 367)

23. **Editor parameters** (editor#, editor-last#, editor-first#)
    - **Description**: Adds and formats editor information using the same logic as author parameters. Supports editor-last1/editor-first1 format. Retrieves editor data from APIs when available and applies same name formatting rules.
    - **Code Location**: `src/includes/Template.php` - editor handling in `add_if_new()` (line 637)
    - **Related**: Uses same name formatting functions as authors

24. **veditors flattening**
    - **Description**: Converts Vancouver-style editor lists (veditors=) to individual editor parameters (editor-last1, editor-first1, etc.) following the same process as vauthors conversion.
    - **Code Location**: `src/includes/Template.php` - editor parameter conversion in `add_if_new()` (line 637)
    - **Related**: Parallel to vauthors processing

25. **Translators**
    - **Description**: Adds translator information from API metadata when available. Formats translator names using the same capitalization and formatting rules as authors. Supports translator-last/translator-first parameter formats.
    - **Code Location**: `src/includes/Template.php` - translator parameter handling
    - **Related**: Uses `NameTools` functions for formatting

26. **Display-editors limit**
    - **Description**: When more than 30 editors are present, automatically sets `display-editors=1` to show only the first editor. Parallels display-authors functionality to prevent excessively long editor lists.
    - **Code Location**: `src/includes/Template.php` - editor limit logic
    - **Related**: Applied in `Template::tidy()` (line 5950)

### PUBLICATION METADATA

27. **Title**
    - **Description**: Retrieves article/book titles from external APIs (CrossRef, PubMed, arXiv, JSTOR). Applies title case capitalization. Creates wikilinks for appropriate terms. Sanitizes title text by removing markdown artifacts and cleaning formatting. Rejects placeholder titles like "PDF" or "Untitled".
    - **Code Location**: `src/includes/api/APIdoi.php` - CrossRef title extraction in `expand_by_doi()` (line 19+)
    - **Related**: `TextTools::wikify_external_text()` formats titles (line 20), `Template::add_if_new()` adds titles (line 637)

28. **Journal**
    - **Description**: Adds and expands journal names from API metadata. Consolidates aliases (work, periodical, journal) into the canonical journal parameter. Applies formatting and abbreviation expansion. Handles journal name variations and standardization.
    - **Code Location**: `src/includes/api/APIPubMed.php` - Entrez journal parsing (line 25+)
    - **Related**: `Template::add_if_new()` (line 637), journal aliases in constants

29. **Newspaper**
    - **Description**: Properly identifies newspaper citations and renames work/website parameters to newspaper when appropriate. Uses heuristics based on publisher/source to determine newspaper vs. journal classification.
    - **Code Location**: `src/includes/Template.php` - parameter type detection and renaming
    - **Related**: Publication type logic in `Template::tidy()` (line 5950)

30. **Magazine**
    - **Description**: Identifies magazine publications and maps work/website to magazine parameter. Distinguishes magazines from journals and newspapers based on metadata and naming patterns.
    - **Code Location**: `src/includes/Template.php` - parameter type detection and renaming
    - **Related**: Publication type logic in `Template::tidy()`

31. **Volume**
    - **Description**: Extracts volume numbers from API metadata. Converts malformed formats like "volume: 123" or "Vol. 123" to clean numeric values. Validates volume numbers for reasonableness.
    - **Code Location**: `src/includes/api/APIPubMed.php` - Entrez volume extraction (line 25+)
    - **Related**: `Template::add_if_new()` adds volumes (line 637)

32. **Issue/Number**
    - **Description**: Adds issue/number information from metadata. Handles journal-specific differences between issue and number parameters. Swaps issue/number when journal typically uses one over the other.
    - **Code Location**: `src/includes/api/APIPubMed.php` - Entrez issue extraction
    - **Related**: Issue/number swap logic in `Template::tidy()` (line 5950)

33. **Pages/Page**
    - **Description**: Adds page numbers and page ranges. Converts between page/pages parameters. Expands abbreviated page ranges (2342-5 → 2342-2345). Standardizes page range formatting with en-dashes. Validates and fixes inverted page ranges.
    - **Code Location**: `src/includes/Template.php` - page handling throughout, page range expansion
    - **Related**: Page formatting in `Template::tidy()` (line 5950)

34. **Article-number**
    - **Description**: Adds article-number parameter for online-only journals that don't use traditional page numbers. Modern journals often use article numbers (e.g., e12345) instead of page ranges.
    - **Code Location**: `src/includes/api/APIdoi.php` - CrossRef article-number extraction
    - **Related**: Added via `Template::add_if_new()` (line 637)

35. **Publisher**
    - **Description**: Adds publisher information from API metadata. Truncates excessively long publisher names. Removes location information embedded in publisher field. Cleans publisher names of junk text and artifacts.
    - **Code Location**: `src/includes/api/APIdoi.php` - CrossRef publisher data
    - **Related**: `TextTools::truncate_publisher()` (line 216), `Template::add_if_new()` (line 637)

36. **Publication-place/Location**
    - **Description**: Extracts and adds publication location/place information. Separates location from publisher when both are present in publisher field. Standardizes city/country formatting.
    - **Code Location**: `src/includes/api/APIdoi.php` - CrossRef location extraction
    - **Related**: Publisher/location parsing in API handlers

37. **Year**
    - **Description**: Extracts year from multiple date formats. Handles publication dates in various formats and normalizes to year parameter. Validates year reasonableness (typically post-1900, pre-future).
    - **Code Location**: `src/includes/Template.php` - year extraction and validation
    - **Related**: Date parsing throughout template code

38. **Date/Access-date**
    - **Description**: Parses and normalizes dates to YYYY-MM-DD ISO format. Handles various input date formats (MM/DD/YYYY, DD Month YYYY, etc.). Removes access-date when no URL present. Validates date logic and rejects impossible dates.
    - **Code Location**: `src/includes/TextTools.php` - date cleaning functions
    - **Related**: Date validation in `Template::add_if_new()` (line 637)

39. **Series**
    - **Description**: Adds series information for book series or journal series when available in metadata. Helps identify multi-volume works and continuing publications.
    - **Code Location**: `src/includes/api/APIdoi.php` - CrossRef series extraction
    - **Related**: Series parameter handling in template processing

40. **Chapter**
    - **Description**: Adds chapter titles for book chapters. Converts article/contribution parameters to chapter when appropriate. Distinguishes chapter-level citations from book-level citations.
    - **Code Location**: `src/includes/api/APIdoi.php` - CrossRef chapter handling
    - **Related**: Book chapter detection in template type logic

41. **Archive/Archiveurl**
    - **Description**: Processes and adds archived URL copies from Wayback Machine or other archives. Handles both archive-url and archiveurl parameter formats. Extracts archive dates. Prioritizes archive URLs when main URL is dead.
    - **Code Location**: `src/includes/api/APIarchives.php` - archive URL processing
    - **Related**: `URLtools` functions handle archive URL detection (line 834)

### IDENTIFIER VALIDATION & CLEANUP

42. **DOI validation**
    - **Description**: Verifies that DOIs actually resolve by making HTTP requests to doi.org. Checks for 404 errors or redirects that indicate broken DOIs. Ensures DOI format correctness (10.xxxx/yyyy pattern).
    - **Code Location**: `src/includes/Template.php` - DOI verification logic
    - **Related**: DOI resolution checking via HTTP requests

43. **DOI-access marking**
    - **Description**: Automatically sets `doi-access=free` for open access articles. Determines access status from CrossRef metadata or Unpaywall API. Helps readers identify freely accessible articles.
    - **Code Location**: `src/includes/api/APIdoi.php` - CrossRef access status in `expand_by_doi()` (line 19+)
    - **Related**: `src/includes/api/APIunpaywall.php` provides additional access information

44. **URL replacement with DOI**
    - **Description**: Replaces publisher proxy URLs, dead URLs, and paywalled URLs with clean dx.doi.org DOI links when DOI is available. Removes ScienceDirect, SpringerLink, and other publisher URLs that simply redirect to the DOI.
    - **Code Location**: `src/includes/URLtools.php` - `drop_urls_that_match_dois()` (line 13)
    - **Related**: URL cleanup in `Template::tidy()` (line 5950)

45. **URL deduplication**
    - **Description**: Removes redundant URLs when stable identifiers (DOI, PMID, PMC) are present. Keeps only the most useful URL and identifier combination. Prevents citation clutter with multiple equivalent links.
    - **Code Location**: `src/includes/URLtools.php` - `drop_urls_that_match_dois()` (line 13)
    - **Related**: Called during template cleanup

46. **Proxy URL cleanup**
    - **Description**: Detects and removes institutional proxy URLs (EZProxy, institutional access systems). Extracts clean URLs from proxy wrappers. Replaces proxy links with direct links or DOIs.
    - **Code Location**: `src/includes/URLtools.php` - `clean_existing_urls()` (line 934), `clean_existing_urls_INSIDE()` (line 941)
    - **Related**: URL cleaning throughout URLtools

47. **Invalid URL detection**
    - **Description**: Identifies and removes URLs that don't add value when DOI is present, especially ScienceDirect and Springer URLs that just redirect. Detects paywalled URLs that can be replaced with better alternatives.
    - **Code Location**: `src/includes/URLtools.php` - URL validation in `clean_existing_urls()` (line 934)
    - **Related**: Publisher-specific URL patterns

48. **Archive URL validation**
    - **Description**: Verifies archive-url vs archiveurl parameter consistency. Consolidates duplicate archive parameters. Validates that archive URLs are actually from recognized archive services (Wayback Machine, archive.today, etc.).
    - **Code Location**: `src/includes/URLtools.php` - archive URL handling in `find_indentifiers_in_urls()` (line 834)
    - **Related**: `src/includes/api/APIarchives.php` processes archives

### URL OPERATIONS

49. **URL simplification**
    - **Description**: Removes tracking parameters (utm_source, utm_campaign, etc.), session IDs, and other unnecessary query string parameters. Simplifies URLs to canonical forms. Removes fragments (#) that don't affect content.
    - **Code Location**: `src/includes/URLtools.php` - `url_simplify()` (line 901)
    - **Related**: Called on all URLs during processing

50. **URL extraction from identifiers**
    - **Description**: Parses URLs to extract embedded identifiers (DOI, PMID, arXiv, JSTOR, etc.). Recognizes identifier patterns in various URL formats from different publishers and services. Converts URLs to structured identifier parameters.
    - **Code Location**: `src/includes/Template.php` - `get_identifiers_from_url()` (line 2326)
    - **Related**: `URLtools::find_indentifiers_in_urls()` (line 834) does detailed extraction

51. **URL canonicalization**
    - **Description**: Standardizes URLs to their canonical forms. Converts http to https when appropriate. Removes www when not needed. Normalizes URL encoding and case.
    - **Code Location**: `src/includes/URLtools.php` - `url_simplify()` (line 901), `clean_existing_urls()` (line 934)
    - **Related**: URL normalization throughout URLtools

52. **Google Books URL handling**
    - **Description**: Extracts ISBNs from Google Books preview URLs. Simplifies complex Google Books URLs to just the ISBN when possible. Handles books.google.com URLs in various formats.
    - **Code Location**: `src/includes/URLtools.php` - `simplify_google_search()` (line 146)
    - **Related**: ISBN extraction from Google Books, `src/includes/api/APIgoogle.php`

53. **Archive.org URL parsing**
    - **Description**: Extracts original URLs from Wayback Machine (web.archive.org) links. Parses archive snapshot dates. Distinguishes archive URLs from archived content URLs. Handles various archive.org URL formats.
    - **Code Location**: `src/includes/URLtools.php` - `find_indentifiers_in_urls()` (line 834)
    - **Related**: Archive URL pattern matching

54. **Chapter-URL handling**
    - **Description**: Adds chapter-url parameter for book chapters to distinguish chapter-specific URLs from book-level URLs. Manages both url and chapter-url parameters appropriately based on citation type.
    - **Code Location**: `src/includes/Template.php` - URL type discrimination in parameter handling
    - **Related**: Book chapter detection logic

### TEXT FORMATTING & NORMALIZATION

55. **Non-breaking space removal**
    - **Description**: Replaces `&nbsp;` (non-breaking space HTML entities) with regular spaces at margins and in inappropriate locations. Preserves intentional non-breaking spaces within text but removes them from parameter boundaries.
    - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185)
    - **Related**: String cleaning in parameter processing

56. **Non-standard space removal**
    - **Description**: Removes Unicode spaces (U+2000-200A: en space, em space, thin space, hair space, etc.) and replaces with standard ASCII space (U+0020). Normalizes various whitespace characters to standard space.
    - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185)
    - **Related**: Unicode normalization

57. **Tab/newline/null byte removal**
    - **Description**: Cleans whitespace artifacts including tabs (\t), newlines (\n, \r), and null bytes (\0) that shouldn't appear in citation parameters. Prevents formatting issues and data corruption.
    - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185)
    - **Related**: Parameter value cleaning

58. **Multiple space collapse**
    - **Description**: Reduces sequences of multiple spaces to single spaces. Cleans up spacing artifacts from concatenation or copy-paste operations. Normalizes whitespace throughout citations.
    - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185)
    - **Related**: Applied to all text parameters

59. **BOM (Byte Order Mark) removal**
    - **Description**: Removes UTF-8 BOM characters (EF BB BF) that can appear at the start of text copied from certain sources. Prevents invisible characters from breaking formatting.
    - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185)
    - **Related**: Unicode cleaning

60. **Trailing/leading punctuation cleanup**
    - **Description**: Removes inappropriate trailing punctuation (colons, commas, semicolons, periods) from parameter values. Cleans up artifacts from copying text. Preserves intentional punctuation in titles and names.
    - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185)
    - **Related**: Parameter value tidying

61. **Quote normalization**
    - **Description**: Removes redundant opening/closing quotes from parameters. Converts "smart quotes" to straight quotes when appropriate. Handles both single and double quotes. Preserves quotes within text while removing boundary quotes.
    - **Code Location**: `src/includes/TextTools.php` - `straighten_quotes()` (line 388)
    - **Related**: Quote handling throughout text processing

62. **HTML entity cleanup**
    - **Description**: Converts HTML entities to their character equivalents: `&amp;` → &, `&apos;` → ', `&quot;` → ", `&lt;` → <, `&gt;` → >. Decodes numeric character references. Preserves wiki-safe entities where appropriate.
    - **Code Location**: `src/includes/TextTools.php` - entity decoding in `sanitize_string()` (line 185)
    - **Related**: HTML decoding functions

63. **Soft hyphen removal**
    - **Description**: Removes soft hyphens (U+00AD) used for line-breaking hints. These invisible characters can break matching and cause formatting issues. Cleans text from sources that use soft hyphens.
    - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185)
    - **Related**: Unicode normalization

64. **Diacritic stripping (for matching)**
    - **Description**: Removes accents and diacritics for similarity comparisons (é → e, ñ → n). Used for fuzzy matching of titles and names. Actual displayed text retains diacritics; stripping only used for comparison.
    - **Code Location**: `src/includes/TextTools.php` - `strip_diacritics()` (line 384)
    - **Related**: `titles_are_similar()` (line 278), `str_equivalent()` (line 263)

65. **Case normalization**
    - **Description**: Converts parameter names to lowercase for consistency. Applies title case to titles following capitalization rules. Normalizes ALL CAPS text to proper case. Preserves intentional capitalization in names and acronyms.
    - **Code Location**: `src/includes/TextTools.php` - case handling functions throughout
    - **Related**: `src/includes/constants/capitalization.php` contains capitalization rules

### DASH & HYPHEN NORMALIZATION

66. **En-dash standardization**
    - **Description**: Converts various dash types (em dash, minus sign, figure dash) to proper en-dash (&ndash; / &#x2013; / U+2013) for page ranges and date ranges. Ensures consistent dash usage across citations.
    - **Code Location**: `src/includes/TextTools.php` - dash normalization
    - **Related**: Applied in page range formatting

67. **Em-dash handling**
    - **Description**: Converts em dashes (—) to proper HTML entity `&mdash;` (&#x2014;). Used for breaks in titles or subtitles. Distinguishes em-dashes from en-dashes used in ranges.
    - **Code Location**: `src/includes/TextTools.php` - dash normalization
    - **Related**: Title formatting

68. **Hyphen standardization**
    - **Description**: Converts Unicode hyphens (U+2010) and other hyphen variants to standard ASCII hyphen-minus (U+002D). Ensures consistent hyphen rendering across systems.
    - **Code Location**: `src/includes/TextTools.php` - character normalization
    - **Related**: Text sanitization

69. **Page range dashes**
    - **Description**: Standardizes dashes in page ranges (e.g., 123–456) using en-dash. Expands abbreviated ranges (123-5 → 123-125, 2342-5 → 2342-2345). Validates page range logic (start < end).
    - **Code Location**: `src/includes/Template.php` - page range processing
    - **Related**: Page parameter handling

70. **Hyphenated names**
    - **Description**: Preserves hyphens in hyphenated surnames (Mary-Jane, Smith-Jones) while applying dash normalization elsewhere. Distinguishes name hyphens from en-dashes in ranges.
    - **Code Location**: `src/includes/NameTools.php` - name parsing functions
    - **Related**: Surname formatting (line 82)

### PARAMETER CORRECTION & CONSOLIDATION

71. **Parameter spelling correction**
    - **Description**: Uses Levenshtein distance algorithm to detect and correct parameter name typos (auhtor → author, paegs → pages). Matches against known parameter names. Confirms corrections before applying.
    - **Code Location**: `src/includes/Parameter.php` - parameter name validation
    - **Related**: `src/includes/constants/mistakes.php` contains common mistakes

72. **Parameter name normalization**
    - **Description**: Converts variant parameter names to standard forms (author1-last → last1, author-first1 → first1). Standardizes hyphenation and naming patterns across different citation template variants.
    - **Code Location**: `src/includes/Parameter.php` - parameter name processing
    - **Related**: Parameter aliases in constants

73. **Underscore to space conversion**
    - **Description**: Converts underscores to spaces in template names (cite_book → cite book, cite_web → cite web). Normalizes template naming to MediaWiki standard.
    - **Code Location**: `src/includes/Template.php` - template name normalization
    - **Related**: Template type handling

74. **Alias consolidation**
    - **Description**: Merges redundant parameters using the same aliases (work vs journal vs periodical, website vs work). Chooses the most appropriate parameter name based on citation type. Prevents duplicate information.
    - **Code Location**: `src/includes/Template.php` - parameter deduplication in `tidy()` (line 5950)
    - **Related**: Parameter alias mapping

75. **Empty parameter removal**
    - **Description**: Removes parameters that are blank, contain only whitespace, or have no meaningful value. Cleans up empty parameters from template conversion or data extraction failures.
    - **Code Location**: `src/includes/Template.php` - parameter cleaning in `tidy()` (line 5950)
    - **Related**: Parameter validation

76. **Duplicate parameter removal**
    - **Description**: When the same parameter appears multiple times, keeps the best version based on completeness and data quality. Removes exact duplicates. Resolves conflicts intelligently.
    - **Code Location**: `src/includes/Template.php` - duplicate detection in `tidy()` (line 5950)
    - **Related**: Parameter comparison logic

77. **Common mistakes correction**
    - **Description**: Applies hardcoded fixes for known common misspellings and errors in parameter names and values. Includes corrections from the mistakes constant file with frequent citation errors.
    - **Code Location**: `src/includes/constants/mistakes.php` - mistake mapping
    - **Related**: Applied during parameter processing

78. **Author parameter flattening**
    - **Description**: Converts `author="First Last"` format to separated last/first parameters. Parses full names into components. Handles various name formats and splits appropriately.
    - **Code Location**: `src/includes/NameTools.php` - `split_author()` (line 30)
    - **Related**: Called by `Template::add_if_new()` (line 637)

79. **ID parameter parsing**
    - **Description**: Extracts structured identifiers (DOI, PMID, ISBN, etc.) from unstructured `id=` field. Converts generic ID values to specific identifier parameters. Handles multiple identifiers in a single id field.
    - **Code Location**: `src/includes/Template.php` - ID parsing in `get_identifiers_from_url()` (line 2326)
    - **Related**: Identifier extraction logic

### TEMPLATE TYPE CONVERSIONS

80. **Cite paper → cite journal**
    - **Description**: Automatically converts {{cite paper}} templates to {{cite journal}} when the citation contains journal-specific parameters (journal name, volume, issue) or when a DOI resolves to a journal article. Uses metadata to determine appropriate template type.
    - **Code Location**: `src/includes/Template.php` - template type conversion in `tidy()` (line 5950) and `final_tidy()` (line 5970)
    - **Related**: Template type detection logic

81. **Cite document → cite journal/web/book**
    - **Description**: Smart conversion of generic {{cite document}} templates to more specific types based on available parameters and identifiers. Analyzes DOI, ISBN, URL patterns, and metadata to determine if it's a journal article, web page, or book.
    - **Code Location**: `src/includes/Template.php` - template type analysis in `tidy()` (line 5950)
    - **Related**: Metadata-driven template classification

82. **Cite website → cite arxiv/web**
    - **Description**: Converts {{cite website}} to {{cite arxiv}} when an arXiv identifier is present. Otherwise optimizes as {{cite web}}. Recognizes arXiv-specific patterns in URLs and parameters.
    - **Code Location**: `src/includes/Template.php` - template type conversion logic
    - **Related**: arXiv detection in `get_identifiers_from_url()` (line 2326)

83. **Cite book → cite journal**
    - **Description**: Converts {{cite book}} to {{cite journal}} when DOI metadata indicates it's actually a journal article, not a book. Prevents misclassification of articles that were initially templated as books.
    - **Code Location**: `src/includes/Template.php` - template type validation in `tidy()` (line 5950)
    - **Related**: DOI metadata analysis in `APIdoi.php`

84. **Cite arxiv → cite journal**
    - **Description**: Upgrades {{cite arxiv}} to {{cite journal}} when the preprint has been formally published and a working DOI is present. Verifies DOI resolution before converting. Preserves arXiv ID as secondary identifier.
    - **Code Location**: `src/includes/api/APIarXiv.php` - `expand_arxiv_templates()` (line 8), template upgrade logic
    - **Related**: DOI validation required for conversion

85. **Underscore replacement in template names**
    - **Description**: Converts underscored template names to spaces (cite_book → Cite book, cite_web → Cite web). Normalizes template naming to MediaWiki conventions. Also handles capitalization.
    - **Code Location**: `src/includes/Template.php` - template name normalization
    - **Related**: Template parsing and reconstruction

86. **Redirect resolution**
    - **Description**: Follows Wikipedia citation template redirects to their canonical target templates. Handles aliases like {{cite article}} → {{cite journal}}, {{citation/core}} → {{citation}}. Ensures consistent template names.
    - **Code Location**: `src/includes/Template.php` - template redirect handling
    - **Related**: Template name resolution

### PARAMETER RENAMING & MOVING

87. **work → journal**
    - **Description**: Renames `work=` parameter to `journal=` when citation is clearly a journal article (has volume, issue, or ISSN). Chooses appropriate parameter based on publication type. Consolidates aliases.
    - **Code Location**: `src/includes/Template.php` - parameter renaming logic in `tidy()` (line 5950)
    - **Related**: Publication type detection

88. **via → work/journal**
    - **Description**: Converts `via=` parameter to `work=` or `journal=` depending on context. Common for database-sourced citations where "via" describes the access method rather than the publication.
    - **Code Location**: `src/includes/Template.php` - parameter type mapping
    - **Related**: Parameter consolidation

89. **website → journal/newspaper**
    - **Description**: Renames `website=` to `journal=` for academic journals or `newspaper=` for news sources based on publisher and content analysis. Improves citation specificity.
    - **Code Location**: `src/includes/Template.php` - website categorization in `tidy()` (line 5950)
    - **Related**: Publisher pattern matching

90. **publisher → work**
    - **Description**: Renames `publisher=` to `work=` for news agencies (Reuters, Associated Press, UPI, AFP, etc.) that function as both publisher and publication. Handles wire services correctly.
    - **Code Location**: `src/includes/Template.php` - news agency detection in `tidy()` (line 5950)
    - **Related**: News agency lists in constants

91. **dead-url → url-status**
    - **Description**: Converts legacy `dead-url=` parameter to modern `url-status=` format. Maps values: dead-url=yes → url-status=dead, dead-url=no → url-status=live. Modernizes citation format.
    - **Code Location**: `src/includes/Template.php` - legacy parameter conversion in `tidy()` (line 5950)
    - **Related**: URL status handling

92. **deadurl → url-status**
    - **Description**: Converts alternative legacy parameter `deadurl=` (no hyphen) to `url-status=`. Handles same value mappings as dead-url. Ensures consistent modern parameter usage.
    - **Code Location**: `src/includes/Template.php` - legacy parameter conversion in `tidy()` (line 5950)
    - **Related**: URL status normalization

93. **url → chapter-url**
    - **Description**: Converts `url=` to `chapter-url=` for book chapters when the URL points to the specific chapter rather than the whole book. Distinguishes chapter-level vs book-level resources.
    - **Code Location**: `src/includes/Template.php` - URL type discrimination
    - **Related**: Book chapter detection logic

94. **chapter-url → url**
    - **Description**: Converts `chapter-url=` back to `url=` when appropriate, such as when citation type changes or when chapter-url is the only URL and citation isn't actually a chapter.
    - **Code Location**: `src/includes/Template.php` - URL parameter adjustment
    - **Related**: Template type changes

95. **author/agency rename**
    - **Description**: Converts Associated Press, Reuters, and other news agencies from `author=` to `agency=` parameter. Recognizes organizational authors that should be tagged as agencies.
    - **Code Location**: `src/includes/Template.php` - author type detection in `tidy()` (line 5950)
    - **Related**: Agency pattern matching

96. **Number/issue swap**
    - **Description**: Swaps `number=` ↔ `issue=` parameters based on journal-specific conventions. Some journals use "number" while others use "issue" for the same concept. Normalizes based on journal identity.
    - **Code Location**: `src/includes/Template.php` - journal-specific parameter mapping in `tidy()` (line 5950)
    - **Related**: Journal conventions database

### SPECIAL FIELD OPERATIONS

97. **Et al. handling**
    - **Description**: Detects and removes improperly placed "et al." text from author parameters. When "et al." is found, adds `display-authors=` parameter to properly show "et al." in display. Cleans author lists.
    - **Code Location**: `src/includes/Template.php` - author list processing in `tidy()` (line 5950)
    - **Related**: Author parameter cleanup

98. **Author limit enforcement**
    - **Description**: When more than 30 authors are present, automatically caps the list and sets `display-authors=1` to show only first author followed by "et al." Prevents excessively long citation displays while preserving metadata.
    - **Code Location**: `src/includes/Template.php` - author count limits in `tidy()` (line 5950)
    - **Related**: Display parameter management

99. **Editor limit enforcement**
    - **Description**: Applies same 30-editor limit as authors. Sets `display-editors=1` when too many editors are present. Prevents unwieldy editor lists in displayed citations.
    - **Code Location**: `src/includes/Template.php` - editor count limits in `tidy()` (line 5950)
    - **Related**: Display parameter management

100. **In Press detection**
     - **Description**: Handles "In press" or "In Press" volume values specially. Recognizes pre-publication status. Preserves this information while attempting to find published version details.
     - **Code Location**: `src/includes/Template.php` - volume validation in `tidy()` (line 5950)
     - **Related**: Publication status handling

101. **Online First handling**
     - **Description**: Recognizes "Online first", "Advance online publication", and similar pre-print indicators. Handles articles published online before print issue assignment. May attempt to find final publication details.
     - **Code Location**: `src/includes/Template.php` - publication status detection
     - **Related**: Volume/issue handling

102. **Access date handling**
     - **Description**: Removes `access-date=` parameter when no URL is present (access dates only apply to web resources). Normalizes date format to YYYY-MM-DD. Validates access date is not in future.
     - **Code Location**: `src/includes/Template.php` - access date validation in `tidy()` (line 5950)
     - **Related**: Date normalization in TextTools

103. **URL status mapping**
     - **Description**: Converts various boolean values to proper url-status values: yes/no/true/false/1/0 → dead/live. Normalizes URL status representation.
     - **Code Location**: `src/includes/Template.php` - url-status normalization in `tidy()` (line 5950)
     - **Related**: Legacy parameter conversion

104. **Agency/Publisher disambiguation**
     - **Description**: Analyzes content to determine whether an organization should be listed as `agency=`, `publisher=`, or `work=`. Applies logic based on entity type and citation context.
     - **Code Location**: `src/includes/Template.php` - entity classification in `tidy()` (line 5950)
     - **Related**: Organization type detection

105. **Book vs Journal detection**
     - **Description**: Analyzes DOI metadata, ISBN presence, and parameter combinations to determine if citation should be {{cite book}} or {{cite journal}}. Prevents template type mismatches.
     - **Code Location**: `src/includes/Template.php` - template type validation in `tidy()` (line 5950)
     - **Related**: Metadata analysis across API calls
### ISBN OPERATIONS

106. **ISBN-10 to ISBN-13 conversion**
     - **Description**: Automatically converts older 10-digit ISBN format to modern 13-digit ISBN-13 format (adds 978 prefix and recalculates checksum). Maintains backward compatibility while using current standards.
     - **Code Location**: `src/includes/Template.php` - ISBN conversion in `add_if_new()` (line 637)
     - **Related**: ISBN formatting logic, `src/includes/constants/isbn.php`

107. **ISBN dash formatting**
     - **Description**: Adds proper hyphenation to ISBNs following ISBN structure (978-0-123-45678-9 format). Placement of dashes depends on publisher prefix and group identifier. Improves ISBN readability.
     - **Code Location**: `src/includes/Template.php` - ISBN formatting in `add_if_new()` (line 637)
     - **Related**: ISBN hyphenation rules in constants

108. **ISBN validation**
     - **Description**: Verifies ISBN checksum using modulo 10 (ISBN-13) or modulo 11 (ISBN-10) algorithms. Detects and rejects invalid ISBNs. Prevents addition of malformed identifiers.
     - **Code Location**: `src/includes/Template.php` - ISBN checksum validation
     - **Related**: ISBN validation functions

109. **ASIN to ISBN conversion**
     - **Description**: Converts Amazon Standard Identification Numbers (ASINs) to ISBNs when the ASIN is actually a valid ISBN-10 (common for books). Retains ASIN when not ISBN-convertible (non-book products).
     - **Code Location**: `src/includes/Template.php` - ASIN processing in `add_if_new()` (line 637)
     - **Related**: ISBN detection and conversion

110. **Duplicate ISBN removal**
     - **Description**: When both ISBN-10 and ISBN-13 versions of the same ISBN are present, removes the ISBN-10 and keeps only ISBN-13. Prevents redundant identifiers in citations.
     - **Code Location**: `src/includes/Template.php` - ISBN deduplication in `tidy()` (line 5950)
     - **Related**: Parameter deduplication logic

### API-SPECIFIC EXPANSIONS

#### CrossRef/DOI API

111. **CrossRef title retrieval**
     - **Description**: Queries CrossRef API to retrieve article titles from DOI metadata. CrossRef is the primary DOI registration agency. Provides authoritative title information from publisher data.
     - **Code Location**: `src/includes/api/APIdoi.php` - title extraction in `expand_by_doi()` (line 19+)
     - **Related**: DOI metadata parsing

112. **CrossRef authors**
     - **Description**: Extracts complete author lists from CrossRef DOI metadata including given names, family names, and ORCID identifiers. Handles author affiliations and sequencing.
     - **Code Location**: `src/includes/api/APIdoi.php` - author parsing in `expand_by_doi()` (line 19+)
     - **Related**: Author name processing

113. **CrossRef journal/volume/issue**
     - **Description**: Retrieves complete bibliographic data including journal name, volume number, and issue number from CrossRef. Provides authoritative publication information from publisher records.
     - **Code Location**: `src/includes/api/APIdoi.php` - bibliographic data in `expand_by_doi()` (line 19+)
     - **Related**: Journal metadata processing

114. **CrossRef pages**
     - **Description**: Extracts page numbers and page ranges from CrossRef metadata. Handles various page formats including standard ranges, article numbers, and electronic-only publications.
     - **Code Location**: `src/includes/api/APIdoi.php` - page extraction in `expand_by_doi()` (line 19+)
     - **Related**: Page formatting functions

#### PubMed/Entrez API

115. **PMID-based expansion**
     - **Description**: Queries NCBI's Entrez E-utilities API using PubMed ID to retrieve complete citation data for biomedical literature. Fetches XML metadata and parses into citation parameters.
     - **Code Location**: `src/includes/api/APIPubMed.php` - `query_pmid_api()` (line 9), `entrez_api()` (line 25)
     - **Related**: XML parsing functions, `get_entrez_xml()` (line 158)

116. **PMC-based expansion**
     - **Description**: Retrieves metadata from PubMed Central using PMC identifiers. Accesses open-access full-text articles. Often finds additional metadata not in PubMed records.
     - **Code Location**: `src/includes/api/APIPubMed.php` - `entrez_api()` with PMC database (line 25)
     - **Related**: PubMed API calls

117. **PubMed authors**
     - **Description**: Extracts author names from PubMed/Entrez XML responses. Parses LastName and ForeName fields. Handles collective author names and consortia.
     - **Code Location**: `src/includes/api/APIPubMed.php` - author extraction in `entrez_api()` (line 25+)
     - **Related**: XML parsing of Author elements

118. **PubMed journal/volume/issue/pages**
     - **Description**: Retrieves complete journal citation metadata from PubMed including journal title (with abbreviations), volume, issue, and pagination. Provides MedLine-quality bibliographic data.
     - **Code Location**: `src/includes/api/APIPubMed.php` - metadata extraction in `entrez_api()` (line 25+)
     - **Related**: PubMed XML structure

#### arXiv API

119. **arXiv title**
     - **Description**: Queries arXiv.org API to retrieve manuscript titles for preprints. Accesses arXiv's XML feed. Provides preliminary publication information before peer review.
     - **Code Location**: `src/includes/api/APIarXiv.php` - title extraction in `arxiv_api()` (line 30)
     - **Related**: arXiv XML parsing

120. **arXiv authors**
     - **Description**: Extracts author lists from arXiv metadata. Parses arXiv's author format. Handles author affiliations when available. Supports both old and new arXiv identifier formats.
     - **Code Location**: `src/includes/api/APIarXiv.php` - author extraction in `arxiv_api()` (line 30)
     - **Related**: arXiv API responses

121. **arXiv abstract and categorization**
     - **Description**: Can extract abstract text and arXiv subject categories (e.g., astro-ph, hep-th, cs.AI). Provides subject classification for preprints.
     - **Code Location**: `src/includes/api/APIarXiv.php` - metadata parsing in `arxiv_api()` (line 30)
     - **Related**: arXiv taxonomy

122. **arXiv DOI linkage**
     - **Description**: Searches for published DOI corresponding to arXiv preprint. CrossRef tracks many arXiv → published paper relationships. Enables upgrade to full publication.
     - **Code Location**: `src/includes/api/APIarXiv.php` - DOI discovery in `expand_arxiv_templates()` (line 8)
     - **Related**: CrossRef linking service

123. **arXiv journal conversion**
     - **Description**: Automatically upgrades {{cite arxiv}} to {{cite journal}} when preprint has been published and DOI is available. Verifies DOI works before converting. Preserves arXiv ID.
     - **Code Location**: `src/includes/api/APIarXiv.php` - template conversion in `expand_arxiv_templates()` (line 8)
     - **Related**: Template type conversion logic

#### JSTOR API

124. **JSTOR title**
     - **Description**: Queries JSTOR using RIS (Research Information Systems) format export to retrieve article titles. JSTOR provides access to scholarly journal archives.
     - **Code Location**: `src/includes/api/APIjstor.php` - RIS parsing in `expand_by_jstor()` (line 15)
     - **Related**: RIS format parsing

125. **JSTOR authors**
     - **Description**: Extracts author names from JSTOR RIS data. Parses author field (AU) from RIS format. Handles multiple authors with proper formatting.
     - **Code Location**: `src/includes/api/APIjstor.php` - author extraction in `expand_by_jstor()` (line 15)
     - **Related**: RIS AU field parsing

126. **JSTOR journal/volume/issue/pages**
     - **Description**: Retrieves complete citation metadata from JSTOR including journal name (JO), volume (VL), issue (IS), start page (SP), and end page (EP) from RIS format.
     - **Code Location**: `src/includes/api/APIjstor.php` - bibliographic data in `expand_by_jstor()` (line 15)
     - **Related**: RIS field mapping

127. **JSTOR DOI extraction**
     - **Description**: Finds DOI identifiers in JSTOR metadata (DO field in RIS). Many JSTOR articles have associated DOIs. Enables cross-referencing with publisher records.
     - **Code Location**: `src/includes/api/APIjstor.php` - DOI extraction in `expand_by_jstor()` (line 15)
     - **Related**: DOI field parsing

#### Zotero/Citoid API

128. **Web URL expansion**
     - **Description**: Uses Zotero's web scraping and metadata extraction to get citation data from arbitrary URLs. Only runs in "slow mode" due to time requirements. Handles publisher websites, repositories, etc.
     - **Code Location**: `src/includes/api/APIzotero.php` - web scraping functions
     - **Related**: URL expansion logic (slow mode only)

129. **Generic title extraction**
     - **Description**: Extracts titles from web pages using Zotero/Citoid. Tries HTML `<title>` tags, Open Graph tags, Dublin Core metadata, and other structured data sources.
     - **Code Location**: `src/includes/api/APIzotero.php` - title extraction from HTML
     - **Related**: Metadata scraping

130. **Generic metadata extraction**
     - **Description**: Extracts authors, publication dates, publishers, and other metadata from web pages using various structured data formats (schema.org, COinS, Dublin Core, etc.).
     - **Code Location**: `src/includes/api/APIzotero.php` - metadata parsing
     - **Related**: Structured data extraction

#### Google Books API

131. **ISBN extraction**
     - **Description**: Queries Google Books API using book titles or URLs to find ISBN identifiers. Extracts ISBNs from Google Books preview pages and search results.
     - **Code Location**: `src/includes/api/APIgoogle.php` - ISBN discovery
     - **Related**: Google Books URL parsing in URLtools

132. **Publisher extraction**
     - **Description**: Retrieves publisher information from Google Books metadata. Provides authoritative publisher names for books.
     - **Code Location**: `src/includes/api/APIgoogle.php` - publisher field extraction
     - **Related**: Book metadata processing

133. **Author extraction**
     - **Description**: Extracts author names from Google Books data. Handles multiple authors and editors. Provides book authorship information.
     - **Code Location**: `src/includes/api/APIgoogle.php` - author parsing
     - **Related**: Book author processing

#### Semantic Scholar API

134. **S2CID to DOI**
     - **Description**: Queries Semantic Scholar's open API to find DOIs associated with Semantic Scholar Corpus IDs. Links S2CID to publisher DOIs for cross-referencing.
     - **Code Location**: `src/includes/api/APIS2.php` - DOI lookup
     - **Related**: Semantic Scholar API integration

#### BibCode (NASA ADS) API

135. **Astronomy data retrieval**
     - **Description**: Queries NASA Astrophysics Data System (ADS) for astronomical and physics literature. Retrieves complete bibliographic records. Only runs in "slow mode" due to API response time.
     - **Code Location**: `src/includes/api/APIBibCode.php` - ADS queries
     - **Related**: Bibcode search (slow mode only)

136. **Author lists from ADS**
     - **Description**: Extracts author names from NASA ADS bibcode records. Handles astronomical author conventions. Supports large collaboration lists common in astronomy.
     - **Code Location**: `src/includes/api/APIBibCode.php` - author extraction
     - **Related**: ADS metadata parsing

137. **Journal metadata from ADS**
     - **Description**: Retrieves journal name, volume, and page information from ADS. Provides canonical astronomy publication data. Handles both journal articles and conference proceedings.
     - **Code Location**: `src/includes/api/APIBibCode.php` - bibliographic extraction
     - **Related**: ADS database queries

### DATA QUALITY OPERATIONS

138. **Title case application**
     - **Description**: Applies proper capitalization rules to titles following English title case conventions. Capitalizes first and last words, major words. Preserves all-caps acronyms and proper nouns. Uses capitalization rules from constants.
     - **Code Location**: `src/includes/TextTools.php` - title case functions, `src/includes/constants/capitalization.php` rules
     - **Related**: `wikify_external_text()` (line 20)

139. **Bad title removal**
     - **Description**: Detects and removes known spam, placeholder, or meaningless titles ("PDF", "Untitled", "Document", "Microsoft Word", etc.). Prevents useless title additions from poor metadata sources.
     - **Code Location**: `src/includes/Template.php` - title validation in `add_if_new()` (line 637)
     - **Related**: `src/includes/constants/bad_data.php` contains bad title list

140. **Redundant title detection**
     - **Description**: Removes title parameter when it exactly matches journal name, series name, or work parameter. Prevents duplicate display of same information. Improves citation clarity.
     - **Code Location**: `src/includes/Template.php` - title comparison in `tidy()` (line 5950)
     - **Related**: `TextTools::titles_are_similar()` (line 278)

141. **Goofy title replacement**
     - **Description**: Replaces nonsensical titles extracted from bad metadata sources ("PDF", "WayBack Machine", "Login", "Error 404", etc.) with better alternatives or removes them entirely.
     - **Code Location**: `src/includes/Template.php` - title filtering in `tidy()` (line 5950)
     - **Related**: Bad title patterns in constants

142. **Title sanitization**
     - **Description**: Removes markdown-style links [link text](url), HTML tags, control characters, and other artifacts from titles. Cleans text copied from web pages or documents.
     - **Code Location**: `src/includes/TextTools.php` - `sanitize_string()` (line 185), `de_wikify()` (line 291)
     - **Related**: Text cleaning functions

143. **Author validation**
     - **Description**: Validates author count is reasonable (not 0, not >100 in most cases). Checks for suspicious patterns (all single letters, all numbers, etc.). Rejects clearly malformed author data.
     - **Code Location**: `src/includes/Template.php` - author validation in `tidy()` (line 5950)
     - **Related**: `NameTools` validation functions

144. **Year validation**
     - **Description**: Checks publication years are within reasonable ranges (typically 1500-current year +1). Rejects impossible years (year 0, negative years, far future). Validates against access-date for consistency.
     - **Code Location**: `src/includes/Template.php` - year validation in `add_if_new()` (line 637) and `tidy()` (line 5950)
     - **Related**: Date validation logic

145. **Volume validation**
     - **Description**: Rejects suspicious volume numbers (extremely large numbers, negative numbers, non-numeric values). Validates against expected journal volume ranges. Handles "In press" specially.
     - **Code Location**: `src/includes/Template.php` - volume validation in `tidy()` (line 5950)
     - **Related**: Numeric parameter validation

146. **Issue validation**
     - **Description**: Rejects suspicious issue numbers like 6-digit numbers (likely years or dates mistakenly placed in issue field). Validates issue format. Checks reasonableness against volume.
     - **Code Location**: `src/includes/Template.php` - issue validation in `tidy()` (line 5950)
     - **Related**: Parameter validation logic

147. **Page range validation**
     - **Description**: Fixes inverted page ranges (456-123 → 123-456). Validates start page < end page. Checks for reasonable page numbers. Expands abbreviated ranges (123-5 → 123-125).
     - **Code Location**: `src/includes/Template.php` - page validation in `tidy()` (line 5950)
     - **Related**: Page formatting functions

148. **Date validation**
     - **Description**: Checks dates are realistic and properly formatted. Validates month (1-12), day (1-31), year. Handles pre-1900 dates by reverting to year-only. Rejects impossible dates (Feb 30, etc.).
     - **Code Location**: `src/includes/Template.php` - date validation in `add_if_new()` (line 637)
     - **Related**: Date parsing in TextTools

### ARCHIVE/PRESERVATION OPERATIONS

149. **Wayback Machine detection**
     - **Description**: Identifies Wayback Machine (web.archive.org) URLs and extracts the original URL being archived. Parses archive snapshot timestamp. Separates archive URL from archived content.
     - **Code Location**: `src/includes/URLtools.php` - archive detection in `find_indentifiers_in_urls()` (line 834)
     - **Related**: `src/includes/api/APIarchives.php`

150. **Internet Archive handling**
     - **Description**: Processes various archive.org URL formats including Wayback Machine, Archive-It, and other Internet Archive services. Extracts original URLs and archive dates.
     - **Code Location**: `src/includes/api/APIarchives.php` - archive URL processing
     - **Related**: Archive URL patterns

151. **Archive date extraction**
     - **Description**: Parses archive snapshot dates from Wayback Machine timestamps (format: YYYYMMDDHHMMSS). Converts to archive-date parameter in YYYY-MM-DD format. Validates dates are reasonable.
     - **Code Location**: `src/includes/api/APIarchives.php` - date extraction
     - **Related**: Archive URL parsing

152. **Dead URL flagging**
     - **Description**: Marks URLs as dead when they return 404 errors or other failure codes. Adds url-status=dead and archive-date when archive URL exists. Helps identify link rot.
     - **Code Location**: `src/includes/Template.php` - URL status in `tidy()` (line 5950)
     - **Related**: URL validation checks

153. **Archive URL prioritization**
     - **Description**: Keeps archive URLs when main URL fails or is dead. Moves archive URL to main url parameter if no working URL exists. Ensures citation has accessible link.
     - **Code Location**: `src/includes/Template.php` - URL prioritization in `tidy()` (line 5950)
     - **Related**: Archive parameter handling

### CLEANUP & SANITATION

154. **Wikilink formatting**
     - **Description**: Properly formats [[wikilink]] markup in titles and other fields. Ensures balanced brackets. Removes or fixes malformed links. Preserves intentional wikilinks while fixing syntax.
     - **Code Location**: `src/includes/TextTools.php` - link formatting, `de_wikify()` (line 291)
     - **Related**: Wiki markup handling

155. **External link removal**
     - **Description**: Removes external link markup [http://example.com link text] from author names, titles, and other fields where external links don't belong. Preserves text, removes URL.
     - **Code Location**: `src/includes/TextTools.php` - link removal
     - **Related**: Parameter sanitization

156. **Placeholder removal**
     - **Description**: Cleans internal bot placeholder markers (CITATION_BOT_PLACEHOLDER_*) used during processing. Ensures placeholders don't leak into final citation output.
     - **Code Location**: `src/includes/Template.php` - placeholder cleanup in `final_tidy()` (line 5970)
     - **Related**: `WikiThings.php` placeholder management

157. **Comment preservation**
     - **Description**: Carefully preserves HTML comments (<!-- -->) in citation parameters. Protects comments from being damaged during processing. Important for Wikipedia editor notes.
     - **Code Location**: `src/includes/WikiThings.php` - comment handling
     - **Related**: Wiki markup preservation

158. **Translit/script handling**
     - **Description**: Manages transliterated titles (trans-title) and non-Latin script titles (script-title). Preserves romanization and original script versions. Handles language parameters.
     - **Code Location**: `src/includes/Template.php` - transliteration parameter handling
     - **Related**: Multi-language citation support

159. **Citation-bot placeholder parsing**
     - **Description**: Parses and properly handles CITATION_BOT_PLACEHOLDER_* markers used to temporarily store complex wiki markup during processing. Ensures correct restoration after processing.
     - **Code Location**: `src/includes/WikiThings.php` - placeholder system
     - **Related**: Template parsing and reconstruction

160. **Floating text detection**
     - **Description**: Finds parameter values that are hidden within other text values. Detects malformed parameters like "title=sometitle|year=2020" and extracts hidden parameter. Fixes parameter parsing errors.
     - **Code Location**: `src/includes/Parameter.php` - parameter parsing
     - **Related**: Template parsing logic

161. **Malformed parameter detection**
     - **Description**: Catches and fixes malformed parameters like "archive=url=http://..." where parameter names are duplicated or mangled. Repairs common template syntax errors.
     - **Code Location**: `src/includes/Parameter.php` - parameter validation
     - **Related**: Template syntax repair

162. **ISBN extraction from text**
     - **Description**: Finds ISBNs hidden within text fields (like "ISBN: 978-0-123-45678-9 published in..." in title or publisher). Extracts ISBN to proper parameter and cleans source text.
     - **Code Location**: `src/includes/Template.php` - ISBN pattern matching in `get_identifiers_from_url()` (line 2326)
     - **Related**: Identifier extraction

### SPECIAL CASE HANDLING

163. **Journal-specific logic**
     - **Description**: Applies special rules for specific journals: Oxford Dictionary of National Biography (Oxford DNB), JSTOR proprietary rules, IEEE publication formatting, etc. Handles publisher-specific requirements.
     - **Code Location**: `src/includes/Template.php` - journal-specific code in `tidy()` (line 5950)
     - **Related**: `URLtools::clean_and_expand_up_oxford_stuff()` (line 365)

164. **DOI prefix handling**
     - **Description**: Special processing for DOI prefixes indicating specific publishers: 10.1093/ (Oxford), 10.1109/ (IEEE), 10.1371/ (PLOS), etc. Applies publisher-specific citation enhancements.
     - **Code Location**: `src/includes/api/APIdoi.php` - prefix-specific logic in `expand_by_doi()` (line 19+)
     - **Related**: Publisher DOI patterns

165. **Reference genome mapping**
     - **Description**: Handles NCBI reference sequences and genome database identifiers. Processes GenBank, RefSeq, and other molecular biology database references. Specialized for genomics citations.
     - **Code Location**: `src/includes/Template.php` - database identifier handling
     - **Related**: Biological database integration

166. **ResearchGate DOI replacement**
     - **Description**: When ResearchGate DOI (10.13140/) is found, attempts to find actual publisher DOI. ResearchGate DOIs point to uploads on their site, not peer-reviewed publications. Upgrades to proper DOI when possible.
     - **Code Location**: `src/includes/api/APIdoi.php` - DOI replacement logic
     - **Related**: DOI quality improvement

167. **Bad DOI replacement**
     - **Description**: When a DOI doesn't resolve or is known to be broken, searches for alternative working DOI using title/author matching. Finds correct DOI to replace bad one. Improves citation reliability.
     - **Code Location**: `src/includes/Template.php` - DOI replacement in `tidy()` (line 5950)
     - **Related**: DOI verification and search

168. **Conference paper detection**
     - **Description**: Identifies conference papers from URLs, DOIs, or metadata patterns. Distinguishes conference proceedings from journals. May adjust template type or parameters accordingly.
     - **Code Location**: `src/includes/Template.php` - publication type detection
     - **Related**: Conference vs journal classification

169. **Book chapter detection**
     - **Description**: Identifies citations that are book chapters rather than whole books based on chapter parameter, chapter URLs, or DOI metadata indicating contribution to edited volume.
     - **Code Location**: `src/includes/Template.php` - book chapter logic in `tidy()` (line 5950)
     - **Related**: Chapter parameter handling

170. **Preprint handling**
     - **Description**: Manages bioRxiv, medRxiv, and other preprint server templates. Attempts to find published versions. Handles transition from preprint to published article. Preserves preprint identifiers.
     - **Code Location**: `src/includes/Template.php` - preprint template processing
     - **Related**: Preprint server URL patterns

171. **Thesis detection**
     - **Description**: Identifies PhD dissertations, Master's theses, and other thesis types from metadata, URLs (ProQuest, institutional repositories), or degree parameter. Ensures appropriate thesis-specific parameters.
     - **Code Location**: `src/includes/Template.php` - thesis identification
     - **Related**: Thesis template handling

### MATHEMATICAL NOTATION CONVERSION

172. **MathML to LaTeX conversion**
     - **Description**: Converts MathML (Mathematical Markup Language) elements to LaTeX syntax for Wikipedia citations. Handles complex MathML structures including superscripts (msup), subscripts (msub), fractions (mfrac), square roots (mroot), isotope notation (mmultiscripts), and other mathematical expressions. **Only applies when adding NEW parameters** via `add_if_new()` - existing MathML in citations is preserved.
     - **Code Location**: `src/includes/MathTools.php` - `convert_mathml_to_latex()` function
     - **Related**: Called during parameter addition to convert mathematical notation

173. **LaTeX formula formatting**
     - **Description**: Properly formats LaTeX mathematical expressions for Wikipedia display. Wraps expressions in appropriate delimiters. Handles special mathematical symbols, operators, and notation. Ensures LaTeX syntax is wiki-compatible.
     - **Code Location**: `src/includes/MathTools.php` - LaTeX formatting functions
     - **Related**: Mathematical notation in titles and parameters

174. **Chemical formula notation**
     - **Description**: Handles chemical formulas and isotope notation in citations. Converts chemical elements to proper formatting with mass numbers as superscripts (e.g., ⁶⁷Ni becomes ^{67}\mathrm{Ni}). Preserves scientific notation in titles and other parameters.
     - **Code Location**: `src/includes/MathTools.php` - isotope and chemical handling in `convert_mathml_to_latex()`
     - **Related**: Scientific publication titles with formulas

### ADDITIONAL IDENTIFIER OPERATIONS

175. **PII (Publisher Item Identifier) to DOI conversion**
     - **Description**: Converts Publisher Item Identifiers (PII) to DOIs when possible. PII is used by some publishers (especially Elsevier) as an alternative identifier. Queries publisher databases to find corresponding DOI.
     - **Code Location**: `src/includes/api/APIpii.php` - `get_doi_from_pii()` function
     - **Related**: Identifier consolidation and DOI discovery

176. **SICI (Serial Item and Contribution Identifier) handling**
     - **Description**: Processes SICI identifiers used for journal articles and contributions. SICI is a legacy identifier format. Extracts bibliographic data from SICI codes when present.
     - **Code Location**: `src/includes/api/APIsici.php` - `use_sici()` function
     - **Related**: Legacy identifier support

### OPEN ACCESS DETECTION

177. **Unpaywall open access URL discovery**
     - **Description**: Queries Unpaywall API to find legal open access versions of paywalled articles. Discovers free PDF links from repositories, PubMed Central, and publisher websites. Adds open access URLs when available. Sets appropriate access indicators.
     - **Code Location**: `src/includes/api/APIunpaywall.php` - `get_unpaywall_url()`, `get_open_access_url()` functions
     - **Related**: DOI-access parameter setting, open access promotion

178. **Semantic Scholar open access detection**
     - **Description**: Uses Semantic Scholar API to detect open access status and find freely available versions. Retrieves license information. Discovers alternative access methods for scholarly articles.
     - **Code Location**: `src/includes/api/APIS2.php` - `get_semanticscholar_license()`, `get_semanticscholar_url()` functions
     - **Related**: S2CID operations, open access flagging

### PUBLISHER-SPECIFIC OPERATIONS

179. **IEEE-specific metadata expansion**
     - **Description**: Handles IEEE (Institute of Electrical and Electronics Engineers) publication-specific metadata retrieval. Queries IEEE Xplore. Extracts conference paper vs. journal article distinctions. Handles IEEE's unique citation requirements.
     - **Code Location**: `src/includes/api/APIieee.php` - `query_ieee_webpages()` function
     - **Related**: DOI prefix 10.1109/ handling

180. **Oxford DNB (Dictionary of National Biography) cleanup**
     - **Description**: Applies special formatting rules for Oxford Dictionary of National Biography citations. Handles Oxford Academic URLs. Processes ODNB-specific identifiers and parameters. Ensures compliance with ODNB citation standards.
     - **Code Location**: `src/includes/miscTools.php` - `clean_cite_odnb()` function, `src/includes/URLtools.php` - `clean_and_expand_up_oxford_stuff()` (line 365)
     - **Related**: Journal-specific logic for Oxford publications

### TEXT ENCODING & CHARACTER HANDLING

181. **Character encoding normalization**
     - **Description**: Converts various character encodings (ISO-8859-1, Windows-1252, UTF-16, etc.) to UTF-8 for consistent display. Detects encoding from metadata or content. Fixes mojibake (garbled text from encoding mismatches). Handles special characters, accented letters, and non-Latin scripts properly.
     - **Code Location**: `src/includes/api/APIarchives.php` - `convert_to_utf8()`, `convert_to_utf8_inside()`, `smart_decode()` functions
     - **Related**: Archive content processing, international character handling

182. **Encoding validation**
     - **Description**: Validates that character encoding is reasonable and doesn't contain control characters or invalid UTF-8 sequences. Checks for encoding artifacts. Rejects suspiciously encoded data.
     - **Code Location**: `src/includes/api/APIarchives.php` - `is_encoding_reasonable()` function
     - **Related**: Data quality validation

### PUBLICATION TYPE DISAMBIGUATION

183. **Conference vs journal article disambiguation**
     - **Description**: Distinguishes conference papers from journal articles based on DOI prefixes, publisher patterns, and metadata. Some conference proceedings pretend to be journals. Adjusts template type and parameters accordingly. Critical for proper citation classification.
     - **Code Location**: `src/includes/miscTools.php` - `handleConferencePretendingToBeAJournal()` function, `src/includes/doiTools.php` - `conference_doi()` function
     - **Related**: Template type conversion (function 80-86)

---

## Summary

This comprehensive list represents **183 distinct editing, expansion, and normalization operations** that Citation Bot performs on citation templates. The bot's main workflow uses `add_if_new()`, `tidy()`, and `final_tidy()` functions in `Template.php` to apply these operations systematically.

## Operating Modes

### Fast Mode (Gadget Default)
- Operations 1-176: All identifier expansion, parameter addition, and cleanup operations
- **Excludes**: Bibcode searches (function 8), URL expansion via Zotero (functions 128-130), and some slow API operations

### Slow Mode (Web Interface Default)  
- **All 183 operations** including bibcode searches, comprehensive URL expansion, and full open access detection

## Source Code References

- **Main expansion logic**: `src/includes/Template.php`
- **Parameter handling**: `src/includes/Parameter.php`
- **API integrations**: `src/includes/api/*.php`
- **URL operations**: `src/includes/URLtools.php`
- **Name formatting**: `src/includes/NameTools.php`
- **Text processing**: `src/includes/TextTools.php`
- **Mathematical notation**: `src/includes/MathTools.php`
- **DOI utilities**: `src/includes/doiTools.php`
- **Miscellaneous tools**: `src/includes/miscTools.php`

## More Information

- **Documentation**: <https://en.wikipedia.org/wiki/User:Citation_bot/use>
- **Bug reports**: <https://en.wikipedia.org/wiki/User_talk:Citation_bot>
- **Source code**: <https://github.com/ms609/citation-bot>

---

**Last updated**: January 2026  
**Maintained by**: Citation Bot community
