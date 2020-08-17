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
        $this->runTestSuite("number_affixes");
    }

    public function testAffixBrackets()
    {
        $this->runTestSuite('affix_Brackets');
    }

    public function testAffixInterveningEmpty()
    {
        $this->runTestSuite('affix_InterveningEmpty');
    }

    public function testAffixPrefixFullCitationTextOnly()
    {
        $this->runTestSuite('affix_PrefixFullCitationTextOnly');
    }

    public function testAffixPrefixWithDecorations()
    {
        $this->runTestSuite('affix_PrefixWithDecorations');
    }

    public function testAffixTextNodeWithMacro()
    {
        $this->runTestSuite('affix_TextNodeWithMacro');
    }
}
