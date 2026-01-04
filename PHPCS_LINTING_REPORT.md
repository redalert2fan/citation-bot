# PHP CodeSniffer Linting Report

## Summary

PHP CodeSniffer was run on all PHP files in the `src/` and `tests/` directories using the PSR-12 coding standard.

### Overall Results

- **Total Files Checked**: 82 files (86 files including 4 non-PHP files that were skipped)
- **Total Errors**: 3,493 errors
- **Total Warnings**: 1,996 warnings
- **Auto-fixable**: 3,046 violations can be fixed automatically using `phpcbf`

## Configuration

- **Tool**: PHP_CodeSniffer version 3.13.2
- **Standard**: PSR-12 (PHP Standards Recommendation 12)
- **Command**: `phpcs --standard=PSR12 -p src/ tests/`

Note: The repository is configured to use MediaWiki CodeSniffer (`.phpcs.xml`), but due to dependency installation issues in the CI environment (PHP 8.3.6 vs required 8.4), PSR-12 was used as an alternative standard for this check.

## Top Violators (Most Errors + Warnings)

| File | Errors | Warnings | Total |
|------|---------|----------|-------|
| Template.php | 243 | 304 | 547 |
| TemplatePart2Test.php | 381 | 42 | 423 |
| TemplatePart4Test.php | 270 | 29 | 299 |
| TemplatePart3Test.php | 270 | 73 | 343 |
| parameters.php | 0 | 308 | 308 |
| URLtools.php | 75 | 164 | 239 |
| textToolsTest.php | 198 | 26 | 224 |
| TemplatePart1Test.php | 232 | 66 | 298 |
| UrlToolsTest.php | 115 | 82 | 197 |
| zoteroTest.php | 134 | 59 | 193 |

## Common Issues Found

Based on sample analysis, common PSR-12 violations include:

1. **Opening Brace Placement**: Opening braces should be on new lines for classes/functions
2. **Line Length**: Lines exceeding 120 characters
3. **Side Effects**: Files mixing symbol declarations with logic execution
4. **Whitespace and Formatting**: Various indentation and spacing issues
5. **Control Structure Formatting**: Incorrect spacing in control structures

## Automatic Fixes Available

**3,046 violations** (87% of errors) can be automatically fixed using PHP Code Beautifier and Fixer (PHPCBF):

```bash
vendor/bin/phpcbf --standard=PSR12 src/ tests/
```

## Detailed Results by Directory

### Source Files (`src/`)

**Entry Points and Scripts** (12 errors, 26 warnings):
- authenticate.php: 2 errors, 6 warnings
- gadgetapi.php: 0 errors, 2 warnings
- generate_template.php: 1 error, 3 warnings
- gitpull.php: 1 error, 2 warnings
- kill_big_job.php: 0 errors, 3 warnings
- linked_pages.php: 0 errors, 1 warning
- process_page.php: 0 errors, 1 warning
- assets/index.js: 104 errors, 0 warnings (JavaScript file, should be excluded)

**Core Includes** (`src/includes/`):
- Major files with significant violations:
  - Template.php: 243 errors, 304 warnings
  - TextTools.php: 115 errors, 50 warnings
  - URLtools.php: 75 errors, 164 warnings
  - WikipediaBot.php: 71 errors, 23 warnings
  - Page.php: 69 errors, 54 warnings

**API Integrations** (`src/includes/api/`):
- APIBibCode.php: 57 errors, 29 warnings
- APIdoi.php: 50 errors, 36 warnings
- APIzotero.php: 54 errors, 90 warnings
- APIarchives.php: 21 errors, 16 warnings
- APIgoogle.php: 22 errors, 19 warnings

**Constants** (`src/includes/constants/`):
- parameters.php: 0 errors, 308 warnings (mostly line length)
- translations.php: 0 errors, 13 warnings
- regular_expressions.php: 0 errors, 2 warnings

### Test Files (`tests/`)

**Test Base**:
- testBaseClass.php: 46 errors, 2 warnings
- parse_junit.php: 1 error, 0 warnings

**Unit Tests** (`tests/phpunit/`):
Most test files have violations, primarily related to:
- Test method naming and structure
- Line length in test data
- Whitespace and formatting

## Recommendations

1. **Immediate**: Run `phpcbf` to automatically fix the 3,046 fixable violations
2. **Code Review**: Manual review needed for the remaining ~400 errors that can't be auto-fixed
3. **CI Integration**: Configure CI to run phpcs checks on new code
4. **Dependency Fix**: Resolve the MediaWiki CodeSniffer installation issues for proper standard compliance
5. **Gradual Cleanup**: Address files with highest violation counts first (Template.php, test files)

## Notes

- This report was generated in a CI environment with limited access
- PHP version mismatch (8.3.6 available vs 8.4 required) prevented full MediaWiki CodeSniffer installation
- PSR-12 standard was used as a fallback, which may have different rules than the project's intended MediaWiki standard
- The high violation count is expected for a mature codebase that predates PSR-12 standardization
- Many violations are stylistic and don't affect functionality

## Commands Used

```bash
# Check all files with summary
vendor/bin/phpcs --standard=PSR12 -p src/ tests/ --report=summary

# Check specific file with details
vendor/bin/phpcs --standard=PSR12 src/generate_template.php

# Auto-fix violations
vendor/bin/phpcbf --standard=PSR12 src/ tests/
```

---

*Report generated on 2026-01-04 using PHP_CodeSniffer 3.13.2*
