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


    /**
     * removes the field $particle from $data and appends its content to the $namePart field of $data
     * @param \stdClass $data
     * @param string $namePart
     * @param string $particle
     */
    public static function appendParticleTo(&$data, $namePart, $particle)
    {
        if (isset($data->{$particle}) && isset($data->{$namePart})) {
            $data->{$namePart} = $data->{$namePart} . " " . $data->{$particle}; // append $particle to $namePart
            unset($data->{$particle}); //remove particle from $data
        }
    }

    /**
     * removes the field $particle from $data and prepends its content to the $namePart field of $data
     * @param \stdClass $data
     * @param string $namePart ("given"|"family")
     * @param string $particle
     */
    public static function prependParticleTo(&$data, $namePart, $particle)
    {
        if (isset($data->{$particle}) && isset($data->{$namePart})) {
            $data->{$namePart} = $data->{$particle} . " " . $data->{$namePart}; //prepend $particle to $namePart
            unset($data->{$particle});//remove particle from $data
        }
    }
}