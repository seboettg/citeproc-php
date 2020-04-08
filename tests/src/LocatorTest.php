<?php
/*
 * citeproc-php: LocatorTest.php
 * User: Sebastian Böttger <seboettg@gmail.com>
 * created at 07.04.20, 18:49
 */

namespace Seboettg\CiteProc;

use PHPUnit\Framework\TestCase;

class LocatorTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    public function testSimpleLocators()
    {
        $this->_testRenderTestSuite("locator_SimpleLocators");
    }

    public function testLocatorConditionConstraints()
    {
        $this->_testRenderTestSuite("locator_LocatorConditionConstraint");
    }
}
