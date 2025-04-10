<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use stdClass;

/**
 * Class NameHelper
 * @package Seboettg\CiteProc\Util
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class NameHelper
{

    /**
     * @param stdClass $precedingItem
     * @param array $currentAuthor
     * @return bool
     * @throws CiteProcException
     */
    public static function identicalAuthors($precedingItem, $currentAuthor)
    {
        if (!property_exists($precedingItem, "author")) {
            throw new CiteProcException("No author to present");
        }

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
     * @param stdClass $preceding
     * @param stdClass $name
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
     * @param stdClass $data
     * @param string $namePart
     * @param string $particle
     */
    public static function appendParticleTo(&$data, $namePart, $particle)
    {
        if (isset($data->{$particle}) && isset($data->{$namePart})) {
            $data->{$namePart} = $data->{$namePart}." ".$data->{$particle}; // append $particle to $namePart
            unset($data->{$particle}); //remove particle from $data
        }
    }

    /**
     * removes the field $particle from $data and prepends its content to the $namePart field of $data
     * @param stdClass $data
     * @param string $namePart ("given"|"family")
     * @param string $particle
     */
    public static function prependParticleTo(&$data, $namePart, $particle)
    {
        if (isset($data->{$particle}) && isset($data->{$namePart})) {
            $data->{$namePart} = $data->{$particle}." ".$data->{$namePart}; //prepend $particle to $namePart
            unset($data->{$particle}); //remove particle from $data
        }
    }

    /**
     * @param array $persons1
     * @param array $persons2
     * @return bool
     */
    public static function sameNames($persons1, $persons2)
    {
        $same = count($persons1) === count($persons2);

        if (!$same) {
            return false;
        }

        array_walk($persons1, function ($name, $key) use ($persons2, &$same) {
            $name1 = self::normalizeName($name);
            $name2 = self::normalizeName($persons2[$key]);
            $same = $same && ($name1 === $name2);
        });

        return (bool) $same;
    }

    /**
     * @param $data
     * @return string
     * @throws CiteProcException
     */
    public static function normalizeName($data)
    {
        if (empty($data->family)) {
            throw new CiteProcException("Illegal argument. Name has no family name.");
        }
        return $data->family.(isset($data->given) ? $data->given : "");
    }

    public static function addExtendedMarkup($nameVar, $nameItem, $formattedName)
    {
        $markupExtension = CiteProc::getContext()->getMarkupExtension();
        if (array_key_exists($nameVar, $markupExtension)) {
            $function = $markupExtension[$nameVar];
            if (is_callable($function)) {
                return $function($nameItem, $formattedName);
            }
        } elseif (array_key_exists($mode = CiteProc::getContext()->getMode(), $markupExtension)) {
            if (array_key_exists($nameVar, $markupExtension[$mode])) {
                $function = $markupExtension[$mode][$nameVar];
                if (is_callable($function)) {
                    return $function($nameItem, $formattedName);
                }
            }
        }
        return $formattedName;
    }
}
