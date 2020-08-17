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
        $this->runTestSuite("fullstyles_APABibliography");
    }

    public function testFullStyleMultipleAuthors()
    {
        $this->runTestSuite("fullstyles_APA_MultipleAuthors");
    }

    public function testFullStyleDINBibliography()
    {
        $this->runTestSuite("fullstyles_DINBibliography");
    }

    public function testFullStyleInternationalJournalOfHumanoidRobotics()
    {
        $this->runTestSuite("fullstyles_InternationalJournalOfHumanoid");
    }

    public function testFullStyleNorthWestUniversityHarvard()
    {
        $this->runTestSuite("fullstyles_NorthWestUniversityHarvard");
    }

    public function testRenderCitationNumber()
    {
        $this->runTestSuite("text_renderCitationNumber");
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
        $this->assertTrue(strpos($cssStyles, "text-indent: -2em") !== false);
    }

    public function testRenderCssStyleLineAndEntrySpacing()
    {
        $style = StyleSheet::loadStyleSheet("harvard-north-west-university");
        $citeProc = new CiteProc($style);
        $cssStyles = $citeProc->renderCssStyles();
        $this->assertTrue(strpos($cssStyles, "csl-entry") !== false);
        $this->assertTrue(strpos($cssStyles, "text-indent: -2em") !== false);
        $this->assertTrue(strpos($cssStyles, "padding-left: 2em") !== false);
    }

    public function testGetInfo()
    {
        $style = StyleSheet::loadStyleSheet("harvard-north-west-university");
        $citeProc = new CiteProc($style);
        $citeProc->init();
        $info = CiteProc::getContext()->getInfo();
        $this->assertEquals("Albi Odendaal", $info->getAuthors()[0]->name);
        $this->assertEquals("North-West University - Harvard", $info->getTitle());
    }

    public function testFilterCitations()
    {
        $style = StyleSheet::loadStyleSheet("harvard-north-west-university");
        $citeProc = new CiteProc($style);

        $dataString = '[
            {
                "author": [
                    {
                        "family": "Doe",
                        "given": "John"
                    }
                ],
                "id": "ITEM-1",
                "issued": {
                    "date-parts":[[2012]]
                },
                "title": "Book 1",
                "type": "book"
            },
            {
                "author": [
                    {
                        "family": "Doe",
                        "given": "Jane"
                    }
                ],
                "issued": {
                    "date-parts":[[2012]]
                },
                "id": "ITEM-2",
                "title": "Book 2",
                "type": "book"
            },
            {
                "author": [
                    {
                        "family": "Doe",
                        "given": "John"
                    }
                ],
                "issued": {
                    "date-parts":[[2011]]
                },
                "id": "ITEM-3",
                "title": "Book 3",
                "type": "book"
            }
        ]';

        $actual = $citeProc->render(json_decode($dataString), "citation");
        $expected = '(Doe, 2011; Doe, 2012; Doe, 2012)';
        $this->assertEquals($expected, $actual);

        $filter = '[{"id": "ITEM-1"}]';
        $actualFiltered = $citeProc->render(json_decode($dataString), "citation", json_decode($filter));
        $expectedFiltered = '(Doe, 2012)';
        $this->assertEquals($actualFiltered, $expectedFiltered);

        $citeProc = new CiteProc(StyleSheet::loadStyleSheet("elsevier-vancouver"));

        $actualFilteredElsevier = $citeProc->render(json_decode($dataString), "citation", json_decode('[{"id": "ITEM-2"}]'));
        $expectedFilteredElsevier = '[2]';
        $this->assertEquals($actualFilteredElsevier, $expectedFilteredElsevier);
    }


    public function testRenderCitationNumberResultAsArray()
    {
        $style = StyleSheet::loadStyleSheet("elsevier-vancouver");
        $citeProc = new CiteProc($style);
        $result = $citeProc->render(json_decode("
        [
            {
                \"id\": \"ITEM-1\",
                \"title\": \"Book 1\",
                \"type\": \"book\"
            },
            {
                \"id\": \"ITEM-2\",
                \"title\": \"Book 2\",
                \"type\": \"book\"
            },
            {
                \"id\": \"ITEM-3\",
                \"title\": \"Book 3\",
                \"type\": \"book\"
            }
        ]"), "citation", json_decode("
        [
            [
                {
                    \"id\": \"ITEM-1\"
                }, 
                {
                    \"id\": \"ITEM-3\"
                }
            ],
            [
                {
                    \"id\": \"ITEM-2\"
                }
            ]
        ]"), true);

        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));
        $this->assertEquals("[1,3]", $result[0]);
        $this->assertEquals("[2]", $result[1]);
    }

    public function testOverrideOnlyCurrentLang()
    {
        $this->runTestSuite("locale_OverrideOnlyCurrentLang");
    }
}
