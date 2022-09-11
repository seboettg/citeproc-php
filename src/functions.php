<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use Seboettg\Collection\Lists\ListInterface;
use Seboettg\Collection\Map\MapInterface;
use function Seboettg\Collection\Lists\emptyList;
use function Seboettg\Collection\Map\emptyMap;

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

function mapOfArray(array $array): MapInterface
{
    $map = emptyMap();
    foreach ($array as $key => $value) {
        $map->put($key, $value);
    }
    return $map;
}

function listOfLists(...$array): ListInterface
{
    $list = emptyList();
    foreach ($array as $item) {
        if (is_array($item)) {
            return listOfLists(...$item);
        }
        $list->add($item);
    }
    return $list;
}
