# UTF-8 Encoding Investigation Report

## Summary

This report documents the investigation of UTF-8 encoding errors in the translation system of the Citation Bot repository.

## Investigation Scope

The investigation focused on:
1. Translation constants for multi-language support (Macedonian, Russian, Serbian, Vietnamese)
2. UTF-8 encoding of translation files
3. HTML output of translated error messages
4. String encoding conversion functions

## Findings

### 1. Translation File Encoding ✅

**File:** `src/includes/constants/translations.php`

- ✅ File is properly encoded as UTF-8 without BOM
- ✅ Contains Cyrillic characters (Macedonian, Russian, Serbian) and Vietnamese diacritics
- ✅ All translation constants are properly defined
- ✅ String replacement in `edit_summary()` method works correctly

**Verification:**
```bash
$ file src/includes/constants/translations.php
src/includes/constants/translations.php: PHP script, Unicode text, UTF-8 text

$ head -c 3 src/includes/constants/translations.php | xxd
00000000: 3c3f 70                                  <?p
# No UTF-8 BOM (EF BB BF) present - correct!
```

### 2. Translation Constants

The following translation constants are defined:
- `MK_ERR1`, `MK_ERR2`, `MK_TRANS` - Macedonian
- `RU_ERR1`, `RU_ERR2`, `RU_TRANS` - Russian  
- `SR_ERR1`, `SR_ERR2`, `SR_TRANS` - Serbian
- `VI_ERR1`, `VI_ERR2`, `VI_TRANS` - Vietnamese
- `ENG_ERR1`, `ENG_ERR2` - English

All constants properly contain multi-byte UTF-8 characters for non-Latin scripts.

### 3. Issue Identified ⚠️

**File:** `src/includes/Page.php`, line 950

**Problem:** Error messages were output directly without proper HTML escaping:
```php
echo '<p><h3>', $err1, '</h3><h4>', $err2, '</h4></p><p>', echoable($text), '</p>';
```

The `$err1` and `$err2` variables (containing UTF-8 Cyrillic/Vietnamese text) were not wrapped with the `echoable()` function, which is used throughout the codebase for safe HTML output.

### 4. Fix Applied ✅

**Change:** Wrapped error message variables with `echoable()` for consistent HTML encoding:
```php
echo '<p><h3>', echoable($err1), '</h3><h4>', echoable($err2), '</h4></p><p>', echoable($text), '</p>';
```

The `echoable()` function (in `src/includes/user_messages.php`) applies `htmlspecialchars()` when `HTML_OUTPUT` is true, ensuring proper UTF-8 HTML entity encoding.

### 5. UTF-8 Conversion Functions ✅

**File:** `src/includes/api/APIarchives.php`

The codebase includes robust UTF-8 conversion functions:
- `convert_to_utf8()` - Main conversion function with heuristics
- `convert_to_utf8_inside()` - Multi-pass encoding detection
- `smart_decode()` - Handles various encoding edge cases

These functions properly detect and convert from various encodings (ISO-2022-JP, EUC-CN, EUC-KR, Windows-1252, ISO-8859-1) to UTF-8.

### 6. Test Coverage ✅

**Existing tests:** `tests/phpunit/includes/textToolsTest.php`
- ✅ Tests UTF-8 conversion for Japanese (ISO-2022-JP)
- ✅ Tests UTF-8 conversion for Chinese (EUC-CN)
- ✅ Tests UTF-8 conversion for Korean (EUC-KR)
- ✅ Tests Windows-1252 to UTF-8 conversion

**New tests:** `tests/phpunit/includes/TranslationsTest.php`
- ✅ Verifies all translation constants contain proper UTF-8 multi-byte characters
- ✅ Tests translation string replacement functionality
- ✅ Validates all error messages are defined and not empty
- ✅ Ensures translation arrays have proper string keys and values

## Recommendations

### ✅ Already Implemented
1. Fixed HTML output of error messages to use `echoable()`
2. Added comprehensive test suite for translation UTF-8 encoding
3. Verified existing UTF-8 conversion functions are working correctly

### Future Considerations
1. Consider adding Content-Type headers at the PHP level when HTML output begins
2. Add integration tests that verify translation display in different wiki contexts
3. Document the translation system in the codebase documentation

## Testing Results

All manual tests passed:
- ✅ Translation constants loaded correctly
- ✅ Multi-byte character detection works
- ✅ String replacement produces correct output for all languages
- ✅ PHP syntax validation passes for all modified files

## Files Modified

1. `src/includes/Page.php` - Fixed error message HTML output
2. `tests/phpunit/includes/TranslationsTest.php` - New test suite (132 lines)

## Conclusion

The investigation revealed that UTF-8 encoding was generally well-handled throughout the codebase. The only issue found was the direct output of translation error messages without proper HTML escaping. This has been corrected by using the existing `echoable()` function, which is the standard approach used throughout the codebase.

The translation system is now properly tested and validated for UTF-8 correctness.

---

**Date:** 2026-01-23  
**Author:** GitHub Copilot Agent  
**Status:** Complete ✅
