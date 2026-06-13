<?php

declare(strict_types=1);

const BORING_STUFF = ["boring", "removed", "added", "changed", "subsubitem", "subitem"];

require_once __DIR__ . '/constants.php';   // @codeCoverageIgnore

function html_echo(string $text, string $alternate_text = ''): void {
    if (!CI) {
        echo HTML_OUTPUT ? $text : $alternate_text; // @codeCoverageIgnore
    }
}

function user_notice(string $symbol, string $class, string $text): void {
    ob_start();
    if (defined('BIG_JOB_MODE') && in_array($class, BORING_STUFF, true)) {
        $text = '.'; // Echo something to keep the code alive, but not so much to overfill the cache
    }
    // These are split over three lines to avoid creating a single long string during error conditions - which could blow out the memory
    echo "\n ", (HTML_OUTPUT ? "<span class='{$class}'>" : ""), $symbol;
    if (defined('BIG_JOB_MODE') && mb_strlen($text) > 900) { // No one looks at this anyway - long ones are often URLs in zotero errors
        echo "HUGE amount of text NOT printed";
        bot_debug_log("HUGE amount of text NOT printed.  Here is a bit: " . mb_substr($text, 0, 500));
    } else {
        echo $text;
    }
    echo HTML_OUTPUT ? "</span>" : "";
    if (CI) {
        ob_end_clean();
    } else {
        ob_end_flush();
    }
}

function report_phase(string $text): void {
    user_notice("\n>", "phase", $text);
}

function report_action(string $text): void {
    user_notice(">", "subitem", $text);
}

function report_info(string $text): void {
    user_notice("  >", "subsubitem", $text);
}

function report_inaction(string $text): void {
    user_notice("  .", "boring", $text);
}

function report_warning(string $text): void {
    user_notice("  !", "warning", $text);
}

function report_modification(string $text): void {
    user_notice("  ~", "changed", $text);
}

function report_add(string $text): void {
    user_notice("  +", "added", $text);
}

function report_forget(string $text): void {
    user_notice("  -", "removed", $text);
}

function report_inline(string $text): void {
    if (!CI && defined('BIG_JOB_MODE')) {
        echo " ", $text;   // @codeCoverageIgnore
    }
}

/**
 * call report_warning to give users a message before we die
 * @codeCoverageIgnore
 */
function report_error(string $text): never {
    if (CI) {
        trigger_error($text);  // Stop this test now
    } elseif (function_exists('bot_debug_log')) {
        bot_debug_log($text);  // Code logfile, if defined
        report_warning($text); // To the user
    } else {
        report_warning($text); // To the user
        trigger_error($text);  // System Logfile
    }
    exit(0);
}

/**
 * @codeCoverageIgnore
 */
function report_minor_error(string $text): void {  // For things we want to error in tests, but continue on Wikipedia
    if (!HTML_OUTPUT) { // command line and testing
        report_error($text);
    } else {
        bot_debug_log($text);
        report_warning($text);
    }
}

/** special flags to mark this function as making all untrustworthy input magically safe to output */
function echoable(?string $string): string {
    /**
     * @psalm-taint-escape html
     * @psalm-taint-escape has_quotes
     */
    $string = (string) $string;
     return HTML_OUTPUT ? htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401) : $string;
}

function pubmed_link(string $identifier, string $pm): string {
    return HTML_OUTPUT
       ? '<a href="https://www.ncbi.nlm.nih.gov/pubmed/' . urlencode($pm) . '" target="_blank" rel="noopener noreferrer" aria-label="Open PMID in new window">' . mb_strtoupper($identifier) . ' ' . echoable($pm) . "</a>"   // @codeCoverageIgnore
       : mb_strtoupper($identifier) . ' ' . echoable($pm);
}

function bibcode_link(string $id): string {
    return HTML_OUTPUT
    ? '<a href="https://ui.adsabs.harvard.edu/abs/' . urlencode($id) . '" target="_blank" rel="noopener noreferrer" aria-label="Open bibcode in new window">' . echoable($id) . '</a>'   // @codeCoverageIgnore
    : echoable($id);
}

function doi_link(string $doi): string {
    return HTML_OUTPUT
    ? '<a href="https://dx.doi.org/' . doi_encode(urldecode($doi)) . '" target="_blank" rel="noopener noreferrer" aria-label="Open DOI in new window">' . echoable($doi) . '</a>'      // @codeCoverageIgnore
    : echoable($doi);
}

function jstor_link(string $id): string {
    return HTML_OUTPUT
    ? '<a href="https://www.jstor.org/citation/ris/' . urlencode($id) . '" target="_blank" rel="noopener noreferrer" aria-label="Open JSTOR in new window">JSTOR ' . echoable($id) . '</a>'    // @codeCoverageIgnore
    : "JSTOR " . echoable($id);
}

