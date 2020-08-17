<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Names;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class NamesTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    public function setUp()
    {
        parent::setUp();
    }

    public function testRenderSubstitute()
    {
        $this->runTestSuite("names_substitute");
    }

    public function testEditorTranslator()
    {
        $this->runTestSuite("name_EditorTranslatorBoth");
    }

    public function testEtAlUseLast()
    {
        $this->runTestSuite("name_EtAlUseLast");
    }
    /*
    public function testEtAlWithCombined()
    {
        //TODO: implement
        //$this->_testRenderTestSuite("name_EtAlWithCombined");
    }
    */
}
