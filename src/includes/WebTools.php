<?php

declare(strict_types=1);

/**
 * Only on webpage
 */

/**
 * @codeCoverageIgnore
 * @param array<string> $pages_in_category
 */
function edit_a_list_of_pages(array $pages_in_category, WikipediaBot $api, string $edit_summary_end): void {
    $final_edit_overview = "";
    // Remove pages with blank as the name, if present
    $key = array_search("", $pages_in_category);
    if ($key !== false) {
        unset($pages_in_category[$key]);
    }
    if (empty($pages_in_category)) {
        report_warning('No links to expand found');
        bot_html_footer();
        return;
    }
    $total = count($pages_in_category);
    $effective_max = defined('MAX_PAGES_OVERRIDE') ? MAX_PAGES_OVERRIDE : MAX_PAGES;
    if ($total > $effective_max) {
        report_warning('Number of links is huge. Cancelling run. Maximum size is ' . (string) $effective_max);
        bot_html_footer();
        return;
    }
    big_jobs_check_overused($total);

    $page = new Page();
    $done = 0;
    $pages_changed = 0;   // Pages where expand_text() returned true, meaning text was actually modified
    $pages_unchanged = 0; // Pages where no edit was made: no changes needed, blank, protected, redirect, etc.

    foreach ($pages_in_category as $page_title) {
        flush(); // Only call to flush in normal code, since calling flush breaks headers and sessions
        big_jobs_check_killed();
        $done++;

        // Show progress for multi-page runs in HTML mode
        if (HTML_OUTPUT && $total > 1) {
            progress_status("Processing page " . (string)$done . " of " . (string)$total . "...");
        }

        // Open a card for this page before processing (wraps all report_* output)
        card_open($page_title, 'pending', 'Processing...');

        if (mb_strpos($page_title, 'Wikipedia:Requests') === false && $page->get_text_from($page_title) && $page->expand_text()) {
            $pages_changed++;
            if (SAVETOFILES_MODE) {
                // Sanitize file name by replacing characters that are not allowed on most file systems to underscores, and also replace path characters
                // And add .md extension to avoid troubles with devices such as 'con' or 'aux'
                $filename = preg_replace('~[\/\\:*?"<>|\s]~', '_', $page_title) . '.md';
                report_phase("Saving to file " . echoable($filename));
                $body = $page->parsed_text();
                $bodylen = mb_strlen($body, '8bit'); // byte count, not character count
                if (file_put_contents($filename, $body) === $bodylen) {
                    report_phase("Saved to file " . echoable($filename));
                } else {
                    report_warning("Save to file failed.");
                }
                unset($body);
                card_close();
            } else {
                report_phase("Writing to " . echoable($page_title) . '... ');
                $attempts = 0;
                if ($total === 1) {
                    $edit_sum = $edit_summary_end;
                } else {
                    $edit_sum = $edit_summary_end . (string) $done . '/' . (string) $total . ' ';
                }
                while (!$page->write($api, $edit_sum) && $attempts < MAX_TRIES) {
                    ++$attempts;
                }
                if ($attempts < MAX_TRIES) {
                    $last_rev = WikipediaBot::get_last_revision($page_title);
                    html_echo(
                    "",
                    "\n" . WIKI_ROOT . "?title=" . urlencode($page_title) . "&diff=prev&oldid=" . $last_rev . "\n");
                    if (HTML_OUTPUT) {
                        card_footer_with_links($page_title, $last_rev);
                        card_close_after_footer();
                    }
                    $final_edit_overview .=
                        "\n [ <a href=\"" . WIKI_ROOT . "?title=" . urlencode($page_title) . "&amp;diff=prev&amp;oldid="
                    . $last_rev . "\">diff</a>" .
                    " | <a href=\"" . WIKI_ROOT . "?title=" . urlencode($page_title) . "&amp;action=history\">history</a> ] " . "<a href=\"" . WIKI_ROOT . "?title=" . urlencode($page_title) . "\">" . echoable($page_title) . "</a>";
                } else {
                    report_warning("Write failed.");
                    $final_edit_overview .= "\n Write failed.            " . "<a href=\"" . WIKI_ROOT . "?title=" . urlencode($page_title) . "\">" . echoable($page_title) . "</a>";
                    card_close();
                }
            }
        } else {
            $pages_unchanged++;
            report_phase($page->parsed_text() ? "No changes required. \n\n      # # # " : "Blank page. \n\n      # # # ");
                $final_edit_overview .= "\n No changes needed. " . "<a href=\"" . WIKI_ROOT . "?title=" . urlencode($page_title) . "\">" . echoable($page_title) . "</a>";
                card_close();
        }

        echo "\n";
        check_memory_usage("After writing page");
        $page->parse_text("");  // Clear variables before doing GC
        gc_collect_cycles();        // This should do nothing
        memory_reset_peak_usage();
    }
    if ($total > 1) {
        if (!HTML_OUTPUT) {
            $final_edit_overview = '';
        }
        if (HTML_OUTPUT) {
            summary_section(
                "Done all " . (string) $total . " pages: " . (string) $pages_changed . " changed, " . (string) $pages_unchanged . " unchanged.",
                '<li>' . $final_edit_overview . '</li>'
            );
        } else {
            echo "\n Done all " . (string) $total . " pages: " . (string) $pages_changed . " changed, " . (string) $pages_unchanged . " unchanged. \n  # # # \n" . $final_edit_overview;
        }
    } else {
        echo "\n Done with page.";
    }
    bot_html_footer();
}

