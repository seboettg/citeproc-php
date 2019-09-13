<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;


use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class DisplayTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    public function testDisplayAuthorAsHeading()
    {
        $this->_testRenderTestSuite("display_AuthorAsHeading");
    }

    public function testDisplayBlock()
    {
        $this->_testRenderTestSuite("display_DisplayBlock");
    }

    public function testDisplaySecondFieldAlignClone()
    {
        $this->_testRenderTestSuite("display_SecondFieldAlignClone");
    }

    public function testDisplayFieldAlignMigratePunctuation()
    {
        $this->_testRenderTestSuite("display_SecondFieldAlignMigratePunctuation");
    }
}