<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

/**
 * Class NameDemoteNonDroppingParticleTest
 * @package src\Seboettg\CiteProc\Rendering\Name
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
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