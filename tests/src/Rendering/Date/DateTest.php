<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class DateTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    public function testDateString()
    {
        $this->runTestSuite("date_String");
    }

    public function testNoDate()
    {
        $this->runTestSuite("date_NoDate");
    }

    public function testLocalizedNumericYear()
    {
        $this->runTestSuite("date_LocalizedNumericYear");
    }

    public function testLocalizedNumericDefaultWithAffixes()
    {
        $this->runTestSuite("date_LocalizedNumericDefaultWithAffixes");
    }

//    public function testLocalizedNumericDefaultWithMissingDay()
//    {
//        $this->_testRenderTestSuite("date_LocalizedNumericDefaultWithMissingDay");
//    }

    public function testLocalizedNumericDefault()
    {
        $this->runTestSuite("date_LocalizedNumericDefault");
    }

    public function testLocalizedDateFormats()
    {
        $this->runTestSuite("date_LocalizedDateFormats-");
    }

    public function testLongMonth()
    {
        $this->runTestSuite("date_LongMonth");
    }

    public function testDateRanges()
    {
        $this->runTestSuite("date_ranges");
    }

    public function testRawParseSimpleDate()
    {
        $this->runTestSuite("date_RawParseSimpleDate");
    }

    public function testDateUncertain()
    {
        $this->runTestSuite("date_Uncertain");
    }

    public function testCondition_EmptyIsUncertainDate()
    {
        $this->runTestSuite("condition_EmptyIsUncertain");
    }

    public function testBugfixDateparts()
    {
        $this->runTestSuite("bugfix-dateparts");
    }
    /*
    public function testDateAD()
    {
        $this->_testRenderTestSuite("date_DateAD");
    }

    public function testDateBC()
    {
        $this->_testRenderTestSuite("date_DateBC");
    }
    */
}
