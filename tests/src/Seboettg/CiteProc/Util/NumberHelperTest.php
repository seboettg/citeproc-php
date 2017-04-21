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

class NumberHelperTest extends TestCase
{

    public function testRoman2Dec()
    {
        $test = [
            "I"     => 1,
            "iv"    => 4,
            "viii"  => 8,
            "XVII"  => 17,
            "XC"    => 90,
            "CI"    => 101,
            "D"     => 500,
            "dviii" => 508,
            "CM"    => 900,
            "XM"    => 990,
            "M"     => 1000,
            "MMXVI" => 2016
        ];

        foreach ($test as $roman => $dec) {
            $this->assertEquals($dec, NumberHelper::roman2Dec($roman));
        }
    }

    public function testIsRomanNumber()
    {
        $this->assertTrue(NumberHelper::isRomanNumber("xiv"));
        $this->assertTrue(NumberHelper::isRomanNumber("XIV"));
        $this->assertFalse(NumberHelper::isRomanNumber("ash"));
        $this->assertFalse(NumberHelper::isRomanNumber("ASH"));
        $this->assertFalse(NumberHelper::isRomanNumber("asd"));
        $this->assertFalse(NumberHelper::isRomanNumber("MAD"));
    }

    public function testEvaluateStringPluralism()
    {
        $this->assertEquals("single", NumberHelper::evaluateStringPluralism("999"));
        $this->assertEquals("single", NumberHelper::evaluateStringPluralism("MMXVI"));
        $this->assertEquals("multiple", NumberHelper::evaluateStringPluralism("3-9"));
        $this->assertEquals("multiple", NumberHelper::evaluateStringPluralism("iii-ix"));
        $this->assertEquals("multiple", NumberHelper::evaluateStringPluralism("iii & ix"));
        //$this->assertEquals("multiple", Number::evaluateStringPluralism("S123–S125"));
    }

}
