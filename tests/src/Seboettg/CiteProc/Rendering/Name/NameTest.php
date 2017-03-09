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
        //$this->_testRenderTestSuite("nameattr_And");
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

    public function testNameAttrInitializeWith()
    {
        $this->_testRenderTestSuite("nameattr_InitializeWith");
    }
}
