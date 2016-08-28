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

namespace Seboettg\CiteProc\Rendering;


use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\Rendering\Date\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $context = new Context();
        $context->setLocale(new Locale("en-GB"));
        CiteProc::setContext($context);
    }

    private $data = "{\"id\": \"ITEM-2\", \"issued\": {\"date-parts\": [\"1983\", \"1\", \"15\"]}, \"title\": \"Item 2\", \"type\": \"book\"}";

    public function testRenderDateParts()
    {
        $xml = "
              <date variable=\"issued\" form=\"numeric-leading-zeros\">
                <date-part prefix=\" \" suffix=\".\" name=\"day\"/>
                <date-part suffix=\".\" name=\"month\"/>
                <date-part name=\"year\"/>
              </date>";

        $date = new Date(new \SimpleXMLElement($xml));
        $ret = $date->render(json_decode($this->data));

        $this->assertEquals(" 15.01.1983", $ret);

    }
}
