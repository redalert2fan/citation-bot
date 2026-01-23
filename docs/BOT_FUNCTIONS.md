# Citation Bot Editing Functions

This document provides a comprehensive numbered list of ALL editing functions that Citation Bot performs on Wikipedia citations.

## Overview

Citation Bot is a Wikipedia maintenance tool that automatically expands and formats bibliographic references. It retrieves metadata from authoritative sources (CrossRef, PubMed, arXiv, JSTOR, etc.) and generates properly formatted Wikipedia citation templates.

---

## Complete List of Bot Functions

### IDENTIFIER EXPANSION & EXTRACTION

1. **DOI (Digital Object Identifier)** - Added/expanded via CrossRef API; validated and corrected
2. **PMID (PubMed ID)** - Added via PubMed/Entrez API; triggers automatic expansion
3. **PMC (PubMed Central ID)** - Added via PubMed API; linked to PMID retrieval
4. **arXiv ID** - Added via arXiv API; extracts from URLs or explicit identifiers
5. **ISSN (International Standard Serial Number)** - Validated and added
6. **ISBN (International Standard Book Number)** - Added; converted from ISBN-10 to ISBN-13; formatted with dashes
7. **JSTOR ID** - Added via JSTOR API; extracted from URLs or DOI conversion
8. **Bibcode** - Added via NASA ADS API; validated for astronomical data
9. **S2CID (Semantic Scholar ID)** - Added; triggers DOI lookup from Semantic Scholar
10. **LCCN (Library of Congress Control Number)** - Converted from {{LCCN}} templates
11. **ASIN (Amazon Standard ID)** - Added or converted to ISBN when applicable
12. **HDL (Handle)** - Extracted from DOI or added directly
13. **CiteSeerX ID** - Converted from CiteSeerX templates
14. **eprint** - arXiv pre-print identifier (renamed to arxiv for cite arxiv template)

### AUTHOR/EDITOR PARAMETERS