function wiki_link(string $page): string {
    return HTML_OUTPUT
    ? '<a href="' . WIKI_ROOT . '?title=' . urlencode(str_replace(' ', '_', $page)) . '" target="_blank" rel="noopener noreferrer" aria-label="Open wiki in new window">Wikipedia page: ' . echoable($page) . '</a>'    // @codeCoverageIgnore
    : "Wikipedia page : " . echoable($page);
}

/**
 * Open a result card for a processed page
 * @codeCoverageIgnore
 */
function card_open(string $page_title, string $status, string $status_label): void {
    if (!HTML_OUTPUT || CI) {
        return;
    }
    $status_class = match($status) {
        'changed' => 'status-changed',
        'unchanged' => 'status-unchanged',
        'error' => 'status-error',
        default => 'status-unchanged'
    };
    $escaped_title = echoable($page_title);
    ob_start();
    echo "\n" . '<article class="result-card" data-status="' . $status . '">' . "\n";
    echo '  <header class="card-header">' . "\n";
    echo '    <h2 class="card-title"><a href="' . WIKI_ROOT . '?title=' . urlencode($page_title) . '">' . $escaped_title . '</a></h2>' . "\n";
    echo '    <span class="status-badge ' . $status_class . '">' . echoable($status_label) . '</span>' . "\n";
    echo '  </header>' . "\n";
    echo '  <div class="card-body">' . "\n";
    ob_end_flush();
}

/**
 * Add a change item to the current card
 * @codeCoverageIgnore
 */
function card_change_item(string $symbol, string $class, string $text): void {
    if (!HTML_OUTPUT || CI) {
        return;
    }
    ob_start();
    $class_map = [
        'phase' => 'change-info',
        'subitem' => 'change-info',
        'subsubitem' => 'change-info',
        'warning' => 'change-warning',
        'changed' => 'change-modified',
        'added' => 'change-added',
        'removed' => 'change-removed',
        'boring' => 'change-info',
    ];
    $li_class = $class_map[$class] ?? 'change-info';
    echo '      <li class="change ' . $li_class . '">' . echoable($symbol . ' ' . $text) . '</li>' . "\n";
    ob_end_flush();
}

/**
 * Close the current card
 * @codeCoverageIgnore
 */
function card_close(): void {
    if (!HTML_OUTPUT || CI) {
        return;
    }
    ob_start();
    echo '  </div>' . "\n"; // Close card-body
    echo '</article>' . "\n";
    ob_end_flush();
}

/**
 * Close card after footer was already output (changed pages)
 * @codeCoverageIgnore
 */
function card_close_after_footer(): void {
    if (!HTML_OUTPUT || CI) {
        return;
    }
    ob_start();
    echo '</article>' . "\n";
    ob_end_flush();
}

/**
 * Add action links footer to the current card
 * @codeCoverageIgnore
 */
function card_footer_with_links(string $page_title, string $rev_id): void {
    if (!HTML_OUTPUT || CI) {
        return;
    }
    ob_start();
    echo '  </div>' . "\n"; // Close card-body before opening footer
    echo '  <footer class="card-footer">' . "\n";
    echo '    <a class="action-link" href="' . WIKI_ROOT . '?title=' . urlencode($page_title) . '&amp;diff=prev&amp;oldid=' . $rev_id . '">diff</a>' . "\n";
    echo '    <a class="action-link" href="' . WIKI_ROOT . '?title=' . urlencode($page_title) . '&amp;action=history">history</a>' . "\n";
    echo '    <a class="action-link" href="' . WIKI_ROOT . '?title=' . urlencode($page_title) . '">view page</a>' . "\n";
    echo '  </footer>' . "\n";
    ob_end_flush();
}

/**
 * Show processing progress status
 * @codeCoverageIgnore
 */
function progress_status(string $text): void {
    if (!HTML_OUTPUT || CI) {
        return;
    }
    ob_start();
    echo '<div class="progress-bar" role="status" aria-live="polite">' . echoable($text) . '</div>' . "\n";
    ob_end_flush();
}

/**
 * Show final summary section after all pages processed
 * @codeCoverageIgnore
 */
function summary_section(string $text, string $links_html): void {
    if (!HTML_OUTPUT || CI) {
        return;
    }
    ob_start();
    echo '<section class="summary-bar" aria-live="polite">' . "\n";
    echo '  <p>' . echoable($text) . '</p>' . "\n";
    echo '  <ul class="summary-links">' . "\n";
    echo $links_html;
    echo '  </ul>' . "\n";
    echo '</section>' . "\n";
    ob_end_flush();
}
