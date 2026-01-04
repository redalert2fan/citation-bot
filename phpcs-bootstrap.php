<?php
/**
 * Bootstrap file for phpcs to ensure MediaWiki codesniffer classes are autoloaded.
 */

// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Register a custom autoloader for MediaWiki\Sniffs namespace
spl_autoload_register(function($class) {
    if (strpos($class, 'MediaWiki\\Sniffs\\') === 0) {
        $file = __DIR__ . '/vendor/mediawiki/mediawiki-codesniffer/MediaWiki/Sniffs/' . str_replace('\\', '/', substr($class, 17)) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    return false;
}, true, true); // Prepend this autoloader

