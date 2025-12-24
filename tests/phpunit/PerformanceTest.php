<?php

declare(strict_types=1);

/*
 * Performance regression tests for Citation Bot
 * These tests measure execution time and resource usage for critical operations
 */

require_once __DIR__ . '/../testBaseClass.php';

final class PerformanceTest extends testBaseClass
{
    /**
     * Maximum acceptable time for expanding a simple template (in seconds)
     */
    private const MAX_SIMPLE_TEMPLATE_TIME = 5.0;

    /**
     * Maximum acceptable time for DOI lookup (in seconds)
     */
    private const MAX_DOI_LOOKUP_TIME = 3.0;

    /**
     * Test that simple template expansion completes within acceptable time
     */
    public function testSimpleTemplateExpansionPerformance(): void {
        $start = microtime(true);

        $text = '{{cite journal|doi=10.1038/nature}}';
        $expanded = $this->process_citation($text);

        $duration = microtime(true) - $start;

        $this->assertNotNull($expanded);
        $this->assertLessThan(
            self::MAX_SIMPLE_TEMPLATE_TIME,
            $duration,
            "Simple template expansion took {$duration}s, expected < " . self::MAX_SIMPLE_TEMPLATE_TIME . "s"
        );
    }

    /**
     * Test that DOI lookup with caching is faster than without
     */
    public function testDOICachingImprovement(): void {
        $doi = '10.1038/nature12373';

        // First call - cache miss
        $start1 = microtime(true);
        $result1 = doi_active($doi);
        $duration1 = microtime(true) - $start1;

        // Second call - should hit cache
        $start2 = microtime(true);
        $result2 = doi_active($doi);
        $duration2 = microtime(true) - $start2;

        $this->assertSame($result1, $result2, 'Cached result should match original');
        $this->assertLessThan(
            $duration1 / 2,
            $duration2,
            "Cached lookup ({$duration2}s) should be significantly faster than initial lookup ({$duration1}s)"
        );
        $this->assertLessThan(
            0.001,
            $duration2,
            "Cached DOI lookup should complete in < 1ms, took {$duration2}s"
        );
    }

    /**
     * Test memory usage during template processing
     */
    public function testMemoryUsageDuringProcessing(): void {
        $initialMemory = memory_get_usage(true);

        // Process a template with moderate complexity
        $text = '{{cite journal|doi=10.1038/nature|title=Test Article|author=Smith, J.|year=2020}}';
        $this->process_citation($text);

        $finalMemory = memory_get_usage(true);
        $memoryIncrease = $finalMemory - $initialMemory;

        // Memory increase should be reasonable (< 10MB for a single template)
        $this->assertLessThan(
            10 * 1024 * 1024,
            $memoryIncrease,
            "Memory increase of " . ($memoryIncrease / 1024 / 1024) . "MB is too high for single template"
        );
    }

    /**
     * Test that HandleCache properly limits memory growth
     */
    public function testHandleCacheMemoryLimit(): void {
        // Fill cache with many items
        for ($i = 0; $i < 1000; $i++) {
            HandleCache::$cache_active["10.1234/test{$i}"] = true;
        }

        $itemCount = count(HandleCache::$cache_active);
        HandleCache::check_memory_use();

        // Cache should still have items after check (not cleared)
        $this->assertGreaterThan(0, count(HandleCache::$cache_active));

        // If we're near the limit, verify the cache can be cleared
        if ($itemCount > 50000) {
            HandleCache::free_memory();
            $this->assertCount(
                0,
                HandleCache::$cache_active,
                'free_memory() should clear the cache'
            );
        }
    }

    /**
     * Benchmark: Measure template parsing performance
     */
    public function testTemplateParsingBenchmark(): void {
        $templates = [
            '{{cite journal|doi=10.1038/nature}}',
            '{{cite book|isbn=978-0-123456-78-9|title=Test Book}}',
            '{{cite web|url=https://example.com|title=Example}}',
            '{{citation|pmid=12345678|title=Medical Article}}',
        ];

        $totalTime = 0;
        foreach ($templates as $text) {
            $start = microtime(true);
            $template = $this->make_citation($text);
            $template->parse_text($text);
            $totalTime += microtime(true) - $start;
        }

        $avgTime = $totalTime / count($templates);

        $this->assertLessThan(
            0.1,
            $avgTime,
            "Average template parsing time ({$avgTime}s) exceeds 0.1s threshold"
        );

        // Report benchmark result
        if (getenv('REPORT_BENCHMARKS') === 'true') {
            echo "\n[BENCHMARK] Average template parsing: " . round($avgTime * 1000, 2) . "ms\n";
        }
    }

    /**
     * Test that URL simplification is performant
     */
    public function testURLSimplificationPerformance(): void {
        $complexUrl = 'https://www.example.com/article?utm_source=test&utm_medium=email&utm_campaign=test123&tracking_id=xyz&session_id=abc&' .
                     'fbclid=IwAR1234567890&gclid=CjwKCAiA&msclkid=test&extra=param&another=param';

        $iterations = 100;
        $start = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $simplified = url_simplify($complexUrl);
        }

        $duration = microtime(true) - $start;
        $avgTime = $duration / $iterations;

        $this->assertLessThan(
            0.01,
            $avgTime,
            "URL simplification averaging {$avgTime}s per call is too slow"
        );
    }

    /**
     * Performance test for handling multiple templates on a page
     */
    public function testMultipleTemplatesPerformance(): void {
        $page_text = '';
        for ($i = 0; $i < 10; $i++) {
            $page_text .= "{{cite journal|doi=10.1038/nature{$i}|title=Article {$i}}}\n\n";
        }

        $start = microtime(true);
        $page = $this->process_page($page_text);
        $duration = microtime(true) - $start;

        $this->assertNotNull($page);
        $this->assertLessThan(
            30.0,
            $duration,
            "Processing 10 templates took {$duration}s, expected < 30s"
        );

        if (getenv('REPORT_BENCHMARKS') === 'true') {
            echo "\n[BENCHMARK] 10 templates processed in: " . round($duration, 2) . "s\n";
        }
    }

    /**
     * Test that cache hit rate is reasonable
     */
    public function testCacheHitRate(): void {
        $doi = '10.1038/test123';

        // Prime the cache
        doi_active($doi);

        $hits = 0;
        $attempts = 100;

        $start = microtime(true);
        for ($i = 0; $i < $attempts; $i++) {
            $startLookup = microtime(true);
            doi_active($doi);
            $lookupTime = microtime(true) - $startLookup;

            // If lookup is very fast (< 1ms), it likely hit cache
            if ($lookupTime < 0.001) {
                $hits++;
            }
        }
        $totalTime = microtime(true) - $start;

        $hitRate = ($hits / $attempts) * 100;

        $this->assertGreaterThan(
            90,
            $hitRate,
            "Cache hit rate of {$hitRate}% is below 90% threshold"
        );

        if (getenv('REPORT_BENCHMARKS') === 'true') {
            echo "\n[BENCHMARK] Cache hit rate: {$hitRate}% ({$hits}/{$attempts})\n";
            echo "[BENCHMARK] Total time for {$attempts} cached lookups: " . round($totalTime * 1000, 2) . "ms\n";
        }
    }
}
