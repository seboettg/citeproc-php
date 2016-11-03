<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

class NumberTest extends \PHPUnit_Framework_TestCase
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
            $this->assertEquals($dec, Number::roman2Dec($roman));
        }
    }
}
