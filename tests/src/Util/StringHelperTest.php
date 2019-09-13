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

    public function testIsLatinString()
    {
        $this->assertFalse(StringHelper::isLatinString("栄我妻"));
        $this->assertFalse(StringHelper::isLatinString("栄我 妻"));
        $this->assertFalse(StringHelper::isLatinString("栄我妻 Hello World.!¡"));
        $this->assertTrue(StringHelper::isLatinString("Hello World"));
        $this->assertFalse(StringHelper::isLatinString("АаБбВвГг"));
        $this->assertFalse(StringHelper::isLatinString("HАllo"));
    }

    public function testIsCyrillicString()
    {
        $this->assertFalse(StringHelper::isCyrillicString("栄我妻"));
        $this->assertFalse(StringHelper::isCyrillicString("栄我 妻"));
        $this->assertFalse(StringHelper::isCyrillicString("Hallo Welt оформления"));
        $this->assertTrue(StringHelper::isCyrillicString("Пример чиком. : чиком!"));
        $this->assertFalse(StringHelper::isCyrillicString("Nicht"));
        $this->assertFalse(StringHelper::isCyrillicString("пеpеводчиком"));
    }
}