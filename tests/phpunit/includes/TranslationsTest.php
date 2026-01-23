<?php

declare(strict_types=1);

namespace tests\phpunit\includes;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/includes/constants/translations.php';

/**
 * Tests for UTF-8 encoding in translation constants
 *
 * @coversNothing
 */
final class TranslationsTest extends TestCase {

    public function testMacedonianTranslationsAreUTF8(): void {
        // Test that Macedonian error messages contain Cyrillic characters
        $this->assertNotEquals(strlen(MK_ERR1), mb_strlen(MK_ERR1, 'UTF-8'), 'MK_ERR1 should contain multi-byte UTF-8 characters');
        $this->assertNotEquals(strlen(MK_ERR2), mb_strlen(MK_ERR2, 'UTF-8'), 'MK_ERR2 should contain multi-byte UTF-8 characters');
        
        // Test that translations contain Cyrillic characters
        $this->assertStringContainsString('Променет', MK_TRANS['Altered'], 'Macedonian translation should contain Cyrillic');
        $this->assertStringContainsString('Додадено', MK_TRANS['Added'], 'Macedonian translation should contain Cyrillic');
    }

    public function testRussianTranslationsAreUTF8(): void {
        // Test that Russian error messages contain Cyrillic characters
        $this->assertNotEquals(strlen(RU_ERR1), mb_strlen(RU_ERR1, 'UTF-8'), 'RU_ERR1 should contain multi-byte UTF-8 characters');
        $this->assertNotEquals(strlen(RU_ERR2), mb_strlen(RU_ERR2, 'UTF-8'), 'RU_ERR2 should contain multi-byte UTF-8 characters');
        
        // Test that translations contain Cyrillic characters
        $this->assertStringContainsString('Изменен', RU_TRANS['Altered'], 'Russian translation should contain Cyrillic');
        $this->assertStringContainsString('Добавлен', RU_TRANS['Added'], 'Russian translation should contain Cyrillic');
    }

    public function testSerbianTranslationsAreUTF8(): void {
        // Test that Serbian error messages contain Cyrillic characters
        $this->assertNotEquals(strlen(SR_ERR1), mb_strlen(SR_ERR1, 'UTF-8'), 'SR_ERR1 should contain multi-byte UTF-8 characters');
        $this->assertNotEquals(strlen(SR_ERR2), mb_strlen(SR_ERR2, 'UTF-8'), 'SR_ERR2 should contain multi-byte UTF-8 characters');
        
        // Test that translations contain Cyrillic characters
        $this->assertStringContainsString('Промењен', SR_TRANS['Altered'], 'Serbian translation should contain Cyrillic');
        $this->assertStringContainsString('Додано', SR_TRANS['Added'], 'Serbian translation should contain Cyrillic');
    }

    public function testVietnameseTranslationsAreUTF8(): void {
        // Test that Vietnamese error messages contain diacritics
        $this->assertNotEquals(strlen(VI_ERR1), mb_strlen(VI_ERR1, 'UTF-8'), 'VI_ERR1 should contain multi-byte UTF-8 characters');
        $this->assertNotEquals(strlen(VI_ERR2), mb_strlen(VI_ERR2, 'UTF-8'), 'VI_ERR2 should contain multi-byte UTF-8 characters');
        
        // Test that translations contain Vietnamese diacritics
        $this->assertStringContainsString('Đã', VI_TRANS['Altered'], 'Vietnamese translation should contain diacritics');
        $this->assertStringContainsString('thêm', VI_TRANS['Added'], 'Vietnamese translation should contain diacritics');
    }

    public function testTranslationReplacementWorks(): void {
        // Test that translation replacement works correctly
        $summary = 'Altered title. Added DOI.';
        
        // Test Macedonian
        $mk_summary = $summary;
        foreach (MK_TRANS as $eng => $not_eng) {
            $mk_summary = str_replace($eng, $not_eng, $mk_summary);
        }
        $this->assertStringContainsString('Променет', $mk_summary, 'Macedonian translation should replace "Altered"');
        $this->assertStringContainsString('Додадено', $mk_summary, 'Macedonian translation should replace "Added"');
        
        // Test Russian
        $ru_summary = $summary;
        foreach (RU_TRANS as $eng => $not_eng) {
            $ru_summary = str_replace($eng, $not_eng, $ru_summary);
        }
        $this->assertStringContainsString('Изменен', $ru_summary, 'Russian translation should replace "Altered"');
        $this->assertStringContainsString('Добавлен', $ru_summary, 'Russian translation should replace "Added"');
        
        // Test Serbian
        $sr_summary = $summary;
        foreach (SR_TRANS as $eng => $not_eng) {
            $sr_summary = str_replace($eng, $not_eng, $sr_summary);
        }
        $this->assertStringContainsString('Промењен', $sr_summary, 'Serbian translation should replace "Altered"');
        $this->assertStringContainsString('Додано', $sr_summary, 'Serbian translation should replace "Added"');
        
        // Test Vietnamese
        $vi_summary = $summary;
        foreach (VI_TRANS as $eng => $not_eng) {
            $vi_summary = str_replace($eng, $not_eng, $vi_summary);
        }
        $this->assertStringContainsString('Đã thay đổi', $vi_summary, 'Vietnamese translation should replace "Altered"');
        $this->assertStringContainsString('Đã thêm', $vi_summary, 'Vietnamese translation should replace "Added"');
    }

    public function testAllTranslationKeysAreStrings(): void {
        // Ensure all translation arrays have string keys and values
        foreach (MK_TRANS as $key => $value) {
            $this->assertIsString($key, 'MK_TRANS key should be string');
            $this->assertIsString($value, 'MK_TRANS value should be string');
        }
        
        foreach (RU_TRANS as $key => $value) {
            $this->assertIsString($key, 'RU_TRANS key should be string');
            $this->assertIsString($value, 'RU_TRANS value should be string');
        }
        
        foreach (SR_TRANS as $key => $value) {
            $this->assertIsString($key, 'SR_TRANS key should be string');
            $this->assertIsString($value, 'SR_TRANS value should be string');
        }
        
        foreach (VI_TRANS as $key => $value) {
            $this->assertIsString($key, 'VI_TRANS key should be string');
            $this->assertIsString($value, 'VI_TRANS value should be string');
        }
    }

    public function testErrorMessagesAreNotEmpty(): void {
        // Ensure all error messages are defined and not empty
        $this->assertNotEmpty(MK_ERR1, 'MK_ERR1 should not be empty');
        $this->assertNotEmpty(MK_ERR2, 'MK_ERR2 should not be empty');
        $this->assertNotEmpty(RU_ERR1, 'RU_ERR1 should not be empty');
        $this->assertNotEmpty(RU_ERR2, 'RU_ERR2 should not be empty');
        $this->assertNotEmpty(SR_ERR1, 'SR_ERR1 should not be empty');
        $this->assertNotEmpty(SR_ERR2, 'SR_ERR2 should not be empty');
        $this->assertNotEmpty(VI_ERR1, 'VI_ERR1 should not be empty');
        $this->assertNotEmpty(VI_ERR2, 'VI_ERR2 should not be empty');
        $this->assertNotEmpty(ENG_ERR1, 'ENG_ERR1 should not be empty');
        $this->assertNotEmpty(ENG_ERR2, 'ENG_ERR2 should not be empty');
    }
}
