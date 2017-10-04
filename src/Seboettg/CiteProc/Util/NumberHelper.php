<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

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
     * @return \Closure
     */
    public static function getCompareNumber()
    {
        return function($numA, $numB, $order) {
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
        if (is_numeric($romanNumber)) {
            return 0;
        }

        $values = [];
        // Convert the string to an array of roman values:
        for ($i = 0; $i < strlen($romanNumber); ++$i) {
            $char = strtoupper($romanNumber{$i});
            if (self::ROMAN_DIGITS[$char] !== null) {
                $values[] = self::ROMAN_DIGITS[$char];
            }
        }

        $sum = 0;
        while ($current = current($values)) {
            $next = next($values);
            $next > $current ? $sum += $next - $current + 0 * next($values) : $sum += $current;
        }

        // Return the value:
        return $sum;
    }

    public static function isRomanNumber($str)
    {
        for ($i = 0; $i < strlen($str); ++$i) {
            $char = strtoupper($str{$i});
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
            $ranges = preg_split("/[-–&,]/", $str);
            if (count($ranges) > 1) {

                $isRange = 1;
                foreach ($ranges as $range) {
                    if (NumberHelper::isRomanNumber(trim($range)) || is_numeric(trim($range))) {
                        $isRange &= 1;
                    }
                }
                if ($isRange == 1) {
                    return 'multiple';
                }
            } else {
                if (is_numeric($str) || NumberHelper::isRomanNumber($str)) {
                    return 'single';
                }
            }
        }
        return $plural;
    }

    public static function extractNumber($string)
    {
        if (preg_match("/(\d+)[^\d]*$/", $string, $match)) {
            return $match[1];
        }
        return $string;
    }
}