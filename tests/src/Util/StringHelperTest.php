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
        static::assertEquals("lower-camel-case", StringHelper::camelCase2Hyphen("lowerCamelCase"));
        static::assertEquals("upper-camel-case", StringHelper::camelCase2Hyphen("UpperCamelCase"));
        static::assertEquals("up-per-cam-el-ca-se", StringHelper::camelCase2Hyphen("Up-perCam-elCa-se"));
    }

    public function testCheckUpperCase()
    {
        static::assertTrue(StringHelper::checkUpperCaseString("HALLO WELT"));
        static::assertFalse(StringHelper::checkUpperCaseString("hallo welt"));
        static::assertTrue(StringHelper::checkUpperCaseString("ÄTHIOPIEN"));
        static::assertFalse(StringHelper::checkUpperCaseString("äTHIOPIEN"));
    }

    public function testCheckLowerCase()
    {
        static::assertFalse(StringHelper::checkLowerCaseString("HALLO WELT"));
        static::assertTrue(StringHelper::checkLowerCaseString("hallo welt"));
        static::assertFalse(StringHelper::checkLowerCaseString("Äthiopien"));
        static::assertTrue(StringHelper::checkLowerCaseString("äthiopien"));
    }

    public function testReplaceOuterQuotes()
    {
        $string = "Getting Property Right: “Informal” Mortgages in the Japanese Courts";
        $actual = StringHelper::replaceOuterQuotes($string, "“", "”", "‘", "’");
        static::assertEquals("Getting Property Right: ‘Informal’ Mortgages in the Japanese Courts", $actual);

        $string = "Getting Property Right: \"Informal\" Mortgages in the Japanese Courts";
        $actual = StringHelper::replaceOuterQuotes($string, "\"", "\"", "‘", "’");
        static::assertEquals("Getting Property Right: ‘Informal’ Mortgages in the Japanese Courts", $actual);
    }

    public function testIsLatinString()
    {
        static::assertFalse(StringHelper::isLatinString("栄我妻"));
        static::assertFalse(StringHelper::isLatinString("栄我 妻"));
        static::assertFalse(StringHelper::isLatinString("栄我妻 Hello World.!¡"));
        static::assertTrue(StringHelper::isLatinString("Hello World"));
        static::assertFalse(StringHelper::isLatinString("АаБбВвГг"));
        static::assertFalse(StringHelper::isLatinString("HАllo"));
    }

    public function testIsCyrillicString()
    {
        static::assertFalse(StringHelper::isCyrillicString("栄我妻"));
        static::assertFalse(StringHelper::isCyrillicString("栄我 妻"));
        static::assertFalse(StringHelper::isCyrillicString("Hallo Welt оформления"));
        static::assertTrue(StringHelper::isCyrillicString("Пример чиком. : чиком!"));
        static::assertFalse(StringHelper::isCyrillicString("Nicht"));
        static::assertFalse(StringHelper::isCyrillicString("пеpеводчиком"));
    }
}
