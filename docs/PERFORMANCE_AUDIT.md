# Performance Audit Report for Citation Bot

**Date:** 2025-12-24  
**Auditor:** GitHub Copilot  
**Repository:** redalert2fan/citation-bot

## Executive Summary

This document provides a comprehensive performance audit of the Citation Bot codebase, focusing on:
- API call efficiency and patterns
- Looping constructs and algorithmic complexity
- Caching implementation and effectiveness
- PHP best practices adherence
- Test coverage adequacy

## 1. Performance Bottlenecks Identified

### 1.1 API Call Patterns

#### Issue 1.1.1: Sequential API Calls in Template Processing Loop
**Location:** `src/includes/URLtools.php:23-144` (`drop_urls_that_match_dois()`)
**Severity:** Medium
**Description:** The function iterates through all templates and makes sequential HTTP requests (lines 97-134) without batching or parallelization.

```php
foreach ($templates as $template) {
    // ... logic ...
    curl_setopt($ch_dx, CURLOPT_URL, "https://dx.doi.org/" . doi_encode($doi));
    $ch_return = bot_curl_exec($ch_dx);  // Sequential blocking call
    // ... more logic ...
    curl_setopt($ch_doi, CURLOPT_URL, $the_url);
    $ch_return = bot_curl_exec($ch_doi);  // Another sequential blocking call
}
```

**Impact:** For pages with multiple templates, this creates a waterfall effect where each template waits for HTTP requests to complete sequentially.

**Recommendation:**
- Collect URLs first, then batch process them
- Consider using curl_multi for parallel requests where appropriate
- Add URL deduplication to avoid redundant requests

#### Issue 1.1.2: Rate Limiting via sleep() Calls
**Location:** Multiple API files
**Severity:** Low-Medium
**Description:** The codebase uses multiple sleep/usleep calls for rate limiting:
- `APIzotero.php:149` - 0.1s delay per request
- `APIPubMed.php:190,206,354` - Multiple delays (1s, 0.1s, 0.02s)
- `APIieee.php:26,39` - 0.1s delays
- `doiTools.php:143,145,154` - 4s, 10s delays

**Impact:** These delays slow down batch processing significantly.

**Recommendation:**
- Implement token bucket algorithm for more efficient rate limiting
- Use adaptive rate limiting based on API response headers
- Consider request queuing with controlled concurrency

### 1.2 Caching Implementation

#### Finding 1.2.1: Effective Caching Strategy Present
**Location:** `src/includes/doiTools.php:5-51` (HandleCache class)
**Status:** ✅ Good Practice
**Description:** The codebase implements a comprehensive caching system for DOI lookups:

```php
final class HandleCache {
    private const MAX_CACHE_SIZE = 100000;
    public static array $cache_active = [];
    public static array $cache_inactive = BAD_DOI_ARRAY;
    public static array $cache_good = [];
    public static array $cache_hdl_loc = [];
    // ... etc
}
```

**Strengths:**
- In-memory caching with size limits
- Multiple cache buckets for different data types
- Memory monitoring with automatic cleanup
- Hash-based lookups for O(1) performance

#### Issue 1.2.2: Cache Clearing Strategy
**Location:** `doiTools.php:30-50`
**Severity:** Low
**Description:** When cache exceeds MAX_CACHE_SIZE, all caches are cleared entirely rather than using LRU eviction.

```php
if ($usage > self::MAX_CACHE_SIZE) {
    self::free_memory();  // Clears everything
}
```

**Impact:** Cache warm-up penalty after clearing.

**Recommendation:**
- Implement LRU (Least Recently Used) eviction
- Consider cache partitioning to preserve frequently accessed items

#### Finding 1.2.3: API-Level Caching
**Location:** `src/includes/api/APIBibCode.php:6-82` (AdsAbsControl class)
**Status:** ✅ Good Practice
**Description:** BibCode API implements separate caching with DOI-to-bibcode mapping.

### 1.3 Looping Constructs

#### Finding 1.3.1: No Nested Loop Issues
**Status:** ✅ Clear
**Description:** Analysis found no deeply nested loops (foreach within foreach) that would cause O(n²) or worse complexity issues.

#### Issue 1.3.2: Large File Processing
**Location:** `src/includes/Template.php` (7,021 lines)
**Severity:** Low
**Description:** The Template class is very large and contains extensive logic.

**Impact:** Harder to maintain and optimize; potential for code duplication.

**Recommendation:**
- Consider extracting specialized logic into trait classes
- Apply Single Responsibility Principle where feasible

### 1.4 Database Query Patterns

**Status:** ✅ Not Applicable
**Description:** The codebase does not use traditional database queries. It primarily interacts with:
- Wikipedia API (via WikipediaBot class)
- Various external APIs (CrossRef, PubMed, Zotero, etc.)
- File-based locking for job control

## 2. PHP Best Practices Review

### 2.1 Positive Findings ✅

1. **Strict Types**: All files use `declare(strict_types=1);`
2. **Type Hints**: Comprehensive use of parameter and return type hints
3. **Static Analysis**: Configured with PHPStan, Psalm, and Phan
4. **Code Style**: Uses PHP_CodeSniffer with MediaWiki standards
5. **Memory Management**: Explicit memory management with `gc_collect_cycles()`
6. **Time Limits**: Appropriate use of `set_time_limit()` for long operations

### 2.2 Areas for Improvement

#### Issue 2.2.1: Error Suppression with @
**Location:** Multiple files
**Severity:** Low
**Examples:**
- `bot_curl.php:52` - `@curl_exec($ch)`
- `WikipediaBot.php:150` - `@curl_exec(self::$ch_write)`

