<?php
/*
 * This file is a part of HDS (HeBIS Discovery System). HDS is an 
 * extension of the open source library search engine VuFind, that 
 * allows users to search and browse beyond resources. More 
 * Information about VuFind you will find on http://www.vufind.org
 * 
 * Copyright (C) 2016 
 * Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
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

namespace AcademicPuma\CiteProc;

/**
 * Class HierAttributes
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class HierAttributes
{
    const AND_ = 'and'; 
    const DELIMITER_PRECEDES_LAST = 'delimiter-precedes-last'; 
    const ET_AL_MIN = 'et-al-min'; 
    const ET_AL_USE_FIRST = 'et-al-use-first';
    const ET_AL_SUBSEQUENT_FIRST = 'et-al-subsequent-min'; 
    const ET_AL_SUBSEQUENT_USE_FIRST = 'et-al-subsequent-use-first'; 
    const INITIALIZE_WITH = 'initialize-with';
    const NAME_AS_SORT_ORDER = 'name-as-sort-order'; 
    const SORT_SEPARATOR = 'sort-separator'; 
    const NAME_FORM = 'name-form'; 
    const NAME_DELIMITER = 'name-delimiter';
    const NAMES_DELIMITER = 'names-delimiter';
    

    private static $arr = array();

    public static function getAllAttributes()
    {
        if (empty(self::$arr)) {
            $refClass = new \ReflectionClass('\AcademicPuma\CiteProc\HierAttributes');
            $constants = $refClass->getConstants();
            array_walk($constants, function ($value) {
                self::$arr[$value] = $value;
            });
        }
        return self::$arr;
    }
}