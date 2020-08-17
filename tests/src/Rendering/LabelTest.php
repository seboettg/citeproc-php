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
        $this->runTestSuite("name_CollapseRoleLabels");
    }

    public function testLabelEmptyLabelVanish()
    {
        $this->runTestSuite("label_EmptyLabelVanish");
    }

    public function testLabelImplicitForm()
    {
        $this->runTestSuite("label_Implicit");
    }

    public function testLabelNoFirstCharCap()
    {
        $this->runTestSuite("label_NoFirstCharCap");
    }

    public function testLabelNonexistentNameVariableLabel()
    {
        $this->runTestSuite("label_NonexistentNameVariableLabel");
    }

    public function testLabelPluralPagesWithAlphaPrefix()
    {
        $this->runTestSuite("label_PluralPagesWithAlphaPrefix");
    }
}
