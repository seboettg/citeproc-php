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
 * Class StringHelper
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
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
}