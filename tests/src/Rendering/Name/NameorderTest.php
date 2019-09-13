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
        $this->_testRenderTestSuite("nameorder_Long.json");
    }

    public function testNameorderLongNameAsSortDemoteDisplayAndSort()
    {
        $this->_testRenderTestSuite("nameorder_LongNameAsSortDemoteDisplayAndSort");
    }

    public function testNameorderLongNameAsSortDemoteNever()
    {
        $this->_testRenderTestSuite("nameorder_LongNameAsSortDemoteNever");
    }

    public function testNameorderShort()
    {
        $this->_testRenderTestSuite("nameorder_Short.json");
    }

    public function testNameorder_ShortDemoteDisplayAndSort()
    {
        $this->_testRenderTestSuite("nameorder_ShortDemoteDisplayAndSort");
    }

    public function testNameorderShortNameAsSortDemoteNever()
    {
        $this->_testRenderTestSuite("nameorder_ShortNameAsSortDemoteNever");
    }
}