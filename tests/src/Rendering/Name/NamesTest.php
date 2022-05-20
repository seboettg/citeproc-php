<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Test\Rendering\Name;

use Exception;
use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\Name\Names;
use Seboettg\CiteProc\Test\TestSuiteTestCaseTrait;
use SimpleXMLElement;

class NamesTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    public function setUp(): void
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

    /**
     * @throws Exception
     */
    public function testRenderLabelBeforeNameShouldBeTrueIfLabelTagBeforeName()
    {
        $csl = <<<EOD
            <names variable="translator">
              <label form="verb-short" prefix=", "/>
              <name name-as-sort-order="first" and="text" sort-separator=", " delimiter=", " delimiter-precedes-last="always"/>
            </names>
        EOD;
        CiteProc::setContext(new Context());
        $style = new SimpleXMLElement($csl);
        $names = new Names($style, null);
        $this->assertTrue($names->isRenderLabelBeforeName());
    }

    /**
     * @throws InvalidStylesheetException
     * @throws Exception
     */
    public function testRenderLabelBeforeNameShouldBeFalseIfLabelTagAfterName()
    {
        $csl = <<<EOD
            <names variable="translator">
              <name name-as-sort-order="first" and="text" sort-separator=", " delimiter=", " delimiter-precedes-last="always"/>
              <label form="verb-short" prefix=", "/>
            </names>
        EOD;
        CiteProc::setContext(new Context());
        $style = new SimpleXMLElement($csl);
        $names = new Names($style, null);
        $this->assertFalse($names->isRenderLabelBeforeName());
    }
}
