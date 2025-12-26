# Citation Bot Performance Pattern Analysis

Generated: 2025-12-26 22:54:11

## Set Time Limit Calls (Slow Operations)

Found 59 locations where execution time limit is reset

### Files with Most Time Limit Resets:
- src/includes/Page.php: 13 times
- src/includes/Template.php: 8 times
- src/includes/api/APIarchives.php: 5 times
- src/includes/api/APIdoi.php: 4 times
- src/includes/api/APIBibCode.php: 4 times
- src/includes/api/APIPubMed.php: 3 times
- src/includes/api/APIzotero.php: 3 times
- src/includes/api/APIS2.php: 2 times
- src/includes/api/APIgoogle.php: 2 times
- src/includes/URLtools.php: 2 times

## Intentional Delays (sleep/usleep)

Found 32 intentional delay points

- src/includes/setup.php:15 - sleep(5)
- src/includes/api/APIPubMed.php:190 - sleep(1)
- src/includes/api/APIPubMed.php:206 - usleep(100000)
- src/includes/api/APIPubMed.php:354 - usleep(20000)
- src/includes/api/APIPubMed.php:358 - sleep(1)
- src/includes/api/APIarchives.php:11 - usleep($left)
- src/includes/api/APIzotero.php:153 - usleep($delay)
- src/includes/api/APIzotero.php:156 - sleep(2)
- src/includes/api/APIieee.php:26 - usleep(100000)
- src/includes/api/APIieee.php:39 - usleep(100000)
- src/includes/api/APIdoi.php:225 - sleep(1)
- src/includes/api/APIdoi.php:260 - sleep(1)
- src/includes/api/APIdoi.php:502 - sleep(2)
- src/includes/api/APIBibCode.php:554 - sleep($time_to_sleep)
- src/includes/doiTools.php:147 - sleep(4)
- src/includes/doiTools.php:149 - sleep(4)
- src/includes/doiTools.php:158 - sleep(10)
- src/includes/doiTools.php:184 - usleep($left)
- src/includes/doiTools.php:488 - usleep(100000)
- src/includes/doiTools.php:833 - sleep(5)
- src/includes/miscTools.php:247 - sleep($time_to_pause)
- src/includes/Page.php:788 - sleep(9)
- src/includes/Page.php:800 - sleep(9)
- src/includes/WikipediaBot.php:79 - sleep(10)
- src/includes/WikipediaBot.php:81 - sleep(1)
- src/includes/WikipediaBot.php:119 - sleep(10)
- src/includes/WikipediaBot.php:121 - sleep(1)
- src/includes/WikipediaBot.php:135 - sleep($depth + 2)
- src/includes/WikipediaBot.php:311 - sleep(5)
- src/includes/WikipediaBot.php:428 - sleep(4)
- src/includes/WikipediaBot.php:483 - sleep(5)
- src/includes/WikipediaBot.php:487 - sleep(10)

## Throttle Function Calls

Found 6 throttle function calls

- src/includes/api/APIarchives.php:5 - function throttle_archive (): void {
- src/includes/api/APIarchives.php:55 - throttle_archive();
- src/includes/doiTools.php:178 - function throttle_dx (): void {
- src/includes/doiTools.php:235 - throttle_dx();
- src/includes/miscTools.php:208 - function throttle(): void {
- src/includes/Page.php:778 - throttle(); // This is only writing.    Not pages that are left unchanged

## SLOW_MODE Conditional Operations

Found 5 references to SLOW_MODE

### Files with SLOW_MODE logic:
- src/includes/setup.php
- src/includes/api/APIzotero.php
- src/includes/api/APIBibCode.php
- src/includes/Page.php

## External API Calls (curl_exec)

Found 36 potential external API call locations

### Files with Most API Calls:
- src/includes/api/APIS2.php: 4 calls
- src/includes/api/APIdoi.php: 4 calls
- src/includes/URLtools.php: 4 calls
- src/includes/WikipediaBot.php: 4 calls
- src/includes/doiTools.php: 3 calls
- src/includes/api/APIunpaywall.php: 2 calls
- src/includes/api/APIzotero.php: 2 calls
- src/includes/api/APIieee.php: 2 calls
- src/includes/api/APIgoogle.php: 2 calls
- src/includes/bot_curl.php: 2 calls

