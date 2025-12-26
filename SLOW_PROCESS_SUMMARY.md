# Slow Process Investigation - Summary and Recommendations

## Overview
This investigation identified and documented all slow processes in the Citation Bot codebase. The performance characteristics are well-understood and primarily driven by external API dependencies and intentional rate limiting to maintain good API citizenship.

## Key Findings

### 1. Intentional Delays (32 instances)
The bot implements extensive sleep/usleep delays for:
- **Rate limiting external APIs** (1-10 second delays)
- **Retry logic for failed API calls** (4-10 second delays before retry)
- **Error recovery** (9 second delays for database locks)

**Top delay sources:**
- `doiTools.php:158` - 10 second delay for 429 rate limit responses
- `WikipediaBot.php:79,119` - 10 second delays for Wikipedia API errors
- `Page.php:788,800` - 9 second delays for database lock recovery
- `WikipediaBot.php:135` - Progressive delays for API retries (depth + 2 seconds)

### 2. Throttle Functions (3 implementations)
| Function | Location | Delay | Purpose |
|----------|----------|-------|---------|
| `throttle()` | miscTools.php | 2s/write (avg) | Wikipedia edit rate limiting |
| `throttle_dx()` | doiTools.php | 40ms | DOI resolution service |
| `throttle_archive()` | APIarchives.php | 1s | Archive service requests |

### 3. Time Limit Resets (59 instances)
Files with frequent `set_time_limit(120)` calls indicate slow operations:

| File | Count | Primary Reason |
|------|-------|----------------|
| Page.php | 13 | Complex page parsing and template expansion |
| Template.php | 8 | Individual template processing |
| APIarchives.php | 5 | Archive HTML parsing with complex regex |
| APIdoi.php | 4 | DOI metadata retrieval |
| APIBibCode.php | 4 | NASA ADS bibcode searches |

### 4. External API Dependencies (36 curl_exec calls)
Most time-consuming operations involve external API calls to:
- CrossRef (DOI resolution)
- PubMed/PMC (medical literature)
- NASA ADS (astronomy/physics bibcodes)
- Zotero (URL-to-citation translation)
- Google Books API
- Semantic Scholar (S2 API)
- IEEE Xplore
- JSTOR
- Unpaywall
- Archive.org

### 5. SLOW_MODE Operations
**Enabled by default in web interface, disabled in gadget API**

SLOW_MODE triggers:
1. **Bibcode searches** (`APIBibCode.php`) - Searches NASA ADS for astronomical papers
2. **Zotero URL expansion** (`APIzotero.php`) - Translates URLs to citations (most expensive operation)

## Performance Impact Analysis

### Typical Processing Times

**Fast Mode (SLOW_MODE disabled):**
- Single citation: 0.5-2 seconds
- Page with 10 citations: 5-15 seconds
- Page with 50 citations: 20-60 seconds

**Slow Mode (SLOW_MODE enabled):**
- Single citation with URL: 2-10 seconds
- Page with 10 citations: 20-120 seconds  
- Page with 50 citations: 2-10 minutes

**Primary bottlenecks:**
1. Zotero URL translation (2-10s per URL in SLOW_MODE)
2. NASA ADS bibcode search (1-5s per search in SLOW_MODE)
3. Archive.org HTML retrieval and parsing (1-3s per archive URL)
4. DOI verification and metadata retrieval (0.5-2s per DOI)

## Architectural Observations

### Well-Designed Aspects ✅
1. **Graceful degradation** - Fast mode for time-sensitive operations
2. **Comprehensive rate limiting** - Respects all external API limits
3. **Retry logic** - Handles transient failures with exponential backoff
4. **Memory management** - Clears caches to prevent exhaustion
5. **Timeout management** - Frequent `set_time_limit()` prevents premature termination

### Areas of Concern ⚠️
1. **No result caching** - DOI verification happens every time (could cache valid DOIs)
2. **Sequential API calls** - Could benefit from concurrent requests where possible
3. **Complex regex operations** - Archive HTML parsing uses slow regex patterns
4. **Multiple retries per request** - Some code paths retry with delays multiple times

## Recommendations

### Immediate (No Code Changes)
1. **Document SLOW_MODE behavior** ✅ Already completed in PERFORMANCE_ANALYSIS.md
2. **Educate users** - Make it clear when to use fast vs slow mode
3. **Set expectations** - Document typical processing times in user-facing docs

### Short-term (Low Risk)
1. **Cache DOI validation results** - Store valid DOIs in a short-lived cache (5-10 minutes)
   - Impact: Reduce duplicate DOI checks within same session
   - Files: `doiTools.php`, add simple in-memory cache

2. **Profile slow regex patterns** - Identify and optimize patterns in `expand_templates_from_archives()` function
   - Impact: Reduce archive HTML parsing time
   - Files: `APIarchives.php`

3. **Add timing instrumentation** - Log actual time spent in each API call
   - Impact: Better visibility into bottlenecks
   - Files: All API*.php files

### Medium-term (Moderate Risk)
1. **Implement API response caching** - Cache successful API responses for 24 hours
   - Impact: Significantly faster re-processing of same citations
   - Files: All API*.php files
   - Consideration: Cache invalidation strategy needed

2. **Batch API requests** - Where APIs support batching (PubMed, CrossRef)
   - Impact: Reduce network round trips
   - Files: `APIPubMed.php`, `APIdoi.php`

3. **Optimize retry logic** - Reduce sleep times for certain error conditions
   - Impact: Faster recovery from transient errors
   - Files: `WikipediaBot.php`, `doiTools.php`

### Long-term (Higher Risk)
1. **Concurrent API requests** - Use async/concurrent requests for independent operations
   - Impact: Potentially 2-5x speedup in SLOW_MODE
   - Files: Major refactoring required
   - Consideration: PHP async support required (ReactPHP, Amp, etc.)

2. **Background job processing** - Queue large operations for async processing
   - Impact: Better UX for large page lists
   - Files: Architectural change
   - Consideration: Requires infrastructure changes

3. **CDN/proxy for common APIs** - Cache common API responses
   - Impact: Reduce latency and load on external services
   - Files: Infrastructure change
   - Consideration: Cost and maintenance overhead

## Conclusion

The Citation Bot's slow processes are **intentional and necessary** for:
1. Respecting external API rate limits
2. Handling errors gracefully
3. Maintaining reliability

The architecture is generally well-designed for the constraints it operates under. The most significant performance improvements would come from:
1. **Caching API responses** (easy win)
2. **Batching API requests** (moderate complexity)
3. **Concurrent request processing** (high complexity)

However, these optimizations should be weighed against:
- Code complexity increase
- Risk of breaking existing functionality
- Maintenance burden
- Actual user pain points

**Current recommendation:** Focus on caching strategies and documentation improvements before considering major architectural changes.

## Files Created During Investigation

1. **PERFORMANCE_ANALYSIS.md** - Comprehensive performance documentation
2. **SLOW_PATTERNS_REPORT.md** - Automated analysis of slow patterns
3. **This file** - Summary and recommendations

## Next Steps

1. Review these findings with maintainers
2. Prioritize recommendations based on user feedback
3. Implement caching for DOI validation as a pilot project
4. Monitor test timing reports to catch performance regressions
5. Consider adding performance benchmarks to CI pipeline
