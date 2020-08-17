<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class DisplayTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    public function testDisplayAuthorAsHeading()
    {
        $this->runTestSuite("display_AuthorAsHeading");
    }

    public function testDisplayBlock()
    {
        $this->runTestSuite("display_DisplayBlock");
    }

    public function testDisplaySecondFieldAlignClone()
    {
        $this->runTestSuite("display_SecondFieldAlignClone");
    }

    public function testDisplayFieldAlignMigratePunctuation()
    {
        $this->runTestSuite("display_SecondFieldAlignMigratePunctuation");
    }
}
