<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class TextTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    private $textXml = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<style>
    <citation>
        <layout>
            <text variable="title"/>
        </layout>
    </citation>
</style>;
EOT;


    private $dataTitle  = '{"title":"Ein herzzerreißendes Werk von umwerfender Genialität","type":"book"}';
    private $dataPublisherPlace = '{"publisher-place":"Frankfurt am Main"}';

    /**
     * @var CiteProc
     */
    private $citeproc;

    public function setUp()
    {

    }



    public function testMacro()
    {

        $macroXml = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<style xmlns="http://purl.org/net/xbiblio/csl" version="1.0">
    <macro name="title">
        <choose>
            <if type="book">
                <text variable="title" font-style="italic"/>
            </if>
            <else>
                <text variable="title"/>
            </else>
        </choose>
    </macro>
    <citation>
        <layout>
            <text macro="title"/>
        </layout>
    </citation>
</style>
EOT;
        $citeProc = new CiteProc($macroXml);



        $this->assertEquals(
            "<i>Ein Buch</i>",
            $citeProc->render(json_decode("[{\"title\":\"Ein Buch\", \"type\": \"book\"}]"), "citation")
        );

        $this->assertEquals(
            "Ein Buch",
            $citeProc->render(json_decode("[{\"title\":\"Ein Buch\", \"type\": \"thesis\"}]"), "citation")
        );
    }

    public function testEnrichMarkupTitles()
    {
        $cslJson = '[{
            "author": [
              {
                "family": "Doe",
                "given": "John",
                "id": "doe"
              },
              {
                "family": "Müller",
                "given": "Alexander"
              }
            ],
            "id": "item-1",
            "issued": {
              "date-parts": [
                [
                  "2001"
                ]
              ]
            },
            "title": "My Anonymous Heritage",
            "type": "book"
        }]';

        $enrichTitleWithLinkFunction = function($citeItem, $renderedVariable) {
            return isset($citeItem->id) ? '<a href="https://example.org/publication/' . $citeItem->id . '" title="' . $renderedVariable . '">'
                . $renderedVariable . '</a>' : $renderedVariable;
        };

        $apa = StyleSheet::loadStyleSheet("apa");
        $citeproc = new CiteProc($apa, "de-DE",
            [
                'title' => $enrichTitleWithLinkFunction
            ]
        );
        $actual = $citeproc->render(json_decode($cslJson), "bibliography");

        $expected = '<div class="csl-bib-body">
  <div class="csl-entry">Doe, J., &#38; Müller, A. (2001). <i><a href="https://example.org/publication/item-1" title="My Anonymous Heritage">My Anonymous Heritage</a></i>.</div>
</div>';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \Seboettg\CiteProc\Exception\CiteProcException
     */
    public function testEnrichMarkupURL()
    {
        $cslJson = '[{
            "author": [
              {
                "family": "Doe",
                "given": "John",
                "id": "doe"
              },
              {
                "family": "Müller",
                "given": "Alexander"
              }
            ],
            "container-title": "Heritages and taxes. How to avoid responsibility.",
            "id": "item-1",
            "issued": {
              "date-parts": [
                [
                  "2001"
                ]
              ]
            },
            "page": "123-127",
            "publisher": "Initiative Neue Soziale Marktwirtschaft (INSM)",
            "publisher-place": "Berlin, Germany",
            "title": "My Anonymous Heritage",
            "type": "book",
            "URL": "https://example.org/publication/item-1"
        }]';

        $enrichUrlWithLinkFunction = function($citeItem, $renderedVariable) {
            return preg_match("/http[s]?:\/\/.+/", $citeItem->URL) ? '<a href="' . $citeItem->URL . '">'
                . $citeItem->URL . '</a>' : $citeItem->URL;
        };

        $apa = StyleSheet::loadStyleSheet("apa");
        $citeproc = new CiteProc($apa, "en-US", [
            'URL' => $enrichUrlWithLinkFunction
        ]);
        $actual = $citeproc->render(json_decode($cslJson), "bibliography");

        $expected = '<div class="csl-bib-body">
  <div class="csl-entry">Doe, J., &#38; Müller, A. (2001). My Anonymous Heritage. In <i>Heritages and taxes. How to avoid responsibility.</i> (pp. 123-127). Initiative Neue Soziale Marktwirtschaft (INSM). <a href="https://example.org/publication/item-1">https://example.org/publication/item-1</a></div>
</div>';
        $this->assertEquals($expected, $actual);
    }

    public function testEnrichMarkupCitationNumber()
    {
        $cslJson = '[
          {
            "author": [
              {
                "family": "Doe",
                "given": "James",
                "suffix": "III"
              }
            ],
            "id": "item-1",
            "issued": {
              "date-parts": [
                [
                  "2001"
                ]
              ]
            },
            "title": "My Anonymous Heritage",
            "type": "book"
          },
          {
            "author": [
              {
                "family": "Anderson",
                "given": "John",
                "id": "anderson.j"
              },
              {
                "family": "Brown",
                "given": "John",
                "id": "brown.j"
              }
            ],
            "issued": {
              "date-parts": [
                [
                  "1998"
                ]
              ]
            },
            "id": "ITEM-2",
            "type": "book",
            "title": "Two authors writing a book"
          }]';

        $apa = StyleSheet::loadStyleSheet("elsevier-with-titles");

        $citeproc = new CiteProc($apa, "en-US",
            [
                "bibliography" => [
                    "citation-number" => function($citeItem, $renderedVariable) {
                        return isset($citeItem->id) ? '<a id="' . $citeItem->id. '" href="#' . $citeItem->id . '">'
                            . $renderedVariable . '</a>' : $renderedVariable;
                    }
                ],
                "citation" => [
                    "citation-number" => function($citeItem, $renderedVariable) {
                        return isset($citeItem->id) ? '<a href="#' . $citeItem->id . '">'
                            . $renderedVariable . '</a>' : $renderedVariable;
                    }
                ]
            ]
        );

        $actual = $citeproc->render(json_decode($cslJson), "bibliography");

        $expected = '<div class="csl-bib-body">
  <div class="csl-entry"><div class="csl-left-margin">[<a id="item-1" href="#item-1">1</a>]</div><div class="csl-right-inline">J. Doe III, My Anonymous Heritage, 2001.</div></div>
  <div class="csl-entry"><div class="csl-left-margin">[<a id="ITEM-2" href="#ITEM-2">2</a>]</div><div class="csl-right-inline">J. Anderson, J. Brown, Two authors writing a book, 1998.</div></div>
</div>';
        $this->assertEquals($expected, $actual);

        $actual = $citeproc->render(json_decode($cslJson), "citation");

        $expected = '[<a href="#item-1">1</a>,<a href="#ITEM-2">2</a>]';

        $this->assertEquals($expected, $actual);
    }

    public function testQuotesPunctuationDefault()
    {
        $this->runTestSuite('quotes_PunctuationDefault');
    }

    public function testQuotesPunctuationInQuotes()
    {
        $this->runTestSuite('quotes_PunctuationInQuotes');
    }

    public function testQuotesPunctuationOutsideQuotes()
    {
        $this->runTestSuite('quotes_PunctuationOutsideQuotes');
    }

    public function testQuotesPunctuationMacro()
    {
        $this->runTestSuite('quotes_PunctuationMacro');
    }
}
