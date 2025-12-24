# Performance Optimization Recommendations

## Overview
This document provides actionable recommendations for improving Citation Bot performance based on the performance audit conducted on 2025-12-24.

## Quick Wins (Can be implemented immediately)

### 1. Add URL Deduplication Before HTTP Requests
**File:** `src/includes/URLtools.php` (drop_urls_that_match_dois function)
**Current Issue:** May make duplicate HTTP requests for the same DOI/URL combination
**Solution:**
```php
function drop_urls_that_match_dois(array &$templates): void {
    // ... existing code ...
    
    // Collect unique DOI/URL pairs first
    $doi_url_pairs = [];
    foreach ($templates as $template) {
        $doi = $template->get_without_comments_and_placeholders('doi');
        $url = $template->get('url') ?: $template->get('chapter-url') ?: $template->get('chapterurl');
        if ($doi && $url) {
            $key = md5($doi . '|' . $url);
            if (!isset($doi_url_pairs[$key])) {
                $doi_url_pairs[$key] = ['doi' => $doi, 'url' => $url, 'templates' => []];
            }
            $doi_url_pairs[$key]['templates'][] = $template;
        }
    }
    
    // Process each unique pair only once
    foreach ($doi_url_pairs as $pair) {
        // ... make HTTP request ...
        // Apply result to all templates with this DOI/URL pair
    }
}
```
**Expected Impact:** Reduce HTTP requests by 10-30% for pages with duplicate references

### 2. Cache Negative DOI Lookup Results
**File:** `src/includes/doiTools.php`
**Current Issue:** Failed DOI lookups are retried unnecessarily
**Solution:** Add TTL-based caching for "not found" results
```php
private static array $cache_not_found = [];  // DOI => expiry_timestamp
private const NOT_FOUND_CACHE_TTL = 3600;  // 1 hour

function doi_active(string $doi): ?bool {
    // Check not-found cache with TTL
    if (isset(self::$cache_not_found[$doi])) {
        if (self::$cache_not_found[$doi] > time()) {
            return false;  // Still within TTL
        }
        unset(self::$cache_not_found[$doi]);  // Expired
    }
    // ... existing lookup code ...
    if ($result === false) {
        self::$cache_not_found[$doi] = time() + self::NOT_FOUND_CACHE_TTL;
    }
}
```
**Expected Impact:** Reduce repeated failed lookups, save 0.5-2s per page

### 3. Increase CURLOPT_BUFFERSIZE for Large Responses
**File:** `src/includes/bot_curl.php`
**Current:** 524288 (512KB)
**Recommendation:** Consider increasing to 1MB for JSON API responses
```php
CURLOPT_BUFFERSIZE => 1048576, // 1MB for better performance with large JSON
```
**Expected Impact:** 5-10% faster for large API responses

## Medium-term Improvements (Require more testing)

### 4. Implement Request Batching with curl_multi
**File:** `src/includes/URLtools.php`
**Current Issue:** Sequential HTTP requests create waterfall delays
**Solution:** Use curl_multi_* functions for parallel requests
```php
function batch_check_urls(array $urls): array {
    $mh = curl_multi_init();
    $handles = [];
    
    foreach ($urls as $key => $url) {
        $ch = bot_curl_init(1.0, [CURLOPT_URL => $url]);
        curl_multi_add_handle($mh, $ch);
        $handles[$key] = $ch;
    }
    
    // Execute all handles in parallel
    do {
        $status = curl_multi_exec($mh, $active);
        if ($active) {
            curl_multi_select($mh);
        }
    } while ($active && $status == CURLM_OK);
    
    // Collect results
    $results = [];
    foreach ($handles as $key => $ch) {
        $results[$key] = curl_multi_getcontent($ch);
        curl_multi_remove_handle($mh, $ch);
    }
    
    curl_multi_close($mh);
    return $results;
}
```
**Expected Impact:** 2-5x faster for pages with multiple DOI verifications
**Risk:** May violate rate limits if not careful; needs throttling

### 5. Implement LRU Cache Eviction
**File:** `src/includes/doiTools.php` (HandleCache class)
**Current Issue:** Cache clearing removes all items including hot data
**Solution:** Use SplDoublyLinkedList or similar for LRU tracking
```php
final class HandleCache {
    private static SplDoublyLinkedList $lru_order;
    
    public static function check_memory_use(): void {
        if ($usage > self::MAX_CACHE_SIZE) {
            // Evict 25% least recently used instead of clearing all
            $to_evict = (int) (self::MAX_CACHE_SIZE * 0.25);
            for ($i = 0; $i < $to_evict; $i++) {
                $key = self::$lru_order->shift();
                unset(self::$cache_active[$key]);
                unset(self::$cache_good[$key]);
                // ... etc
            }
        }
    }
}
```
**Expected Impact:** Maintain 80%+ cache hit rate after eviction
**Risk:** More complex to implement and maintain

