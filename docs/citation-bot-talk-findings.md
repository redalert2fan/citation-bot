# Citation Bot Talk-Page Findings

Working worksheet for manually verifying the non-capitalization reports currently visible on [User talk:Citation bot](https://en.wikipedia.org/wiki/User_talk:Citation_bot).

**Snapshot:** 22 September 2026

**Scope:** Current unarchived talk page only. Archives were excluded. The three capitalization-only reports were excluded: `Caps: de La Plata`, `Caps: И → и`, and `Caps: у`.

**Code under test:** Official `upstream/master`, commit `f5125dc75a10bd0774805daf470fb3249a5cc9bf`, `Dedupe at=Note into article-number (#6051)`.

**Word version:** [citation-bot-talk-findings.docx](citation-bot-talk-findings.docx)

**Important:** “Not reproduced” is not automatically “fixed”. It can mean that the report was incorrect, the linked revision is not the reported edit, upstream metadata changed, or the test input could not be reconstructed exactly.

## Manual Procedure

1. Start from a clean disposable checkout. Do not run the investigation against a dirty working tree.

   ```powershell
   git fetch --prune upstream master
   git worktree add --detach "$env:TEMP\citation-bot-verify" upstream/master
   Set-Location "$env:TEMP\citation-bot-verify"
   composer install --no-interaction --prefer-dist
   ```

2. Record the exact commit with `git rev-parse HEAD`.

3. For a link of the form `diff=prev&oldid=NNN`, retrieve revision `NNN` and its parent. The parent is normally the pre-bot citation. For a two-revision diff, use the earlier revision as input.

4. Replay the exact citation through `Page::parse_text()` followed by `Page::expand_text()` in both fast and slow modes. A live page replay with `--savetofiles` is acceptable only when the linked historical citation cannot be reconstructed.

5. Compare the output with the report and record one of these outcomes:

   - `CONFIRMED FIXED`: the exact reported input no longer produces the bad output, with code/test evidence or a repeatable replay.
   - `STILL REPRODUCES`: the same incorrect transformation occurs.
   - `DIFFERENT RESULT`: the old transformation is gone, but the current output is still wrong or incomplete.
   - `NOT REPRODUCED`: the exact input produces no change and the report may be stale or incorrect.
   - `INSUFFICIENT EVIDENCE`: the report lacks exact wikitext, the diff is unavailable, or behavior depends on external API state.
   - `FALSE ATTRIBUTION`: the cited edit was not made by Citation Bot.

6. Check the output against the documentation links below. A syntactically valid CS1 identifier is not proof that it belongs to the cited work; verify identity against the publisher, DOI registry, catalogue, or source page when relevant.

## Verification Commands

Run the deterministic regression matrix on the tested commit:

```powershell
php tools/cs1_harness.php
php tools/cs1_harness.php --slow
```

The observed baseline for this snapshot was `45 passed, 16 documented known gaps, 0 failures` in each mode. The focused regression test for report `at=` → `article-number=` was:

```powershell
php -d memory_limit=2G vendor/bin/phpunit tests/phpunit/includes/TemplatePart2Test.php --filter testArticleNumberReplacesEquivalentAtNote
```

Observed result: `1 test, 5 assertions passed`.

## Documentation References

- [Help:CS1 errors](https://en.wikipedia.org/wiki/Help:CS1_errors)
- [Help:Citation Style 1](https://en.wikipedia.org/wiki/Help:Citation_Style_1)
- [Template:Cite web](https://en.wikipedia.org/wiki/Template:Cite_web)
- [Template:Cite journal](https://en.wikipedia.org/wiki/Template:Cite_journal)
- [Template:Cite book](https://en.wikipedia.org/wiki/Template:Cite_book)
- [Wikipedia:Citation templates](https://en.wikipedia.org/wiki/Wikipedia:Citation_templates)

Relevant rules include the distinction between web, journal, and book sources; `url` versus `chapter-url` and `contribution-url`; `page`, `pages`, `at`, and `article-number`; required parent parameters; and identifier syntax validation.

## Report Worksheet

The provisional results below came from replaying the linked or quoted citations on the commit above. Replace the provisional result after manual confirmation, and use the final column for source checks, API observations, or corrected interpretations.

| # | Report and evidence | Provisional result | Manual verification | Notes / final result |
|---:|---|---|---|---|
| 1 | [URL removed](https://en.wikipedia.org/wiki/Special:Diff/1254239468) | **STILL REPRODUCES**: Amazon/Library Journal `cite web` becomes `cite book` and loses the URL. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 2 | [author/first → last/first](https://en.wikipedia.org/wiki/Special:Diff/1257568538) | **NOT REPRODUCED**: four exact book examples remained unchanged. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 3 | [web vs book](https://en.wikipedia.org/wiki/Special:Diff/1256314002), [follow-up](https://en.wikipedia.org/wiki/Special:Diff/1263375628) | **LIKELY FIXED**: ISBN/OCLC web examples stayed `cite web`. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 4 | [Amazon link bug](https://en.wikipedia.org/wiki/Special:Diff/1263465110), [second example](https://en.wikipedia.org/wiki/Special:Diff/1265696226) | **STILL REPRODUCES**: both Amazon sources become book citations and lose the URL. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 5 | [conference → journal](https://en.wikipedia.org/wiki/Special:Diff/1268341479) | **NOT REPRODUCED** for the exact pre-bot citation. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 6 | [half-assed cite web → cite journal](https://en.wikipedia.org/wiki/Special:Diff/1271282424) | **DIFFERENT RESULT**: leaves `cite web` and adds DOI. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 7 | [IAU Circular / CBET fields](https://en.wikipedia.org/wiki/Special:Diff/1273942491) | **LIKELY FIXED**: tested `volume` becomes `issue`. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 8 | [Current Topics series](https://en.wikipedia.org/wiki/Special:Diff/1275415131), [follow-up](https://en.wikipedia.org/wiki/Special:Diff/1276392179) | **STILL INCOMPLETE**: converts to book but incomplete chapter structure. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 9 | [book title/chapter swapped](https://en.wikipedia.org/wiki/Special:Diff/1289615016) | **NOT REPRODUCED**; original diff inaccessible. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 10 | [incorrect HDL](https://en.wikipedia.org/wiki/Special:Diff/1302363847) | **NOT REPRODUCED**: no unrelated HDL added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 11 | [Italic tags](https://en.wikipedia.org/wiki/Special:Diff/1306735547) and three linked examples | **NOT REPRODUCED**; no precise expected output given. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 12 | [volume changed to issue](https://en.wikipedia.org/wiki/Special:Diff/1307257118) | **STILL REPRODUCES RELATED DAMAGE**: removes volume, retains issue. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 13 | [Associated Press](https://en.wikipedia.org/wiki/Special:Diff/1307480172) | **STILL INCORRECT / DIFFERENT**: `agency` becomes `work=Associated Press News`. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 14 | [IAU Circular cleanup](https://en.wikipedia.org/wiki/Special:Diff/1309130927) | **LIKELY FIXED**: loses inappropriate volume fields. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 15 | [url → chapter-url](https://en.wikipedia.org/wiki/Special:Diff/1309227702) | **STILL REPRODUCES**: whole-book IA URLs moved to `chapter-url`. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 16 | [Advances in Enzymology](https://en.wikipedia.org/wiki/Special:Diff/1313478328) | **NOT REPRODUCED**; title added to not-a-journal list. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 17 | [wrong URL](https://en.wikipedia.org/wiki/Special:Diff/1309822714), [recurrence](https://en.wikipedia.org/wiki/Special:Diff/1368642122) | **NOT REPRODUCED**: Figshare URL not added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 18 | [incorrect ISBN](https://en.wikipedia.org/wiki/Special:Diff/1321722265) | **INSUFFICIENT EVIDENCE**: no ISBN added in replay, edition not verified. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 19 | [cite web → cite book mapping](https://en.wikipedia.org/wiki/Special:Diff/1323850993) and [PR 5920](https://github.com/ms609/citation-bot/pull/5920) | **STILL INCOMPLETE**: Erxleben maps work to series but leaves title wrong. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 20 | [existing cite book mapping](https://en.wikipedia.org/wiki/Special:Diff/1324718002) | **NOT REPRODUCED / INSUFFICIENT**. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 21 | [ISBN/date incompatibility](https://en.wikipedia.org/wiki/Special:Diff/1313444244) | **STILL REPRODUCES**: `orig-year` becomes `year`. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 22 | [CABI inconsistency](https://en.wikipedia.org/wiki/Special:Diff/1326275105) | **NOT REPRODUCED IN ISOLATION**. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 23 | [fake issue and identifiers](https://en.wikipedia.org/wiki/Special:Diff/1326425964) | **INSUFFICIENT EVIDENCE**: exact citation not quoted. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 24 | [redundant encyclopedia title](https://en.wikipedia.org/wiki/Special:Diff/1327973638) | **LIKELY FIXED**: no redundant title added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 25 | [bogus series](https://en.wikipedia.org/wiki/Special:Diff/1334297429) | **LIKELY FIXED**: no series added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 26 | [conference/journal contamination](https://en.wikipedia.org/wiki/Special:Diff/1335696299) | **NOT REPRODUCED**: only `doi-access` added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 27 | [url should be contribution-url](https://en.wikipedia.org/wiki/Special:Diff/1335896006) | **NOT REPRODUCED**: no URL added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 28 | [math formatting removed](https://en.wikipedia.org/wiki/Special:Diff/1335903462) | **LIKELY FIXED**: `<math>` preserved. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 29 | [same value volume and issue](https://en.wikipedia.org/wiki/Special:Diff/1329009885) | **LIKELY FIXED**: `volume=163` unchanged. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 30 | [malformed vauthors](https://en.wikipedia.org/wiki/Special:Diff/1347354919) | **LIKELY FIXED**: malformed vauthors not added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 31 | [unrelated thesis URL](https://en.wikipedia.org/wiki/Special:Diff/1350864862) | **LIKELY FIXED**: no thesis URL added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 32 | [spurious Annual Reviews issue](https://en.wikipedia.org/wiki/Special:Diff/1351794869) | **NOT REPRODUCED**: no issue added. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 33 | [`cookieAbsent`](https://en.wikipedia.org/wiki/Special:Diff/1351798135), [second example](https://en.wikipedia.org/wiki/Special:Diff/1351799610) | **UNRESOLVED CLEANUP**: citations untouched. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 34 | [shortened DOI](https://en.wikipedia.org/wiki/Special:Diff/1354485720) | **FALSE ATTRIBUTION LIKELY**. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 35 | [duplicate chapter/title](https://en.wikipedia.org/wiki/Special:Diff/1365841755) | **DIFFERENT / AMBIGUOUS**. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 36 | [page P# range](https://en.wikipedia.org/wiki/Special:Diff/1368649553), [second](https://en.wikipedia.org/wiki/Special:Diff/1368661324) | **STILL REPRODUCES**: `P12:3-P12:9` becomes `12:3-P12:9`. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 37 | [garbled math formula](https://en.wikipedia.org/wiki/Special:Diff/1368650401) | **NOT REPRODUCED**: formula preserved. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 38 | [same journal and series](https://en.wikipedia.org/wiki/Special:Diff/1368642122) | **NOT REPRODUCED**. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 39 | [TBA volume/issue/pages](https://en.wikipedia.org/wiki/Special:Diff/1370511618) | **FEATURE UNIMPLEMENTED**: TBA left unchanged. | [ ] Confirmed [ ] Changed [ ] Invalid |  |
| 40 | [cleanup article](https://en.wikipedia.org/wiki/Special:Diff/1375460136), [second](https://en.wikipedia.org/wiki/Special:Diff/1375462328) | **NOT REPRODUCIBLE**: placeholder not in linked revisions. | [ ] Confirmed [ ] Changed [ ] Invalid |  |

## Feature Requests

| Request | Provisional finding | Manual verification |
|---|---|---|
| DOI to Who's Who | No expansion; no implementation found. | [ ] Confirmed [ ] Changed [ ] Invalid |
| Convert cite web to BioRef/GBIF | No implementation found. | [ ] Confirmed [ ] Changed [ ] Invalid |
| Crossref Retraction Watch | No implementation found. | [ ] Confirmed [ ] Changed [ ] Invalid |
| Treat and and & as equivalent | Partial normalization in TextTools.php; example needs replay. | [ ] Confirmed [ ] Changed [ ] Invalid |
| Add free scholar.archive.org links | No dedicated feature found. | [ ] Confirmed [ ] Changed [ ] Invalid |

## Final Manual Notes

- Full `composer run test` was not a clean pass. The parallel suite produced several network-dependent failures and a Wikipedia API retry timeout in `ConstantsTest::testConversionsGood` at `src/includes/WikipediaBot.php:843`.
- The CS1 harness passed in both modes; its 16 known gaps are documented behavior, not talk-page findings.
- The report about `at=` being retained after conversion is not in the table because current master contains commit `#6051`; its focused regression test passed.
- No capitalization-only report is included.
