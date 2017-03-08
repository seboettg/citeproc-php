<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;


/**
 * Class StringHelper
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class StringHelper
{

    const PREPOSITIONS = [
        'on', 'in', 'at', 'since', 'for', 'ago', 'before', 'to', 'past', 'till', 'until', 'by', 'under', 'below', 'over',
        'above', 'across', 'through', 'into', 'towards', 'onto', 'from', 'of', 'off', 'about', 'via'
    ];

    const ARTICLES = [
        'a', 'an', 'the'
    ];

    const ADVERBS = [
        'yet', 'so', 'just', 'only'
    ];

    const CONJUNCTIONS = [
        'nor', 'so', 'and', 'or'
    ];

    const ADJECTIVES = [
        'down', 'up'
    ];


    public static function capitalizeAll($text)
    {
        $wordArray = explode(" ", $text);

        array_walk($wordArray, function (&$word) {
            $word = ucfirst($word);
        });

        return implode(" ", $wordArray);
    }

    public static function capitalizeForTitle($titleString)
    {
        if (preg_match('/(.+[^\<\>][\.:\/;\?\!]\s?)([a-z])(.+)/', $titleString, $match)) {
            $titleString = $match[1].StringHelper::mb_ucfirst($match[2]).$match[3];
        }

        $wordArray = explode(" ", $titleString);

        array_walk($wordArray, function (&$word) {

            $words = explode("-", $word);
            if (count($words) > 1) {
                array_walk($words, function (&$w) {
                    $w = StringHelper::keepLowerCase($w) ? $w : StringHelper::mb_ucfirst($w);
                });
                $word = implode("-", $words);
            }
            $word = StringHelper::keepLowerCase($word) ? $word : StringHelper::mb_ucfirst($word);
        });

        return implode(" ", $wordArray);
    }

    public static function keepLowerCase($word)
    {
        $lowerCase =  in_array($word, self::PREPOSITIONS) ||
                      in_array($word, self::ARTICLES) ||
                      in_array($word, self::CONJUNCTIONS) ||
                      in_array($word, self::ADJECTIVES);
        return $lowerCase;

    }

    public static function mb_ucfirst($string, $encoding = 'UTF-8')
    {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);
        $encodings = ['ISO-8859-7'];
        $encoding = mb_detect_encoding($firstChar, $encodings, true);
        return in_array($encoding, $encodings) ? $firstChar.$then : mb_strtoupper($firstChar, $encoding) . $then;
    }

    public static function initializeBySpaceOrHyphen($string, $initializeSign)
    {
        $res = "";
        $exploded = explode("-", $string);
        $i = 0;
        foreach ($exploded as $explode) {
            $spaceExploded = explode(" ", $explode);
            foreach ($spaceExploded as $givenPart) {
                $res .= substr($givenPart, 0, 1) . $initializeSign;
            }
            if ($i < count($exploded) - 1) {
                $res = rtrim($res) . "-";
            }
            ++$i;
        }
        return $res;
    }

    public static function camelCase2Hyphen($string)
    {
        $hyphenated = preg_replace("/([A-Z])/", "-$1", $string);
        $hyphenated = substr($hyphenated, 0, 1) === "-" ? substr($hyphenated, 1) : $hyphenated;
        return mb_strtolower($hyphenated);
    }
}