### 6. Token Bucket Rate Limiting
**File:** `src/includes/api/APIzotero.php` and other API files
**Current Issue:** Fixed delays regardless of actual API capacity
**Solution:** Implement token bucket algorithm
```php
final class RateLimiter {
    private float $tokens;
    private float $max_tokens;
    private float $refill_rate;
    private float $last_refill;
    
    public function acquire(float $cost = 1.0): bool {
        $this->refill();
        if ($this->tokens >= $cost) {
            $this->tokens -= $cost;
            return true;
        }
        return false;
    }
    
    private function refill(): void {
        $now = microtime(true);
        $elapsed = $now - $this->last_refill;
        $this->tokens = min($this->max_tokens, 
                           $this->tokens + ($elapsed * $this->refill_rate));
        $this->last_refill = $now;
    }
}

// Usage:
private static $rate_limiter = new RateLimiter(10, 2.0); // 10 tokens, 2/sec
if (!self::$rate_limiter->acquire()) {
    usleep(100000);  // Wait if no tokens available
}
```
**Expected Impact:** More efficient rate limiting, up to 20% faster
**Risk:** Requires careful tuning per API

## Long-term Considerations

### 7. External Caching Layer (Redis/Memcached)
**Scope:** System-wide
**Use Case:** Multi-process or distributed bot instances
**Benefits:**
- Share cache across multiple bot processes
- Persistent cache survives process restarts
- Lower memory pressure on PHP process

**Implementation Considerations:**
- Requires infrastructure setup
- Add cache miss handling
- Implement cache warming strategies
- Monitor cache hit rates

### 8. Circuit Breaker Pattern for APIs
**Scope:** All API integration files
**Purpose:** Prevent cascading failures and wasted time on dead APIs
**Implementation:**
```php
final class CircuitBreaker {
    private const STATE_CLOSED = 0;  // Normal operation
    private const STATE_OPEN = 1;    // Failing, reject requests
    private const STATE_HALF_OPEN = 2; // Testing if recovered
    
    private int $state = self::STATE_CLOSED;
    private int $failure_count = 0;
    private float $last_failure_time = 0;
    private const THRESHOLD = 5;
    private const TIMEOUT = 60.0;
    
    public function call(callable $fn): mixed {
        if ($this->state === self::STATE_OPEN) {
            if (microtime(true) - $this->last_failure_time > self::TIMEOUT) {
                $this->state = self::STATE_HALF_OPEN;
            } else {
                throw new Exception('Circuit breaker is OPEN');
            }
        }
        
        try {
            $result = $fn();
            $this->onSuccess();
            return $result;
        } catch (Exception $e) {
            $this->onFailure();
            throw $e;
        }
    }
    
    private function onSuccess(): void {
        $this->failure_count = 0;
        $this->state = self::STATE_CLOSED;
    }
    
    private function onFailure(): void {
        $this->failure_count++;
        $this->last_failure_time = microtime(true);
        if ($this->failure_count >= self::THRESHOLD) {
            $this->state = self::STATE_OPEN;
        }
    }
}
```

### 9. Application Performance Monitoring (APM)
**Tools to Consider:**
- Blackfire.io (PHP profiling)
- Tideways (Performance monitoring)
- New Relic (Full-stack monitoring)
- DataDog (Infrastructure + APM)

**Metrics to Track:**
- Request/response times per API
- Cache hit/miss rates
- Memory usage patterns
- Error rates by type
- Template processing time distribution

## Testing Strategy

### Performance Regression Tests
**Location:** `tests/phpunit/PerformanceTest.php` (already created)
**Run Before Each Release:**
```bash
composer test -- --filter Performance
```

### Benchmarking Script
Create `tools/benchmark.php`:
```php
<?php
// Benchmark critical operations
$operations = [
    'template_parsing' => function() { /* ... */ },
    'doi_lookup' => function() { /* ... */ },
    'url_expansion' => function() { /* ... */ },
];

foreach ($operations as $name => $op) {
    $start = microtime(true);
    for ($i = 0; $i < 100; $i++) {
        $op();
    }
    $duration = microtime(true) - $start;
    echo "{$name}: " . ($duration / 100 * 1000) . "ms per operation\n";
}
```

### Load Testing
**Tool:** Apache Bench or similar
**Test Scenario:** Process 100 pages with varying complexity
**Target:** < 30 seconds for 10 templates per page average

## Monitoring Checklist

- [ ] Add timing instrumentation to critical paths
- [ ] Log cache hit rates hourly
- [ ] Track API response times
- [ ] Monitor memory usage trends
- [ ] Set up alerts for performance degradation
- [ ] Create performance dashboard

## Rollout Plan

### Phase 1 (Week 1-2): Quick Wins
- Implement URL deduplication
- Add negative result caching
- Increase curl buffer sizes
- Deploy to staging
- Monitor for issues

### Phase 2 (Week 3-4): Medium-term Improvements
- Implement request batching for safe operations
- Add LRU cache eviction
- Improve rate limiting
- A/B test in production

### Phase 3 (Month 2-3): Long-term Infrastructure
- Evaluate external caching solutions
- Implement circuit breakers
- Set up comprehensive APM
- Optimize based on production data

## Success Metrics

**Target Improvements:**
- 30% reduction in average page processing time
- 50% reduction in redundant HTTP requests
- 90%+ cache hit rate maintenance
- < 1% increase in error rates
- Memory usage stays below 256MB per process

## Documentation

All optimization changes should include:
1. Inline code comments explaining the optimization
2. Performance test validating the improvement
3. Update to this document with actual results
4. Monitoring dashboard reflecting the change

---
**Last Updated:** 2025-12-24  
**Next Review:** 2026-01-24