15. **Author names** (last#, first#, author#, surname, given, forename) - Added via APIs; formatted and cleaned
16. **Author name formatting** - Converts "Lastname, Firstname" → split into last/first parameters
17. **Surname formatting** - Applies proper capitalization (e.g., "O'Brien", "McDonald")
18. **Forename formatting** - Adds periods to single initials; separates multiple initials
19. **Jr/Sr/III suffixes** - Extracted and preserved from author names
20. **Display-authors** - Added when limiting author display (limits to 1 when >30 authors)
21. **Authors parameter** - Flattened from vauthors or vauthors when present
22. **vauthors** - Converted to individual last/first parameters
23. **Editor parameters** (editor#, editor-last#, editor-first#) - Added similarly to authors
24. **veditors** - Flattened to individual editor parameters
25. **Translators** - Added via APIs; formatted like author parameters
26. **Display-editors** - Added when limiting editor display

### PUBLICATION METADATA

27. **Title** - Added/expanded via APIs; formatted with title case; wikilinked where appropriate
28. **Journal** - Added/expanded; formatted; aliases (work, periodical) consolidated
29. **Newspaper** - Properly mapped from work/website when appropriate
30. **Magazine** - Properly mapped from work/website when appropriate
31. **Volume** - Added/expanded; converted from malformed "volume: 123" formats
32. **Issue/Number** - Added/expanded; swapped when journal has no volume
33. **Pages/Page** - Added; converted between page/pages; expanded abbreviated ranges (2342-5 → 2342-2345)
34. **Article-number** - Added for online-only articles
35. **Publisher** - Added/expanded; truncated when too long; removed locations and junk
36. **Publication-place/Location** - Added; consolidated from publisher field
37. **Year** - Extracted and added from multiple formats
38. **Date/Access-date** - Parsed and normalized to YYYY-MM-DD format
39. **Series** - Added when available in metadata
40. **Chapter** - Added for book chapters; converted from article/contribution parameters
41. **Archive/Archiveurl** - Processed and added for archived copies

### IDENTIFIER VALIDATION & CLEANUP

42. **DOI validation** - Checks if DOI resolves; marks inactive with doi-broken-date
43. **DOI-access** - Added as "free" when appropriate; triggers URL consolidation
44. **URL replacement with DOI** - Replaces proxy/dead URLs with dx.doi.org links
45. **URL deduplication** - Removes redundant URLs when DOI/PMID/PMC present
46. **Proxy URL cleanup** - Drops or replaces proxy URLs (EZProxy, institutional access)
47. **Invalid URL detection** - Removes ScienceDirect/Springer URLs when DOI available
48. **Archive URL validation** - Verifies and consolidates archive-url vs archiveurl

### URL OPERATIONS

49. **URL simplification** - Removes tracking parameters and query strings
50. **URL extraction from identifiers** - Parses URLs to extract DOI, PMID, arXiv, etc.
51. **URL canonicalization** - Standardizes URLs to canonical forms
52. **Google Books URL handling** - Extracts ISBN from Google Books URLs
53. **Archive.org URL parsing** - Extracts identifiers from Wayback Machine URLs
54. **Chapter-URL** - Added for book chapters; distinguished from main URL

### TEXT FORMATTING & NORMALIZATION

55. **Non-breaking space removal** - Replaces &nbsp; with regular spaces at margins
56. **Non-standard space removal** - Removes Unicode spaces (U+2000-200A, etc.)
57. **Tab/newline/null byte removal** - Cleans whitespace artifacts
58. **Multiple space collapse** - Reduces multiple spaces to single space
59. **BOM (Byte Order Mark) removal** - Removes UTF-8 BOM characters
60. **Trailing/leading punctuation cleanup** - Removes trailing colons, commas, semicolons
61. **Quote normalization** - Removes redundant opening/closing quotes
62. **HTML entity cleanup** - Converts &amp; → &, &apos; → ', etc.
63. **Soft hyphen removal** - Removes U+00AD soft hyphens
64. **Diacritic stripping** - Removes accents for similarity matching
65. **Case normalization** - Converts parameter names to lowercase; title case for titles

### DASH & HYPHEN NORMALIZATION

66. **En-dash standardization** - Converts various dash types to &ndash; (&#x2013;)
67. **Em-dash handling** - Converts — to &mdash; (&#x2014;)
68. **Hyphen standardization** - Converts Unicode hyphens (U+2010) to ASCII hyphen
69. **Page range dashes** - Standardizes dashes in page ranges (e.g., 123–456)
70. **Hyphenated names** - Preserves hyphens in surnames while normalizing

### PARAMETER CORRECTION & CONSOLIDATION

71. **Parameter spelling correction** - Uses Levenshtein distance to fix typos
72. **Parameter name normalization** - Converts author1-last → last1, etc.
73. **Underscore to space** - Converts cite_book → cite book
74. **Alias consolidation** - Merges redundant parameters (e.g., work vs journal)
75. **Empty parameter removal** - Drops parameters that are blank
76. **Duplicate parameter removal** - Removes duplicate parameters keeping best version
77. **Common mistakes list** - Applies hardcoded fixes for known misspellings
78. **Author parameter flattening** - Converts author="First Last" to last/first split
79. **ID parameter parsing** - Extracts structured identifiers from unstructured "id=" field

### TEMPLATE TYPE CONVERSIONS

80. **Cite paper → cite journal** - Converts when appropriate based on content
81. **Cite document → cite journal/web/book** - Smart conversion based on parameters
82. **Cite website → cite arxiv/web** - Converts based on arXiv presence
83. **Cite book → cite journal** - Converts when DOI indicates journal article
84. **Cite arxiv → cite journal** - Converts when DOI is present and works
85. **Underscore replacement** - Converts cite book → Cite book
86. **Redirect resolution** - Follows citation template redirects to canonical forms

### PARAMETER RENAMING & MOVING

87. **work → journal** - Renames when appropriate
88. **via → work/journal** - Converts via parameter
89. **website → journal/newspaper** - Renames based on context
90. **publisher → work** - Renames for news agencies (Reuters, AP, UPI, etc.)
91. **dead-url → url-status** - Converts legacy parameter to new format
92. **deadurl → url-status** - Converts legacy parameter
93. **url → chapter-url** - Converts for book chapters
94. **chapter-url → url** - Converts when appropriate
95. **author/agency rename** - Converts Associated Press/Reuters from author to agency
96. **Number/issue swap** - Renames issue ↔ number based on journal properties

### SPECIAL FIELD OPERATIONS

97. **Et al. handling** - Removes/fixes misplaced et al. text; adds display-authors
98. **Author limit enforcement** - Caps at 30 authors; sets display-authors=1
99. **Editor limit enforcement** - Caps at 30 editors; sets display-editors=1
100. **In Press detection** - Handles "In press" volumes specially
101. **Online First handling** - Recognizes pre-publication online versions
102. **Access date handling** - Removes when no URL present; normalizes format
103. **URL status mapping** - Converts yes/no/true/false to dead/live
104. **Agency/Publisher disambiguation** - Renames between parameters based on content
105. **Book vs Journal detection** - Analyzes DOI/ISBN/content to set correct type

### ISBN OPERATIONS

106. **ISBN-10 to ISBN-13 conversion** - Converts older ISBNs to modern 13-digit format
107. **ISBN dash formatting** - Adds proper dashes (978-0-19-XXXXXX-X format)
108. **ISBN validation** - Checks for valid ISBN checksums
109. **ASIN to ISBN conversion** - Converts Amazon ASINs that are valid ISBNs
110. **Duplicate ISBN removal** - Removes when two ISBNs present

### API-SPECIFIC EXPANSIONS

#### CrossRef/DOI API

111. **CrossRef title retrieval** - Gets title from DOI metadata
112. **CrossRef authors** - Extracts author list from DOI
113. **CrossRef journal/volume/issue** - Retrieves bibliographic data
114. **CrossRef pages** - Gets page numbers from metadata

#### PubMed/Entrez API

115. **PMID-based expansion** - Retrieves complete citation data
116. **PMC-based expansion** - Gets PubMed Central metadata
117. **PubMed authors** - Extracts author list
118. **PubMed journal/volume/issue/pages** - Complete journal metadata

#### arXiv API

119. **arXiv title** - Retrieves manuscript title
120. **arXiv authors** - Gets author list from arXiv
121. **arXiv abstract** - Can extract categorization
122. **arXiv DOI linkage** - Finds published DOI for preprint
123. **arXiv journal conversion** - Upgrades to cite journal when published

#### JSTOR API

124. **JSTOR title** - Extracts from RIS data
125. **JSTOR authors** - Gets author list from JSTOR
126. **JSTOR journal/volume/issue/pages** - Complete citation data
127. **JSTOR DOI extraction** - Finds DOI from JSTOR metadata

#### Zotero/Citoid API

128. **Web URL expansion** - Extracts citation metadata from web pages
129. **Generic title extraction** - Gets title from web pages
130. **Generic metadata extraction** - Extracts authors, dates, publishers from page

#### Google Books API

131. **ISBN extraction** - Finds ISBN from Google Books
132. **Publisher extraction** - Gets publisher from book metadata
133. **Author extraction** - Gets author list from book data

#### Semantic Scholar API

134. **S2CID to DOI** - Links S2CID to publisher DOI

#### BibCode (NASA ADS) API

135. **Astronomy data** - Retrieves for astronomy/physics papers
136. **Author lists** - Gets authors from ADS
137. **Journal metadata** - Gets journal/volume/pages

### DATA QUALITY OPERATIONS

138. **Title case application** - Applies proper capitalization to titles
139. **Bad title removal** - Drops known spam/placeholder titles
140. **Redundant title detection** - Removes if equals journal/series/work
141. **Goofy title replacement** - Replaces titles like "PDF" or "WayBack"
142. **Title sanitization** - Removes markdown links and artifacts
143. **Author validation** - Checks author count; validates name patterns
144. **Year validation** - Checks for valid year ranges
145. **Volume validation** - Rejects suspicious volume numbers
146. **Issue validation** - Rejects suspicious issue numbers (like 6-digit numbers)
147. **Page range validation** - Fixes inverted ranges; validates format
148. **Date validation** - Checks dates are realistic; pre-1900 reverts to year only

### ARCHIVE/PRESERVATION OPERATIONS

149. **Wayback Machine detection** - Extracts actual URL from archive links
150. **Internet Archive handling** - Processes archive.org URLs
151. **Archive date extraction** - Parses archive snapshot dates
152. **Dead URL flagging** - Marks and dates URLs known to be dead
153. **Archive URL prioritization** - Keeps archive URLs when main URL fails

### CLEANUP & SANITATION

154. **Wikilink formatting** - Properly formats [[link]] markup
155. **External link removal** - Removes from author/title fields
156. **Placeholder removal** - Cleans bot placeholder markers
157. **Comment preservation** - Keeps <!-- --> comments intact
158. **Translit/script handling** - Manages trans-title and script-title fields
159. **Citation-bot placeholder parsing** - Handles CITATION_BOT_PLACEHOLDER_* markers
160. **Floating text detection** - Finds parameters hidden in text values
161. **Malformed parameter detection** - Catches archive=url=http style errors
162. **ISBN extraction from text** - Finds and extracts ISBNs hidden in text fields

### SPECIAL CASE HANDLING

163. **Journal-specific logic** - Oxford DNB, JSTOR, IEEE special rules
164. **DOI prefix handling** - Special processing for 10.1093/, 10.1109/, etc.
165. **Reference genome mapping** - Handles NCBI reference sequences
166. **Research gate DOI replacement** - Upgrades 10.13140/ to publisher DOI
167. **Bad DOI replacement** - Finds working DOI when current fails
168. **Conference paper detection** - Identifies from URLs/metadata
169. **Book chapter detection** - Identifies from structure
170. **Preprint handling** - Manages bioRxiv/medRxiv templates
171. **Thesis detection** - Identifies PhD/MS thesis from metadata

---

## Summary

This comprehensive list represents **171 distinct editing, expansion, and normalization operations** that Citation Bot performs on citation templates. The bot's main workflow uses `add_if_new()`, `tidy()`, and `final_tidy()` functions in `Template.php` to apply these operations systematically.

## Operating Modes

### Fast Mode (Gadget Default)
- Operations 1-162: All identifier expansion, parameter addition, and cleanup operations
- **Excludes**: Bibcode searches (function 8) and URL expansion via Zotero (functions 128-130)

### Slow Mode (Web Interface Default)  
- **All 171 operations** including bibcode searches and comprehensive URL expansion

## Source Code References

- **Main expansion logic**: `src/includes/Template.php`
- **Parameter handling**: `src/includes/Parameter.php`
- **API integrations**: `src/includes/api/*.php`
- **URL operations**: `src/includes/URLtools.php`
- **Name formatting**: `src/includes/NameTools.php`
- **Text processing**: `src/includes/TextTools.php`

## More Information

- **Documentation**: <https://en.wikipedia.org/wiki/User:Citation_bot/use>
- **Bug reports**: <https://en.wikipedia.org/wiki/User_talk:Citation_bot>
- **Source code**: <https://github.com/ms609/citation-bot>

---

**Last updated**: January 2026  
**Maintained by**: Citation Bot community
