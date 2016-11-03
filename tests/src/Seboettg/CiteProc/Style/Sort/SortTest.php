<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style\Sort;


use PHPUnit_Framework_ExpectationFailedException;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;
use Seboettg\CiteProc\TestSuiteTests;

class SortTest extends \PHPUnit_Framework_TestCase implements TestSuiteTests
{
    use TestSuiteTestCaseTrait;

    public function testRenderTestSuite()
    {
        $this->_testRenderTestSuite("sort_CiteGroupDelimiter");
    }
}
