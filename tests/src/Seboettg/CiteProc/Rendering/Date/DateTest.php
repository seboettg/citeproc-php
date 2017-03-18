<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date;


use PHPUnit_Framework_ExpectationFailedException;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;
use Seboettg\CiteProc\TestSuiteTests;

class DateTest extends \PHPUnit_Framework_TestCase
{

    use TestSuiteTestCaseTrait;

    public function testDateString()
    {
        $this->_testRenderTestSuite("date_String");
    }

    public function testNoDate()
    {
        $this->_testRenderTestSuite("date_NoDate");
    }

    public function testLocalizedNumericYear()
    {
        $this->_testRenderTestSuite("date_LocalizedNumericYear");
    }

    public function testLocalizedNumericDefaultWithAffixes()
    {
        $this->_testRenderTestSuite("date_LocalizedNumericDefaultWithAffixes");
    }

    public function testLocalizedNumericDefaultWithMissingDay()
    {
        $this->_testRenderTestSuite("date_LocalizedNumericDefaultWithMissingDay");
    }

    public function testLocalizedNumericDefault()
    {
        $this->_testRenderTestSuite("date_LocalizedNumericDefault");
    }

    public function testLocalizedDateFormats()
    {
        $this->_testRenderTestSuite("date_LocalizedDateFormats-");
    }

    public function testLongMonth()
    {
        $this->_testRenderTestSuite("date_LongMonth");
    }

    public function testDateRanges()
    {
        $this->_testRenderTestSuite("date_ranges");
    }

    public function testRawParseSimpleDate()
    {
        $this->_testRenderTestSuite("date_RawParseSimpleDate");
    }


}
