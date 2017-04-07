<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;
use PHPUnit\Framework\TestCase;

/**
 * Class StringHelper
 * @package src\Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class StringHelperTest extends TestCase
{

    public function testCamelCase2Hyphen()
    {
        $this->assertEquals("lower-camel-case", StringHelper::camelCase2Hyphen("lowerCamelCase"));
        $this->assertEquals("upper-camel-case", StringHelper::camelCase2Hyphen("UpperCamelCase"));
        $this->assertEquals("up-per-cam-el-ca-se", StringHelper::camelCase2Hyphen("Up-perCam-elCa-se"));
    }

    public function testCheckUpperCase()
    {
        $this->assertTrue(StringHelper::checkUpperCaseString("HALLO WELT"));
        $this->assertFalse(StringHelper::checkUpperCaseString("hallo welt"));
        $this->assertTrue(StringHelper::checkUpperCaseString("ÄTHIOPIEN"));
        $this->assertFalse(StringHelper::checkUpperCaseString("äTHIOPIEN"));
    }

    public function testCheckLowerCase()
    {
        $this->assertFalse(StringHelper::checkLowerCaseString("HALLO WELT"));
        $this->assertTrue(StringHelper::checkLowerCaseString("hallo welt"));
        $this->assertFalse(StringHelper::checkLowerCaseString("Äthiopien"));
        $this->assertTrue(StringHelper::checkLowerCaseString("äthiopien"));
    }

    public function testReplaceOuterQuotes()
    {
        $string = "Getting Property Right: “Informal” Mortgages in the Japanese Courts";
        $actual = StringHelper::replaceOuterQuotes($string, "“", "”", "‘", "’");
        $this->assertEquals("Getting Property Right: ‘Informal’ Mortgages in the Japanese Courts", $actual);

        $string = "Getting Property Right: \"Informal\" Mortgages in the Japanese Courts";
        $actual = StringHelper::replaceOuterQuotes($string, "\"", "\"", "‘", "’");
        $this->assertEquals("Getting Property Right: ‘Informal’ Mortgages in the Japanese Courts", $actual);
    }
}