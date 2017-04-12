<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;
use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class LabelTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    /*
    public function testLabelEditorTranslator()
    {
        $this->_testRenderTestSuite("label_EditorTranslator1");
    }
    */

    public function testNameCollapseRoleLables()
    {
        $this->_testRenderTestSuite("name_CollapseRoleLabels");
    }

    public function testLabelEmptyLabelVanish()
    {
        $this->_testRenderTestSuite("label_EmptyLabelVanish");
    }

    public function testLabelImplicitForm()
    {
        $this->_testRenderTestSuite("label_Implicit");
    }

    public function testLabelNoFirstCharCap()
    {
        $this->_testRenderTestSuite("label_NoFirstCharCap");
    }

    public function testLabelNonexistentNameVariableLabel()
    {
        $this->_testRenderTestSuite("label_NonexistentNameVariableLabel");
    }

    public function testLabelPluralPagesWithAlphaPrefix()
    {
        $this->_testRenderTestSuite("label_PluralPagesWithAlphaPrefix");
    }
}
