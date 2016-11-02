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

namespace Seboettg\CiteProc\Rendering\Name;


class NameTest extends \PHPUnit_Framework_TestCase
{


    public function testGetOptions()
    {
        $xml = "<name delimiter=\"; \" form=\"short\" name-as-sort-order=\"all\" sort-separator=\", \" and=\"symbol\" et-al-min=\"4\" et-al-use-first=\"2\"/>";

        $name = new Name(new \SimpleXMLElement($xml), new Names(new \SimpleXMLElement("<names />")));

        $options = $name->getOptions();

        $this->assertEquals("; ", $options['delimiter']);
        $this->assertEquals("short", $options['form']);
        $this->assertEquals("all", $options['name-as-sort-order']);
        $this->assertEquals(", ", $options['sort-separator']);
        $this->assertEquals("symbol", $options['and']);
        $this->assertEquals("4", $options['et-al-min']);
        $this->assertEquals("2", $options['et-al-use-first']);
        $this->assertArrayNotHasKey("parent", $options);
        $this->assertArrayNotHasKey("namePart", $options);
    }
}