**Recommendation:** Use proper error handling instead of suppression where possible.

#### Issue 2.2.2: Static Variables in Functions
**Location:** Multiple API files
**Examples:**
- `URLtools.php:11-12` - `static $ch_dx; static $ch_doi;`
- `doiTools.php:126,770` - `static $ch = null;`

**Status:** Acceptable pattern for curl handle reuse, but document why.

## 3. Test Coverage Analysis

### 3.1 Test Suite Structure

**Test Files:** 30 PHP test files identified
**Location:** `tests/phpunit/` directory

**Test Coverage:**
- ✅ Core classes (Template, Page, Parameter)
- ✅ Name tools
- ✅ Text tools
- ✅ URL tools
- ✅ DOI tools
- ✅ API integrations (14 API test files)
- ✅ Gadget API
- ✅ Template generation

### 3.2 Test Coverage Assessment

#### Strengths ✅
1. Comprehensive API testing coverage (14 API test files)
2. Separate test files for major components
3. Tests split into logical units (TemplatePart1Test, TemplatePart2Test)
4. PHPUnit configured with coverage reporting

#### Gaps Identified

##### Gap 3.2.1: Performance Testing
**Severity:** Medium
**Description:** No dedicated performance or load tests found.

**Recommendation:**
- Add performance regression tests
- Create benchmarks for critical paths (template expansion, API calls)
- Test rate limiting behavior
- Measure cache hit rates

##### Gap 3.2.2: Integration Test Coverage
**Status:** Unclear without running tests
**Recommendation:**
- Document which tests are unit vs integration
- Ensure integration tests cover multi-template scenarios
- Test concurrent API usage patterns

##### Gap 3.2.3: Error Path Testing
**Recommendation:**
- Ensure API failure scenarios are tested
- Test cache overflow behavior
- Test rate limiting edge cases

## 4. Helper Library Usage

### 4.1 Dependencies Review

**Composer Dependencies:**
- `mediawiki/oauthclient` (2.3.0) - OAuth authentication
- PHP 8.4+ requirement

**Assessment:** ✅ Minimal external dependencies
- No heavy frameworks
- Single focused OAuth library
- Self-contained implementation

### 4.2 Internal Helper Functions

**Status:** ✅ Well-organized
- Dedicated helper files (miscTools.php, TextTools.php, NameTools.php)
- Functions grouped by domain
- No apparent over-abstraction

## 5. Priority Recommendations

### High Priority
1. **Implement request batching** for URLtools.php DOI verification
2. **Add performance regression tests** to prevent future slowdowns
3. **Document cache strategy** and tuning parameters

### Medium Priority
4. **Optimize rate limiting** with token bucket algorithm
5. **Implement LRU cache eviction** instead of full cache clearing
6. **Add performance monitoring hooks** for production metrics
7. **Create benchmark suite** for critical operations

### Low Priority
8. **Reduce error suppression** with @ operator
9. **Document static variable usage** in functions
10. **Consider Template.php refactoring** for maintainability

## 6. Performance Optimization Opportunities

### 6.1 Quick Wins
1. Add URL deduplication before making HTTP requests
2. Increase curl buffer size for large responses (already set to 512KB)
3. Add early returns to avoid unnecessary processing
4. Cache negative results (API not found) with TTL

### 6.2 Medium-term Improvements
1. Implement curl_multi for parallel API requests where safe
2. Add adaptive rate limiting based on API response times
3. Implement cache warming for frequently accessed DOIs
4. Add request coalescing for duplicate API calls

### 6.3 Long-term Considerations
1. Consider external caching layer (Redis/Memcached) for multi-process scenarios
2. Implement circuit breaker pattern for failing APIs
3. Add comprehensive APM (Application Performance Monitoring)
4. Profile memory usage patterns with xdebug

## 7. Testing Recommendations

### 7.1 Missing Test Categories
1. **Performance Tests:**
   - Template expansion time benchmarks
   - API call duration tracking
   - Cache hit rate measurement
   - Memory usage profiling

2. **Load Tests:**
   - Multi-template page processing
   - Concurrent API usage
   - Cache overflow scenarios

3. **Error Scenario Tests:**
   - API timeouts
   - Rate limiting responses
   - Malformed responses
   - Network failures

### 7.2 Test Infrastructure Improvements
1. Add performance baseline measurements
2. Create test fixtures for common scenarios
3. Mock external API calls for unit tests
4. Add integration test suite for full workflow

## 8. Metrics to Track

### 8.1 Performance Metrics
- Average page processing time
- API call duration (per API)
- Cache hit/miss rates
- Memory usage peaks
- Failed request rates

### 8.2 Quality Metrics
- Test coverage percentage (aim for >80%)
- Static analysis violations
- Security scan results
- Code complexity scores

## 9. Conclusion

The Citation Bot codebase demonstrates several **strong performance practices**:
- ✅ Comprehensive caching implementation
- ✅ Minimal external dependencies
- ✅ Strong test coverage for core functionality
- ✅ Good PHP 8.4+ type safety practices

**Key areas requiring attention:**
1. Sequential API call patterns in URL processing
2. Performance test coverage gaps
3. Cache eviction strategy refinement
4. Rate limiting optimization

The codebase is generally well-architected for performance, with the main opportunities lying in **optimizing external API interactions** and **adding performance regression testing** to prevent future slowdowns.

---

**Next Steps:**
1. Implement high-priority recommendations
2. Add performance test suite
3. Create performance monitoring dashboard
4. Document optimization guidelines for contributors
