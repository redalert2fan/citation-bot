# Citation Bot Performance Analysis

## Executive Summary

This document provides a comprehensive analysis of performance-related aspects of the Citation Bot, including intentional throttling mechanisms, slow-mode operations, and areas that require extended execution time.

## 1. Throttling Mechanisms

The Citation Bot implements three distinct throttling functions to control the rate of external API requests and prevent service disruption:

### 1.1 General Write Throttle (`throttle()`)

**Location:** `src/includes/miscTools.php:208-250`

**Purpose:** Rate-limits writes to Wikipedia to avoid overloading the API

**Behavior:**
- Checks every 20 writes (cycles)
- Maintains minimum 40 seconds between batches (2 seconds per write on average)
- Monitors and clears memory if usage exceeds 30% of PHP memory limit
- Called by `Page::write()` before submitting edits to Wikipedia

**Impact:**
- Ensures responsible Wikipedia API usage
- Prevents bot from being blocked for excessive requests
- Critical for multi-page operations

### 1.2 DOI Resolution Throttle (`throttle_dx()`)

**Location:** `src/includes/doiTools.php:178-187`

**Purpose:** Rate-limits DOI resolution checks via dx.doi.org

**Behavior:**
- Minimum 40ms (40,000 microseconds) between calls
- Uses `usleep()` for sub-second precision
- Called by `is_doi_works()` before checking DOI validity

**Impact:**
- Minimal per-request delay (~40ms)
- Prevents overwhelming DOI resolution service
- Can accumulate when processing many citations

### 1.3 Archive URL Throttle (`throttle_archive()`)

**Location:** `src/includes/api/APIarchives.php:5-14`

**Purpose:** Rate-limits requests to archive services (Wayback Machine, Ghostarchive, etc.)

**Behavior:**
- Minimum 1 second (1,000,000 microseconds) between calls
- Uses `usleep()` for precise timing
- Called by `expand_templates_from_archives()` when extracting titles from archive URLs

**Impact:**
- 1 second delay per archive URL processed
- Most significant throttle delay
- Only affects citations with archive URLs and problematic titles

## 2. SLOW_MODE Operations

**Configuration:** Set via `--slow` command-line flag or `slow=1` request parameter

**Default Behavior:**
- **Web Interface:** SLOW_MODE enabled by default (checked checkbox)
- **Gadget API:** SLOW_MODE always disabled (fast mode only)
- **Command Line:** Disabled unless `--slow` flag specified

### 2.1 Operations ONLY in SLOW_MODE

#### Bibcode Search (`APIBibCode.php:130`)
```php
if (!SLOW_MODE && $template->blank('bibcode')) {
    return;  // Only look for new bibcodes in slow mode
}
```

**Impact:** Searches NASA ADS for bibcodes for astronomical/physics papers. External API calls can be slow.

#### Zotero URL Expansion (`APIzotero.php:79`)
```php
if (!SLOW_MODE) {
    return; // Skip URL expansion
}
```

**Operations Skipped in Fast Mode:**
- URL to citation metadata translation via Zotero
- HDL (Handle System) identifier expansion
- OSTI (Office of Scientific and Technical Information) expansion
- RFC (Request for Comments) document expansion
- SSRN (Social Science Research Network) expansion
- CiteSeerX citation expansion

**Impact:** Zotero translation service can take several seconds per URL. Most significant slow-mode operation.

### 2.2 Why SLOW_MODE Matters

**Performance Difference:**
- Fast Mode: Typically completes in seconds
- Slow Mode: Can take minutes for pages with many URLs

**Browser Timeout Risk:**
- Gadget uses fast mode to avoid browser connection timeouts
- Web interface can handle longer processing times
- Command-line has no timeout restrictions

## 3. Execution Time Limits

The bot uses `set_time_limit(120)` extensively (found 59 times in codebase) to prevent PHP script timeout during:

### 3.1 High-Frequency Time Limit Resets
- **Template expansion** (8 locations in Template.php)
- **Page operations** (13 locations in Page.php)
- **All API functions** (38+ locations across API*.php files)
- **Archive processing** (5 locations in APIarchives.php)

**Strategy:** Reset time limit frequently during long operations to ensure completion

### 3.2 Particularly Slow Operations
Explicitly marked with comments:

