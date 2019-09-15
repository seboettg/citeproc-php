<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;


class SubstituteTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    public function testNameSubstituteInheritLabel()
    {
        $this->_testRenderTestSuite("name_SubstituteInheritLabel");
    }

    public function testNameSubstituteMacro()
    {
        $this->_testRenderTestSuite("name_SubstituteMacro");
    }

    public function testNameSubstituteName()
    {
        $this->_testRenderTestSuite("name_SubstituteName");
    }

    public function testRenderSubstituteSuppressMultipleValues()
    {
        $this->_testRenderTestSuite("names_substituteSuppressSubstitution");
    }

    public function testNameSubstituteSuppressOrdinaryVariable()
    {
        $this->_testRenderTestSuite("name_substitute_SuppressOrdinaryVariable");
    }

    public function testNameSubstituteOnElementFail()
    {
        $this->_testRenderTestSuite("name_SubstituteOnDateGroupSpanFail");
        $this->_testRenderTestSuite("name_SubstituteOnGroupSpanGroupSpanFail");
        $this->_testRenderTestSuite("name_SubstituteOnMacroGroupSpanFail");
        $this->_testRenderTestSuite("name_SubstituteOnNamesSingletonGroupSpanFail.json");
        $this->_testRenderTestSuite("name_SubstituteOnNamesSpanNamesSpanFail.json");
        $this->_testRenderTestSuite("name_SubstituteOnNumberGroupSpanFail");
    }

}
