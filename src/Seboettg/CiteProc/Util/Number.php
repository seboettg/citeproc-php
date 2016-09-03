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

    /**
     * @return \Closure
     */
    public static function getCompareNumber() {
        return function ($numA, $numB, $order)
        {
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

}