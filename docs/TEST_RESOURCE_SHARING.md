# Test Resource Sharing and Parallel Execution Issues

## Problem Overview

Several tests in the Citation Bot test suite have been identified as potentially interfering with each other during parallel execution due to shared external API resources and timing-sensitive operations.

## Affected Tests

### Primary Tests Under Investigation

1. **TemplatePart1Test::testInPress**
   - Location: `tests/phpunit/includes/TemplatePart1Test.php:1383`
   - Uses: PMID 9858586
   - Special handling: Includes `$this->flush()` and `sleep(5)` to avoid PubMed rate limiting
   - Comment in code: "Flaky test - pubmed seems to be annoyed with us sometimes"

2. **PageTest::testUrlReferencesWithText5**
   - Location: `tests/phpunit/includes/PageTest.php:312`
   - Uses: DOI 10.1002/1097-0185(20000701)259:3<312::AID-AR80>3.0.CO;2-X
   - Includes PMID 10861364 and s2cid 9250632 (s2cid stripped in assertion)
   - Makes external API calls to Wiley online library

3. **PageTest::testVancNames7**
   - Location: `tests/phpunit/includes/PageTest.php:176`
   - Uses: PMID 28702423, PMC 5503415
   - Includes `sleep(1)` before execution

## Resource Sharing Analysis

### Shared PMID/PMC Resources

Seven tests in `PageTest` (testVancNames1 through testVancNames7) all use the **same PMID (28702423) and PMC (5503415)**:
- testVancNames1 (line 136)
- testVancNames2 (line 143) - with sleep(1)
- testVancNames3 (line 150) - with sleep(1)
- testVancNames4 (line 157) - with sleep(1)
- testVancNames5 (line 164) - with sleep(1)
- testVancNames6 (line 171) - with sleep(1)
- testVancNames7 (line 178) - with sleep(1)

All query PubMed for the article "Food Ingredients That Inhibit Cholesterol Absorption" by Jesch and Carr from 2017.

### External API Dependencies

The tests make calls to several external APIs:
1. **PubMed/PMC (Entrez)** - `https://eutils.ncbi.nlm.nih.gov/entrez/eutils/`
2. **Semantic Scholar S2** - For s2cid values
3. **CrossRef** - For DOI resolution
4. **Zotero** - For citation expansion

### Static/Global State

Several static variables are shared across tests:

```php
// From Template.php
public static array $all_templates = [];

// From APIzotero.php  
private static int $zotero_announced = 0;
private static CurlHandle $zotero_ch;
private static int $zotero_failures_count = 0;

// From WikipediaBot.php
private static ?self $last_WikipediaBot = null;
```

The `setUp()` method in `testBaseClass.php` resets some but not all of these:
```php
protected function setUp(): void {
    Zotero::create_ch_zotero();
    $wb = new WikipediaBot();
    unset($wb);
    WikipediaBot::make_ch();
    
    AdsAbsControl::small_give_up();
    AdsAbsControl::big_give_up();
    Zotero::block_zotero();
    gc_collect_cycles();
    $this->flush();
}
```

### Rate Limiting Evidence

Multiple instances of rate-limiting delays in the code:

1. **APIPubMed.php**:
   - `usleep(100000)` - Wait 1/10 second between queries
   - `usleep(20000)` - Wait 1/50 second  
   - `sleep(1)` - Wait 1 second on XML parse errors

2. **Test files**:
   - `sleep(5)` in testInPress
   - `sleep(1)` in testVancNames2-7
   - `sleep(3)` in testJunkData

## Parallel Execution Issues

### Current Configuration

From `phpunit.xml.dist`:
```xml
processIsolation="false"
```

This means tests run in the same PHP process, sharing:
- Static class variables
- Global state
- File handles and cURL connections

### Potential Race Conditions

When tests run in parallel:

1. **API Rate Limiting**: Multiple simultaneous requests to PubMed may trigger rate limits
2. **Shared PMID/PMC**: Seven tests querying same PMID/PMC simultaneously may cause:
   - Cache invalidation issues
   - API throttling
   - Race conditions in response handling
3. **Static Variable Conflicts**: `Template::$all_templates` may be modified by concurrent tests
4. **cURL Handle Reuse**: Static cURL handles may be used by multiple tests simultaneously

## Timing-Sensitive Operations

Several tests use explicit delays suggesting they are timing-sensitive:

```php
// testInPress - 5 second delay
$this->flush();
sleep(5);

// testVancNames2-7 - 1 second delays  
sleep(1);

// testJunkData - multiple attempts with 3 second delays
sleep(3);
```

These delays suggest:
1. External API calls need time to complete
2. Rate limiting requires spacing between calls
3. Test may fail if APIs respond too slowly or too quickly

## Recommendations

### Option 1: Serialize Related Tests (Recommended)

Add PHPUnit group annotations to prevent parallel execution:

```php
/**
 * @group pubmed-api
 * @group serial
 */
public function testInPress(): void { ... }

/**
 * @group pubmed-api  
 * @group serial
 */
public function testVancNames7(): void { ... }
```

Configure PHPUnit to run @serial tests sequentially.

### Option 2: Test Isolation

Enable process isolation for affected tests:

```php
/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
public function testInPress(): void { ... }
```

### Option 3: Mock External APIs

Replace actual API calls with mocked responses to eliminate:
- Rate limiting issues
- Network timing variability
- External service dependencies

### Option 4: Shared Test Fixtures

Create a test fixture that:
1. Pre-fetches common PMID/PMC data once
2. Caches results for reuse across tests
3. Reduces duplicate API calls

## Implementation Priority

1. **High Priority**: Serialize tests using same PMID/PMC (testVancNames1-7)
2. **Medium Priority**: Add @group annotations for API-dependent tests
3. **Low Priority**: Consider mocking for fully deterministic testing

## Testing Recommendations

To verify the issue:
1. Run tests individually - should pass
2. Run affected tests in parallel - may fail
3. Add verbose output to see API call timing
4. Monitor external API request rates

## References

- PHPUnit Documentation: https://docs.phpunit.de/en/10.1/annotations.html
- Test isolation: https://docs.phpunit.de/en/10.1/test-isolation.html
