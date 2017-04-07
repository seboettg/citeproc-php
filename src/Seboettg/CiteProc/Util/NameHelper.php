<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

/**
 * Class NameHelper
 * @package Seboettg\CiteProc\Util
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class NameHelper
{

    /**
     * @param \stdClass $precedingItem
     * @param array $currentAuthor
     * @return bool
     */
    public static function identicalAuthors($precedingItem, $currentAuthor)
    {
        if (count($precedingItem->author) !== count($currentAuthor)) {
            return false;
        }
        foreach ($currentAuthor as $current) {
            if (self::precedingHasAuthor($precedingItem, $current)) {
                continue;
            }
            return false;
        }
        return true;
    }

    /**
     * @param \stdClass $preceding
     * @param \stdClass $name
     * @return bool
     */
    public static function precedingHasAuthor($preceding, $name)
    {
        foreach ($preceding->author as $author) {
            if ($author->family === $name->family && $author->given === $name->given) {
                return true;
            }
        }
        return false;
    }
}