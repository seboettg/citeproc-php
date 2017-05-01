<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use PHPUnit\Framework\TestCase;


class CiteProcTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    /**
     * @var array
     */
    private $dataObj;

    /**
     * @var CiteProc
     */
    private $citeProc;

    public function setUp()
    {
        //parent::setU
    }

    public function testFullStyleBibliography1()
    {
        $this->_testRenderTestSuite("fullstyles_APABibliography");
    }

    public function testFullStyleMultipleAuthors()
    {
        $this->_testRenderTestSuite("fullstyles_APA_MultipleAuthors");
    }

    public function testFullStyleDINBibliography()
    {
        $this->_testRenderTestSuite("fullstyles_DINBibliography");
    }

    public function testFullStyleInternationalJournalOfHumanoidRobotics()
    {
        $this->_testRenderTestSuite("fullstyles_InternationalJournalOfHumanoid");
    }

    public function testFullStyleNorthWestUniversityHarvard()
    {
        $this->_testRenderTestSuite("fullstyles_NorthWestUniversityHarvard");
    }


    public function testRenderCssStyle()
    {
        $style = StyleSheet::loadStyleSheet("international-journal-of-humanoid-robotics");
        $citeProc = new CiteProc($style);
        $cssStyles = $citeProc->renderCssStyles();

        $this->assertTrue(strpos($cssStyles, "csl-left-margin") !== false);
        $this->assertTrue(strpos($cssStyles, "csl-right-inline") !== false);
    }

    public function testRenderCssStyleHangingIndent()
    {
        $style = StyleSheet::loadStyleSheet("din-1505-2");
        $citeProc = new CiteProc($style);
        $cssStyles = $citeProc->renderCssStyles();
        $this->assertTrue(strpos($cssStyles, "csl-entry") !== false);
        $this->assertTrue(strpos($cssStyles, "text-indent: 45px") !== false);
    }

    public function testRenderCssStyleLineAndEntrySpacing()
    {
        $style = StyleSheet::loadStyleSheet("harvard-north-west-university");
        $citeProc = new CiteProc($style);
        $cssStyles = $citeProc->renderCssStyles();
        $this->assertTrue(strpos($cssStyles, "csl-entry") !== false);
        $this->assertTrue(strpos($cssStyles, "line-height: 1em") !== false);
        $this->assertTrue(strpos($cssStyles, "margin-bottom: 2em") !== false);
    }
}
