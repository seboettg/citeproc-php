<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class NameTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    public function testNameArticularNameAsSortOrder()
    {
        $this->_testRenderTestSuite("name_ArticularNameAsSortOrder");
    }

    public function testNameArticularPlain()
    {
        $this->_testRenderTestSuite("name_ArticularPlain");
    }

    public function testNameArticularShortForm()
    {
        $this->_testRenderTestSuite("name_ArticularShortForm.json");
    }

    public function testNameAsianGlyphs()
    {
        $this->_testRenderTestSuite("name_AsianGlyphs");
    }

    public function testNameAuthorCount()
    {
        $this->_testRenderTestSuite("name_AuthorCount.json");
    }

    public function testNameAuthorCountWithMultipleVariables()
    {
        $this->_testRenderTestSuite("name_AuthorCountWithMultipleVariables");
    }

    public function testNameAuthorCountWithSameVarContentAndCombinedTermFail()
    {
        $this->_testRenderTestSuite("name_AuthorCountWithSameVarContentAndCombinedTermFail");
    }

    public function testNameAuthorCountWithSameVarContentAndCombinedTermSucceed()
    {
        $this->_testRenderTestSuite("name_AuthorCountWithSameVarContentAndCombinedTermSucceed");
    }

    public function testNameAuthorEditorCount()
    {
        $this->_testRenderTestSuite("name_AuthorEditorCount");
    }

    public function testNameCelticClanName()
    {
        $this->_testRenderTestSuite("name_CelticClanName");
    }

    public function testNameCeltsAndToffsCrowdedInitials()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsCrowdedInitials");
    }

    public function testNameCeltsAndToffsNoHyphens()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsNoHyphens");
    }

    public function testNameCeltsAndToffsSpacedInitials()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsSpacedInitials");
    }

    public function testNameCeltsAndToffsWithHyphens()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsWithHyphens");
    }

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

    public function testNameNamepartAffixes()
    {
        $this->_testRenderTestSuite("name_namepartAffixes.json");
    }

    public function testNameNamepartAffixesNameAsSortOrder()
    {
        $this->_testRenderTestSuite("name_namepartAffixesNameAsSortOrder");
    }
}