```php
set_time_limit(120); // This can be slow
```
Found in:
- `APIzotero.php` (`expand_by_zotero()` function) - Zotero translation service calls
- `APIarchives.php` (`expand_templates_from_archives()` function) - Complex regex operations on archive HTML

## 4. Test Performance Infrastructure

### 4.1 Test Timing Collection

**Tool:** `tests/parse_junit.php`

**Purpose:** Parse JUnit XML test results and display execution times

**Features:**
- Sorts tests by duration (slowest first)
- Shows pass/fail status for each test
- Calculates total test suite execution time
- Helps identify performance regressions

### 4.2 Parallel Test Execution

**Configuration:** `composer.json:39`

```json
"test": "paratest --processes=auto --runner=WrapperRunner --enforce-time-limit --default-time-limit=60000 ..."
```

**Benefits:**
- Uses all available CPU cores
- 2-4x speedup over sequential execution
- Required because PHPUnit 12 removed native parallel support
- 60-second default timeout per test (--default-time-limit=60000ms)

## 5. Memory Management

### 5.1 Memory Monitoring

**Function:** `check_memory_usage()` in `miscTools.php:38-47`

**Thresholds:**
- Reports usage over 24MB
- Reports peak usage over 128MB

### 5.2 Memory Clearing

**Function:** `throttle()` includes memory management

**Behavior:**
- Checks every 20 writes
- If memory exceeds 30% of PHP limit:
  - Clears HandleCache
  - Clears AdsAbsControl cache
  - Logs memory reduction

**Purpose:** Prevent memory exhaustion during long-running multi-page operations

## 6. Known Slow Patterns

### 6.1 Complex Regex on Large HTML

**Location:** `APIarchives.php` in the `expand_templates_from_archives()` function

Multiple complex regex patterns applied to full archive page HTML:
```php
set_time_limit(120); // Slow regex sometimes
```

### 6.2 External API Dependencies

Ranked by typical response time:

1. **Zotero Translation** - 2-10 seconds per URL (SLOW_MODE only)
2. **NASA ADS Bibcode Search** - 1-5 seconds per search (SLOW_MODE only)
3. **Archive.org Access** - 1-3 seconds per request (with 1s throttle)
4. **CrossRef DOI Resolution** - 0.5-2 seconds per DOI
5. **PubMed/PMC API** - 0.3-1 second per request
6. **Google Books API** - 0.5-2 seconds per request

## 7. Optimization Opportunities

### 7.1 Already Implemented
✅ Parallel test execution with ParaTest
✅ Memory cache clearing to prevent exhaustion
✅ Rate limiting to maintain good API citizenship
✅ Fast mode for browser-based operations
✅ Conditional SLOW_MODE operations

### 7.2 Potential Improvements
⚠️ Consider caching DOI resolution results (currently checks each time)
⚠️ Batch API requests where services support it
⚠️ Profile and optimize complex regex patterns in archive processing
⚠️ Consider async/concurrent API requests for independent operations

## 8. Testing Performance

To generate test timing reports:

```bash
composer run test
```

This will:
1. Run tests in parallel using all CPU cores
2. Generate `junit.xml` with timing data
3. Display sorted test timings via `parse_junit.php`

To identify slow tests, look for:
- Tests taking >5 seconds
- Tests with external API calls not properly mocked
- Tests with expensive operations in loops

## 9. Recommendations

### For Users
1. **Use Fast Mode** when quick results are needed and URL expansion is not critical
2. **Use Slow Mode** for comprehensive citation expansion
3. **Process pages individually** rather than in large batches to avoid timeouts

### For Developers
1. **Always mock external APIs** in tests to maintain fast test suite
2. **Reset time limits** (`set_time_limit(120)`) in long-running functions
3. **Monitor memory usage** in functions processing many templates
4. **Test both fast and slow modes** when modifying expansion logic
5. **Run test suite locally** before pushing to identify performance regressions

## 10. Conclusion

The Citation Bot's performance characteristics are primarily driven by:
1. **External API dependencies** - Most significant factor
2. **Intentional throttling** - Necessary for API citizenship
3. **SLOW_MODE vs Fast Mode** - User-controlled speed/thoroughness tradeoff

The current architecture appropriately balances:
- Thoroughness vs speed
- API rate limits vs performance
- Memory usage vs caching
- User experience vs comprehensiveness

The test infrastructure provides good visibility into performance, and the parallel test execution keeps development velocity high despite a large test suite.
