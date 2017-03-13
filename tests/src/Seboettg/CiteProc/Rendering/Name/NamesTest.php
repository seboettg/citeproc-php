<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Names;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\Rendering\Group;
use Seboettg\CiteProc\Rendering\Name\Names;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;
use Seboettg\CiteProc\TestSuiteTests;

class NamesTest extends \PHPUnit_Framework_TestCase implements TestSuiteTests
{

    use TestSuiteTestCaseTrait;

    public function setUp()
    {
        parent::setUp();
    }

    public function testRenderSubstitute()
    {
        //TODO: implement
        //$this->_testRenderTestSuite("names_substitute");
    }


    public function testEditorTranslator()
    {
        $this->_testRenderTestSuite("name_EditorTranslatorBoth");
    }

    public function testEtAl()
    {
        //TODO: implement
        //$this->_testRenderTestSuite("name_EtAl");
    }

    public function testRenderTestSuite()
    {
    }
}
