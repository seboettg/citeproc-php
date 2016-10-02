<?php
/*
 * This file is a part of HDS (HeBIS Discovery System). HDS is an 
 * extension of the open source library search engine VuFind, that 
 * allows users to search and browse beyond resources. More 
 * Information about VuFind you will find on http://www.vufind.org
 * 
 * Copyright (C) 2016 
 * HeBIS Verbundzentrale des HeBIS-Verbundes 
 * Goethe-Universität Frankfurt / Goethe University of Frankfurt
 * http://www.hebis.de
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Seboettg\CiteProc\Util;


/**
 * Class Number
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Number
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

    /**
     * @return \Closure
     */
    public static function getCompareNumber()
    {
        return function ($numA, $numB, $order) {
            if (is_numeric($numA) && is_numeric($numB)) {
                $ret = $numA - $numB;
            } else {
                $ret = strcmp($numA, $numB);
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

    /*
    public static function roman2dec($roman)
    {

        $preDecimal = [1, 10, 100, 1000];

        $roman = strtolower($roman);
        //XVII
        $ret = 0;
        for ($i = strlen($roman); $i > 0;) {
            $numberFound = false;
            for ($j = 0; $j < $i && $j < strlen($roman);) {
                $char = substr($roman, $j, ($i - $j));

                for ($k = 0; $k < 4; ++$k) {
                    if (($pos = array_search($char, self::ROMAN_NUMERALS[$k])) !== false) {
                        $ret = ($preDecimal[$k] * $pos) + $ret;
                        $i = $j;
                        $j = 0;
                        $numberFound = true;
                        break;
                    }
                }
                if (!$numberFound) {
                    ++$j;
                }
            }
            if (!$numberFound) {
                --$i;
            }
        }
        $x = "foo";
        return $ret;
    }
    */

    public static function roman2Dec($romanNumber)
    {

        $roman = [
            "M" => 1000,
            "D" => 500,
            "C" => 100,
            "L" => 50,
            "X" => 10,
            "V" => 5,
            "I" => 1
        ];

        if(is_numeric($romanNumber)) {
            return 0;
        }


        // Convert the string to an array of roman values:
        for ($i = 0; $i < strlen($romanNumber); ++$i) {
            $char = strtoupper($romanNumber{$i});
            if (isset($roman[$char])) {
                $values[] = $roman[$char];
            }
        }

        $sum = 0;
        while($current = current($values)) {
            $next = next($values);
            $next > $current ? $sum += $next - $current + 0 * next($values) : $sum += $current;
        }

        // Return the value:
        return $sum;
    }

}