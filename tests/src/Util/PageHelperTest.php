<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\Style\Options\PageRangeFormats;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class PageHelperTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    private $chicago;
    private $minimal;
    private $minimalTwo;
    private $expanded;

    public function setUp()
    {
        parent::setUp();
        $this->chicago = new PageRangeFormats(PageRangeFormats::CHICAGO);
        $this->minimal = new PageRangeFormats(PageRangeFormats::MINIMAL);
        $this->minimalTwo = new PageRangeFormats(PageRangeFormats::MINIMAL_TWO);
        $this->expanded = new PageRangeFormats(PageRangeFormats::EXPANDED);
    }

    public function testProcessPageRangeFormatsChicago()
    {
        $this->assertEquals(
            "3-10",
            PageHelper::processPageRangeFormats(
                explode("-", "3-10"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "71-72",
            PageHelper::processPageRangeFormats(
                explode("-", "71-72"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "100-104",
            PageHelper::processPageRangeFormats(
                explode("-", "100-104"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "1100-1123",
            PageHelper::processPageRangeFormats(
                explode("-", "1100-1123"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "107-8",
            PageHelper::processPageRangeFormats(
                explode("-", "107-108"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "505-17",
            PageHelper::processPageRangeFormats(
                explode("-", "505-517"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "1002-6",
            PageHelper::processPageRangeFormats(
                explode("-", "1002-1006"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "321-25",
            PageHelper::processPageRangeFormats(
                explode("-", "321-325"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "415-532",
            PageHelper::processPageRangeFormats(
                explode("-", "415-532"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "11564-68",
            PageHelper::processPageRangeFormats(
                explode("-", "11564-11568"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "13792-803",
            PageHelper::processPageRangeFormats(
                explode("-", "13792-13803"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "1496-1504",
            PageHelper::processPageRangeFormats(
                explode("-", "1496-1504"),
                $this->chicago
            )
        );

        $this->assertEquals(
            "2787-2816",
            PageHelper::processPageRangeFormats(
                explode("-", "2787-2816"),
                $this->chicago
            )
        );
    }

    public function testProcessPageRangeFormatsExpanded()
    {
        $this->assertEquals(
            "42-45",
            PageHelper::processPageRangeFormats(
                explode("-", "42-45"),
                $this->expanded
            )
        );

        $this->assertEquals(
            "321-328",
            PageHelper::processPageRangeFormats(
                explode("-", "321-328"),
                $this->expanded
            )
        );

        $this->assertEquals(
            "2787-2816",
            PageHelper::processPageRangeFormats(
                explode("-", "2787-2816"),
                $this->expanded
            )
        );
    }

    public function testProcessPageRangeFormatsMinimal()
    {
        $this->assertEquals(
            "42-5",
            PageHelper::processPageRangeFormats(
                explode("-", "42-45"),
                $this->minimal
            )
        );

        $this->assertEquals(
            "42-55",
            PageHelper::processPageRangeFormats(
                explode("-", "42-55"),
                $this->minimal
            )
        );

        $this->assertEquals(
            "321-8",
            PageHelper::processPageRangeFormats(
                explode("-", "321-328"),
                $this->minimal
            )
        );

        $this->assertEquals(
            "2787-816",
            PageHelper::processPageRangeFormats(
                explode("-", "2787-2816"),
                $this->minimal
            )
        );
    }

    public function testProcessPageRangeFormatsMinimalTwo()
    {
        $this->assertEquals(
            "42-45",
            PageHelper::processPageRangeFormats(
                explode("-", "42-45"),
                $this->minimalTwo
            )
        );

        $this->assertEquals(
            "342-51",
            PageHelper::processPageRangeFormats(
                explode("-", "342-351"),
                $this->minimalTwo
            )
        );
    }

    public function testPageRangeFormatCitationChicago()
    {
        $this->runTestSuite("page_ChicagoAuthorDateLooping");
    }
}