/**
 * @codeCoverageIgnore
 */
function bot_html_header(): void {
    if (!HTML_OUTPUT) {
        echo "\n";
        return;
    }
    echo '<!DOCTYPE html><html lang="en" dir="ltr">', "\n",
    ' <head>', "\n",
    '  <meta name="viewport" content="width=device-width, initial-scale=1.0" />', "\n",
    '  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', "\n",
    '  <title>Citation Bot: running</title>', "\n",
    '  <link rel="copyright" type="text/html" href="https://www.gnu.org/licenses/gpl-3.0" />', "\n",
    '  <link rel="stylesheet" type="text/css" href="assets/results.css" />', "\n",
    ' </head>', "\n",
    ' <body>', "\n",
    '  <a href="#main-content" class="skip-link">Skip to main content</a>', "\n",
    '  <header class="results-header">', "\n",
    '   <p>Citation Bot progress</p>', "\n",
    '   <p>', "\n",
    '    <a href="https://en.wikipedia.org/wiki/User:Citation_bot/use" target="_blank" rel="noopener noreferrer" title="Using Citation Bot">How&nbsp;to&nbsp;Use&nbsp;/&nbsp;Tips&nbsp;and&nbsp;Tricks</a> |',
    '    <a href="https://en.wikipedia.org/wiki/User_talk:Citation_bot" target="_blank" rel="noopener noreferrer" title="Report bugs at Wikipedia">Report&nbsp;bugs</a> |',
    '    <a href="https://github.com/ms609/citation-bot" target="_blank" rel="noopener noreferrer" title="GitHub repository">Source&nbsp;code</a>',
    '   </p>', "\n",
    '  </header>', "\n",
    '  <main id="main-content">', "\n",
    '   <h1 class="sr-only">Citation Bot results</h1>', "\n",
    '   <div class="results-container">', "\n";
    if (ini_get('pcre.jit') === '0') {
        report_warning('PCRE JIT Disabled');
    }
}

/**
 * @codeCoverageIgnore
 */
function bot_html_footer(): void {
    if (HTML_OUTPUT) {
        echo '</div></main>', "\n",
        '<footer class="results-footer">', "\n",
        '  <p><a href="./">Edit another page</a></p>', "\n",
        '</footer>', "\n",
        '</body></html>';
    }
    echo "\n";
}
