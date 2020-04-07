<?php /** @noinspection PhpInternalEntityUsedInspection */

/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

use Seboettg\CiteProc\CiteProc;
use Symfony\Polyfill\Mbstring\Mbstring;

/**
 * Class StringHelper
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class StringHelper
{
    const PREPOSITIONS = [
        'on', 'in', 'at', 'since', 'for', 'ago', 'before', 'to', 'past', 'till', 'until', 'by', 'under', 'below',
        'over', 'above', 'across', 'through', 'into', 'towards', 'onto', 'from', 'of', 'off', 'about', 'via'
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

    const ISO_ENCODINGS = [
        'ISO-8859-1',
        'ISO-8859-2',
        'ISO-8859-3',
        'ISO-8859-4',
        'ISO-8859-5',
        'ISO-8859-6',
        'ISO-8859-7',
        'ISO-8859-8',
        'ISO-8859-9',
        'ISO-8859-10',
        'ISO-8859-11',
        'ISO-8859-13',
        'ISO-8859-14',
        'ISO-8859-15',
        'ISO-8859-16'
    ];

    /**
     * opening quote sign
     */
    const OPENING_QUOTE = "“";

    /**
     * closing quote sign
     */
    const CLOSING_QUOTE = "”";

    /**
     * @param $text
     * @return string
     */
    public static function capitalizeAll($text)
    {
        $wordArray = explode(" ", $text);

        array_walk($wordArray, function (&$word) {
            $word = ucfirst($word);
        });

        return implode(" ", $wordArray);
    }

    /**
     * @param $titleString
     * @return string
     */
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

    /**
     * @param $word
     * @return bool
     */
    public static function keepLowerCase($word)
    {
        // keep lower case if the first char is not an utf-8 letter
        return in_array($word, self::PREPOSITIONS) ||
            in_array($word, self::ARTICLES) ||
            in_array($word, self::CONJUNCTIONS) ||
            in_array($word, self::ADJECTIVES) ||
            (bool) preg_match("/[^\p{L}].+/", $word);
    }

    /**
     * @param $string
     * @param string $encoding
     * @return string
     */
    // phpcs:disable
    public static function mb_ucfirst($string, $encoding = 'UTF-8')
    {// phpcs:enable
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);

        /** @noinspection PhpInternalEntityUsedInspection */
        $encoding = Mbstring::mb_detect_encoding($firstChar, self::ISO_ENCODINGS, true);
        return in_array($encoding, self::ISO_ENCODINGS) ?
            Mbstring::mb_strtoupper($firstChar, $encoding).$then : $firstChar.$then;
    }
    // phpcs:disable
    public static function mb_strrev($string)
    {// phpcs:enable
        $result = '';
        for ($i = mb_strlen($string); $i >= 0; --$i) {
            $result .= mb_substr($string, $i, 1);
        }
        return $result;
    }

    /**
     * @param string $delimiter
     * @param string[] $arrayOfStrings
     * @return string;
     */
    public static function implodeAndPreventConsecutiveChars($delimiter, $arrayOfStrings)
    {
        $delim = trim($delimiter);
        if (!empty($delim)) {
            foreach ($arrayOfStrings as $key => $textPart) {
                $pos = mb_strpos(StringHelper::mb_strrev($textPart), StringHelper::mb_strrev($delim));
                if ($pos === 0) {
                    $length = mb_strlen($textPart) - mb_strlen($delim);
                    $textPart = mb_substr($textPart, 0, $length);
                    $arrayOfStrings[$key] = $textPart;
                }
            }
        }
        return implode($delimiter, $arrayOfStrings);
    }

    /**
     * @param $string
     * @param $initializeSign
     * @return string
     */
    public static function initializeBySpaceOrHyphen($string, $initializeSign)
    {
        $initializeWithHyphen = CiteProc::getContext()->getGlobalOptions()->isInitializeWithHyphen();
        $res = "";
        $exploded = explode("-", $string);
        $i = 0;
        foreach ($exploded as $explode) {
            $spaceExploded = explode(" ", $explode);
            foreach ($spaceExploded as $givenPart) {
                $firstLetter = mb_substr($givenPart, 0, 1, "UTF-8");
                if (StringHelper::isLatinString($firstLetter)) {
                    $res .= ctype_upper($firstLetter) ? $firstLetter.$initializeSign : " ".$givenPart." ";
                } else {
                    $res .= $firstLetter.$initializeSign;
                }
            }
            if ($i < count($exploded) - 1 && $initializeWithHyphen) {
                $res = rtrim($res)."-";
            }
            ++$i;
        }
        return $res;
    }

    /**
     * @param $string
     * @return mixed|string
     */
    public static function camelCase2Hyphen($string)
    {
        $hyphenated = preg_replace("/([A-Z])/", "-$1", $string);
        $hyphenated = substr($hyphenated, 0, 1) === "-" ? substr($hyphenated, 1) : $hyphenated;
        return mb_strtolower($hyphenated);
    }

    /**
     * @param $string
     * @return bool
     */
    public static function checkLowerCaseString($string)
    {
        return ($string === mb_strtolower($string));
    }

    /**
     * @param $string
     * @return bool
     */
    public static function checkUpperCaseString($string)
    {
        return ($string === mb_strtoupper($string));
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function clearApostrophes($string)
    {
        return preg_replace("/\'/", "’", $string);
    }

    /**
     * replaces outer quotes of $text by given inner quotes
     *
     * @param $text
     * @param $outerOpenQuote
     * @param $outerCloseQuote
     * @param $innerOpenQuote
     * @param $innerCloseQuote
     * @return string
     */
    public static function replaceOuterQuotes(
        $text,
        $outerOpenQuote,
        $outerCloseQuote,
        $innerOpenQuote,
        $innerCloseQuote
    ) {
        if (preg_match("/(.*)$outerOpenQuote(.+)$outerCloseQuote(.*)/u", $text, $match)) {
            return $match[1].$innerOpenQuote.$match[2].$innerCloseQuote.$match[3];
        }
        return $text;
    }

    /**
     * @param $string
     * @return bool
     */
    public static function isLatinString($string)
    {
        return boolval(preg_match_all("/^[\p{Latin}\p{Common}]+$/u", $string));
        //return !$noLatin;
    }

    /**
     * @param $string
     * @return bool
     */
    public static function isCyrillicString($string)
    {
        return boolval(preg_match("/^[\p{Cyrillic}\p{Common}]+$/u", $string));
    }

    /**
     * @param $string
     * @return bool
     */
    public static function isAsianString($string)
    {
        return boolval(preg_match("/^[\p{Han}\s\p{P}]*$/u", $string));
    }

    /**
     * removes all kind of brackets from a given string
     * @param $datePart
     * @return mixed
     */
    public static function removeBrackets($datePart)
    {
        return str_replace(["[", "]", "(", ")", "{", "}"], "", $datePart);
    }
}
