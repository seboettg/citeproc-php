<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;


/**
 * Class StringHelper
 * @package src\Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class StringHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testCamelCase2Hyphen()
    {
        $this->assertEquals("lower-camel-case", StringHelper::camelCase2Hyphen("lowerCamelCase"));
        $this->assertEquals("upper-camel-case", StringHelper::camelCase2Hyphen("UpperCamelCase"));
        $this->assertEquals("up-per-cam-el-ca-se", StringHelper::camelCase2Hyphen("Up-perCam-elCa-se"));
    }
}