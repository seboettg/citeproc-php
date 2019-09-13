<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class AffixesTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    public function testAffixNumber()
    {
        $this->_testRenderTestSuite("number_affixes");
    }

    public function testAffixBrackets()
    {
        $this->_testRenderTestSuite('affix_Brackets');
    }

    public function testAffixInterveningEmpty()
    {
        $this->_testRenderTestSuite('affix_InterveningEmpty');
    }

    public function testAffixPrefixFullCitationTextOnly()
    {
        $this->_testRenderTestSuite('affix_PrefixFullCitationTextOnly');
    }

    public function testAffixPrefixWithDecorations()
    {
        $this->_testRenderTestSuite('affix_PrefixWithDecorations');
    }

    public function testAffixTextNodeWithMacro()
    {
        $this->_testRenderTestSuite('affix_TextNodeWithMacro');
    }
}
