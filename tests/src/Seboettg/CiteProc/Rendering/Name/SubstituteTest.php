<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace src\Seboettg\CiteProc\Rendering\Name;

use Seboettg\CiteProc\TestSuiteTestCaseTrait;


class SubstituteTest extends \PHPUnit_Framework_TestCase
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

    public function testNameSubstituteOnElementFail()
    {
        //TODO: implement
        //$this->_testRenderTestSuite("name_SubstituteOn");
    }

}
