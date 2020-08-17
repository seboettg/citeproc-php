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
        $this->runTestSuite("locator_SimpleLocators");
    }

    public function testLocatorConditionConstraints()
    {
        $this->runTestSuite("locator_LocatorConditionConstraint");
    }

    public function testLocatorTermSelection()
    {
        $this->runTestSuite("locator_TermSelection");
    }

    public function testLocatorWithLeadingSpace()
    {
        $this->runTestSuite("locator_WithLeadingSpace");
    }

    public function testLocatorDelimiterAndCitationNumber()
    {
        $this->runTestSuite("locator_DelimiterAndCitationNumber");
    }

    public function testVariableLocatorCondition()
    {
        $this->runTestSuite("locator_VariableLocatorCondition");
    }
}
