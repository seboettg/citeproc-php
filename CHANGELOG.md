# CHANGELOG
## 2.2.2 2020-09-26

* Bugfix for [92](https://github.com/seboettg/citeproc-php/issues/92)
* Bugfix for [93](https://github.com/seboettg/citeproc-php/issues/93)

## 2.2.1 2020-09-12

* Bugfix for issue [82](https://github.com/seboettg/citeproc-php/issues/82)
* Bugfix for [84](https://github.com/seboettg/citeproc-php/issues/84)
* Merged PR [86](https://github.com/seboettg/citeproc-php/pull/86)
* Bugfix for [89](https://github.com/seboettg/citeproc-php/issues/89)

## 2.2.0 2020-04-04
* Compatibility for PHP 7.2, 7.3 and 7.4. This solves the issues [76](https://github.com/seboettg/citeproc-php/issues/76), [78](https://github.com/seboettg/citeproc-php/issues/75), [80](https://github.com/seboettg/citeproc-php/issues/80) and [81](https://github.com/seboettg/citeproc-php/issues/81).
* Merged Pull Requests [75](https://github.com/seboettg/citeproc-php/pull/75) and [79](https://github.com/seboettg/citeproc-php/pull/79)

Thanks to [@kchoong](https://github.com/kchoong) and [@westcomputerconsultancy](https://github.com/westcomputerconsultancy).

## 2.1.9 2019-11-04
* bugfix for [issue 68](https://github.com/seboettg/citeproc-php/issues/68)
* bugfix for [issue 69](https://github.com/seboettg/citeproc-php/issues/69)
* bugfix for [issue 70](https://github.com/seboettg/citeproc-php/issues/70)
* feature/enhancement for [issue 71](https://github.com/seboettg/citeproc-php/issues/71)
* refactoring of the code parts for rendering date ranges
* redesign/refactoring of constraints (condition handling for choose elements)

## 2.1.8 - 2019-09-13
* bugfix of [PR 66](https://github.com/seboettg/citeproc-php/pull/66)
* bugfix for displaced delimiters that appear when in the name list more than one empty entry exists.

## 2.1.7 - 2019-03-24

* bugfix of [PR 64](https://github.com/seboettg/citeproc-php/pull/64) Call to a member function getRangeDelimiter() on null
* bugfix of [PR 63](https://github.com/seboettg/citeproc-php/pull/63) Don't show "et-al" text for citation witch don't reach et-al number

## 2.1.6 - 2018-10-13

* bugfix of [issue 59](https://github.com/seboettg/citeproc-php/issues/59)
* bugfix of [issue 60](https://github.com/seboettg/citeproc-php/issues/60)

## 2.1.5 - 2018-09-23

* bugfix of [issue 57](https://github.com/seboettg/citeproc-php/issues/57)
* bugfix of [issue 58](https://github.com/seboettg/citeproc-php/issues/58)

## 2.1.4 - 2018-07-30

* bugfix of [PR 52](https://github.com/seboettg/citeproc-php/pull/52): Fix locale overrides using inactive language 
* bugfix of [PR 53](https://github.com/seboettg/citeproc-php/pull/53): Guard against unset variable
* improvement of [PR 54](https://github.com/seboettg/citeproc-php/pull/54): Add loading of primary dialect

Thanks to [@jonathonwalz](https://github.com/jonathonwalz) for these Pull Requests.

## 2.1.3 - 2018-06-15

* bugfix for issue [50](https://github.com/seboettg/citeproc-php/issues/50): In some cases punctuation in quote did not work.

## 2.1.2 - 2018-04-18

* bugfix for issue [49](https://github.com/seboettg/citeproc-php/issues/49): Stylesheets that used the ``text-case="title"`` option, in combination with some in Slavic (or Serbo-Croatian) language, caused errors that destroyed the entire output. This was caused by the capitalization of non-letter characters.

## 2.1.1 - 2018-02-08
* Support for render variables that are using "-short" suffixes, if Text tags have a "form" attribute which is set to "short": ``<text form="short" .../>``. This is used e.g. for abbreviated journal title (container-title-short) and occurred a wrong output in different styles (for example AMA American Medical Association) in previous citeproc-php versions. Have a look at issue [47](https://github.com/seboettg/citeproc-php/issues/47).

## 2.1.0 - 2017-11-23

* possibility to filter specific citations independently from CSL input data (inspired by @CarlosCraviotto PR #39). Have a look [here](https://github.com/seboettg/citeproc-php/blob/master/README.md#filter-citations).
* possibility to use custom Lambda functions to enrich bibliographies and citations with additional HTML markup. Have a look [here](https://github.com/seboettg/citeproc-php/blob/master/README.md#advanced-usage-of-citeproc-php).

## 2.0.4 - 2017-11-15

* bugfix for issue [46](https://github.com/seboettg/citeproc-php/issues/46): initialize names didn't work for cyrillic characters


## 2.0.3 - 2017-11-11

* bugfix for issue [44](https://github.com/seboettg/citeproc-php/issues/44): Missing title with chicago-fullnote-bibliography. The problem occurred because of an incorrect implementation of the "none-condition" in ChooseIf.
* bugfix for an issue that appears sometimes in connection with date-parts.

## 2.0.2 - 2017-10-04

* bugfix for issue [41](https://github.com/seboettg/citeproc-php/issues/41): fixed missing suppression of substituted values
* bugfix for issue [42](https://github.com/seboettg/citeproc-php/issues/42): citeproc-php caused a fatal error if php 5.6 was used
* citeproc-php uses now version 1.2 of [seboettg/collection](https://packagist.org/packages/seboettg/collection)

## 2.0.1 - 2017-05-23

* bugfix for issue [36](https://github.com/seboettg/citeproc-php/issues/36).
* bugfix for issue [37](https://github.com/seboettg/citeproc-php/issues/37).
* solves a problem of exceeded script runtime which sometimes occurs while running `composer update`. Now, 
the depended citation styles and locales are not longer composer dependencies but will be cloned by a shell script instead which simply will triggered from `composer update`.

## 2.0.0 - 2017-05-11

* 1st stable release
* fix issues that causing if styles using uncertain dates
* add info node parser and getInfo() method in Context class. Thus, it's now possible to get meta data of given stylesheet
* fix issues in et-al abbreviation which was appearing in citation

## 2.0.0-beta 2017-05-01

* beta release
* all features of milestone "Version 2.0" have been implemented
