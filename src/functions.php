<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

/**
 * System locale-save implementation of \ucfirst. For example, when using the tr_TR locale, \ucfirst('i') yields "i".
 * This implementation of ucfirst is locale-independent.
 * @param string $string
 * @return string
 */
function ucfirst(string $string): string
{
    $firstChar = substr($string, 0, 1);
    $firstCharUpper = strtr($firstChar, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    return $firstCharUpper . substr($string, 1);
}
