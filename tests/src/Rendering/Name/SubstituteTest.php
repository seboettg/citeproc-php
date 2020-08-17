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
        $this->runTestSuite("name_SubstituteInheritLabel");
    }

    public function testNameSubstituteMacro()
    {
        $this->runTestSuite("name_SubstituteMacro");
    }

    public function testNameSubstituteName()
    {
        $this->runTestSuite("name_SubstituteName");
    }

    public function testRenderSubstituteSuppressMultipleValues()
    {
        $this->runTestSuite("names_substituteSuppressSubstitution");
    }

    public function testNameSubstituteSuppressOrdinaryVariable()
    {
        $this->runTestSuite("name_substitute_SuppressOrdinaryVariable");
    }

    public function testNameSubstituteOnElementFail()
    {
        $this->runTestSuite("name_SubstituteOnDateGroupSpanFail");
        $this->runTestSuite("name_SubstituteOnGroupSpanGroupSpanFail");
        $this->runTestSuite("name_SubstituteOnMacroGroupSpanFail");
        $this->runTestSuite("name_SubstituteOnNamesSingletonGroupSpanFail.json");
        $this->runTestSuite("name_SubstituteOnNamesSpanNamesSpanFail.json");
        $this->runTestSuite("name_SubstituteOnNumberGroupSpanFail");
    }

}
