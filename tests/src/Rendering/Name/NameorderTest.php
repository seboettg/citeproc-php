<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Test\Rendering\Name;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\Test\TestSuiteTestCaseTrait;

class NameorderTest extends TestCase
{
    use TestSuiteTestCaseTrait;


    public function testNameorderLong()
    {
        $this->runTestSuite("nameorder_Long.json");
    }

    public function testNameorderLongNameAsSortDemoteDisplayAndSort()
    {
        $this->runTestSuite("nameorder_LongNameAsSortDemoteDisplayAndSort");
    }

    public function testNameorderLongNameAsSortDemoteNever()
    {
        $this->runTestSuite("nameorder_LongNameAsSortDemoteNever");
    }

    public function testNameorderShort()
    {
        $this->runTestSuite("nameorder_Short.json");
    }

    public function testNameorder_ShortDemoteDisplayAndSort()
    {
        $this->runTestSuite("nameorder_ShortDemoteDisplayAndSort");
    }

    public function testNameorderShortNameAsSortDemoteNever()
    {
        $this->runTestSuite("nameorder_ShortNameAsSortDemoteNever");
    }
}