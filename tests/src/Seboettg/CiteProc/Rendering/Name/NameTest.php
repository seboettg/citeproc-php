<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;


use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class NameTest extends \PHPUnit_Framework_TestCase
{

    use TestSuiteTestCaseTrait;

    public function testNameAttrAnd()
    {
        $this->_testRenderTestSuite("nameattr_And");
    }

    public function testNameAttrDelimiterPrecedesEtAl()
    {
        $this->_testRenderTestSuite("nameattr_DelimiterPrecedesEtAl");
    }

    public function testNameAttrDelimiterPrecedesLast()
    {
        $this->_testRenderTestSuite("nameattr_DelimiterPrecedesLast");
    }

    public function testNameAttrEtAlMin()
    {
        $this->_testRenderTestSuite("nameattr_EtAlMin");
    }

    public function testNameAttrEtAlUseFirst()
    {
        $this->_testRenderTestSuite("nameattr_EtAlUseFirst");
    }

    public function testNameAttrNameDelimiter()
    {
        $this->_testRenderTestSuite("nameattr_NameDelimiter");
    }

    public function testNameAttrNameForm()
    {
        $this->_testRenderTestSuite("nameattr_NameForm");
    }

    public function testNameAttrNameAsSortOrder()
    {
        $this->_testRenderTestSuite("nameattr_NameAsSortOrder");
    }

    public function testNameAttrSortSeparator()
    {
        $this->_testRenderTestSuite("nameattr_SortSeparator");
    }

    public function testNameAttrInitializeWith()
    {
        $this->_testRenderTestSuite("nameattr_InitializeWith");
    }

    public function testNameAttrEtAlSubsequentMin()
    {
        $this->_testRenderTestSuite("nameattr_EtAlSubsequentMin");
    }

    public function testNameAttrEtAlSubsequentUseFirst()
    {
        $this->_testRenderTestSuite("nameattr_EtAlSubsequentUseFirst");
    }

    public function testNameAttrEtAlSubsequentCompleteAll()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_complete-all");
    }

    public function testNameAttrEtAlSubsequentCompleteEach()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_complete-each");
    }

    public function testNameAttrEtAlSubsequentNone()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_none");
    }

    public function testNameAttrEtAlSubsequentPartialEach()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_partial-each");
    }

    public function testNameAttrEtAlSubsequentPartialFirst()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_partial-first");
    }

    public function testFormatSmallCaps()
    {
        $this->_testRenderTestSuite("name_FormatSmallCaps");
    }
}
