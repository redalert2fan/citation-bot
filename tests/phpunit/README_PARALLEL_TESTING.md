# Parallel Testing Considerations

## Overview

This document describes the test group annotations used in the Citation Bot test suite to manage parallel execution and prevent resource conflicts.

## Test Groups

The following PHPUnit test groups have been defined:

### External API Groups

- **`@group external-api`** - Tests that make calls to external APIs
- **`@group pubmed-api`** - Tests specifically using PubMed/PMC Entrez API
- **`@group doi-api`** - Tests that resolve DOIs via CrossRef or other services

### Resource Sharing Groups

- **`@group shared-pmid-28702423`** - Tests that all query the same PMID/PMC (28702423/5503415)
  - Includes: `testVancNames1` through `testVancNames7`
  - These tests should be run sequentially to avoid PubMed rate limiting

## Known Issues with Parallel Execution

### Tests That May Fail in Parallel

1. **TemplatePart1Test::testInPress**
   - Uses: PMID 9858586
   - Has 5-second sleep to avoid rate limiting
   - Groups: `@group pubmed-api`, `@group external-api`

2. **PageTest::testVancNames1-7**
   - All use the same PMID 28702423 and PMC 5503415
   - Each has 1-second sleep between executions
   - Groups: `@group pubmed-api`, `@group external-api`, `@group shared-pmid-28702423`

3. **PageTest::testUrlReferencesWithText5**
   - Makes DOI resolution calls
   - Groups: `@group external-api`, `@group doi-api`

### Rate Limiting

External APIs have rate limits:
- **PubMed**: Multiple rapid requests may trigger throttling
- **CrossRef**: DOI resolution has usage limits
- **Semantic Scholar**: S2CID lookups may be rate-limited

## Running Tests

### Run All Tests (Default)
```bash
vendor/bin/phpunit
```

### Run Tests Excluding External APIs
```bash
vendor/bin/phpunit --exclude-group external-api
```

### Run Only Tests with Shared Resources
```bash
vendor/bin/phpunit --group shared-pmid-28702423
```

### Run Tests Serially (No Parallelization)
```bash
vendor/bin/phpunit --no-parallel
```

## Adding New Tests

When adding new tests that use external APIs:

1. **Add appropriate group annotations:**
   ```php
   /**
    * @group external-api
    * @group pubmed-api  // if using PubMed
    */
   public function testMyNewTest(): void {
       // test code
   }
   ```

2. **If multiple tests share the same resource:**
   ```php
   /**
    * @group external-api
    * @group shared-pmid-XXXXXXX  // use actual PMID
    */
   ```

3. **Consider adding delays:**
   - Use `sleep(1)` between tests using the same resource
   - Use `$this->flush()` to clear output buffers

## Debugging Parallel Test Failures

If tests pass individually but fail in parallel:

1. Check if they share external API resources
2. Look for static variable conflicts
3. Check for rate limiting in API responses
4. Add appropriate group annotations
5. Consider adding sleep delays

## Further Reading

- Full analysis: [docs/TEST_RESOURCE_SHARING.md](../../docs/TEST_RESOURCE_SHARING.md)
- PHPUnit Groups: https://docs.phpunit.de/en/10.1/annotations.html#group
- Test Isolation: https://docs.phpunit.de/en/10.1/test-isolation.html
