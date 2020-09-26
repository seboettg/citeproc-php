<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

use Closure;

/**
 * Class Number
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class NumberHelper
{

    const PATTERN_ORDINAL = "/\d+(st|nd|rd|th)?\.?$/";

    const PATTERN_ROMAN = "/^[ivxlcdm]+\.?$/i";

    const PATTERN_ROMAN_RANGE = "/^([ivxlcdm]+\.*)\s*([*\–\-&+,;])\s*([ivxlcdm]+\.?)$/i";

    const PATTERN_AFFIXES = "/^[a-z]?\d+[a-z]?$/i";

    const PATTERN_COMMA_AMPERSAND_RANGE = "/\d*([\s?\-&+,;\s])+\d+/";

    const ROMAN_NUMERALS = [
        ["", "i", "ii", "iii", "iv", "v", "vi", "vii", "viii", "ix"],
        ["", "x", "xx", "xxx", "xl", "l", "lx", "lxx", "lxxx", "xc"],
        ["", "c", "cc", "ccc", "cd", "d", "dc", "dcc", "dccc", "cm"],
        ["", "m", "mm", "mmm", "mmmm", "mmmmm"]
    ];

    const ROMAN_DIGITS = [
        "M" => 1000,
        "D" => 500,
        "C" => 100,
        "L" => 50,
        "X" => 10,
        "V" => 5,
        "I" => 1
    ];

    /**
     * @return Closure
     */
    public static function getCompareNumber()
    {
        return function ($numA, $numB, $order) {
            if (is_numeric($numA) && is_numeric($numB)) {
                $ret = $numA - $numB;
            } else {
                $ret = strcasecmp($numA, $numB);
            }
            if ("descending" === $order) {
                return $ret > 0 ? -1 : 1;
            }
            return $ret > 0 ? 1 : -1;
        };
    }

    /**
     * @param $num
     * @return string
     */
    public static function dec2roman($num)
    {
        $ret = "";
        if ($num < 6000) {
            $numStr = strrev($num);
            $len = strlen($numStr);
            for ($pos = 0; $pos < $len; $pos++) {
                $n = $numStr[$pos];
                $ret = self::ROMAN_NUMERALS[$pos][$n] . $ret;
            }
        }
        return $ret;
    }

    /**
     * @param $romanNumber
     * @return int|mixed
     */
    public static function roman2Dec($romanNumber)
    {
        $romanNumber = trim($romanNumber);
        if (is_numeric($romanNumber)) {
            return 0;
        }

        $values = [];
        // Convert the string to an array of roman values:
        for ($i = 0; $i < mb_strlen($romanNumber); ++$i) {
            $char = mb_strtoupper($romanNumber[$i]);
            if (isset(self::ROMAN_DIGITS[$char])) {
                $values[] = self::ROMAN_DIGITS[$char];
            }
        }

        $sum = 0;
        while ($current = current($values)) {
            $next = next($values);
            $next > $current ? $sum += $next - $current + 0 * next($values) : $sum += $current;
        }
        return $sum;
    }

    /**
     * @param $str
     * @return bool
     */
    public static function isRomanNumber($str)
    {
        $number = trim($str);
        for ($i = 0; $i < mb_strlen($number); ++$i) {
            $char = mb_strtoupper($number[$i]);
            if (!in_array($char, array_keys(self::ROMAN_DIGITS))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $str
     * @return string
     */
    public static function evaluateStringPluralism($str)
    {
        $plural = 'single';
        if (!empty($str)) {
            $isRange = self::isRange($str);
            if ($isRange) {
                return 'multiple';
            } else {
                if (is_numeric($str) || NumberHelper::isRomanNumber($str)) {
                    return 'single';
                }
            }
        }
        return $plural;
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function extractNumber($string)
    {
        if (preg_match("/(\d+)[^\d]*$/", $string, $match)) {
            return $match[1];
        }
        return $string;
    }

    /**
     * @param $str
     * @return array[]|false|string[]
     */
    public static function splitByRangeDelimiter($str)
    {
        return preg_split("/[-–&,]/", $str);
    }

    /**
     * @param string $str
     * @return bool
     */
    private static function isRange($str)
    {
        $rangeParts = self::splitByRangeDelimiter($str);
        $isRange = false;
        if (count($rangeParts) > 1) {
            $isRange = true;
            foreach ($rangeParts as $range) {
                if (NumberHelper::isRomanNumber(trim($range)) || is_numeric(trim($range))) {
                    $isRange = $isRange && true;
                }
            }
        }
        return $isRange;
    }

    /**
     * @param int|string $number
     * @return bool
     */
    public static function isRomanRange($number)
    {
        return preg_match(self::PATTERN_ROMAN_RANGE, $number);
    }
}
