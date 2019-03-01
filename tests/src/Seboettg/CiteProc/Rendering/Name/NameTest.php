<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class NameTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    public function testNameArticularNameAsSortOrder()
    {
        $this->_testRenderTestSuite("name_ArticularNameAsSortOrder");
    }

    public function testNameArticularPlain()
    {
        $this->_testRenderTestSuite("name_ArticularPlain");
    }

    public function testNameArticularShortForm()
    {
        $this->_testRenderTestSuite("name_ArticularShortForm.json");
    }

    public function testNameAsianGlyphs()
    {
        $this->_testRenderTestSuite("name_AsianGlyphs");
    }

    public function testNameAuthorCount()
    {
        $this->_testRenderTestSuite("name_AuthorCount.json");
    }

    public function testNameAuthorCountWithMultipleVariables()
    {
        $this->_testRenderTestSuite("name_AuthorCountWithMultipleVariables");
    }

    public function testNameAuthorCountWithSameVarContentAndCombinedTermFail()
    {
        $this->_testRenderTestSuite("name_AuthorCountWithSameVarContentAndCombinedTermFail");
    }

    public function testNameAuthorCountWithSameVarContentAndCombinedTermSucceed()
    {
        $this->_testRenderTestSuite("name_AuthorCountWithSameVarContentAndCombinedTermSucceed");
    }

    public function testNameAuthorEditorCount()
    {
        $this->_testRenderTestSuite("name_AuthorEditorCount");
    }

    public function testNameCelticClanName()
    {
        $this->_testRenderTestSuite("name_CelticClanName");
    }

    public function testNameCeltsAndToffsCrowdedInitials()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsCrowdedInitials");
    }

    public function testNameCeltsAndToffsNoHyphens()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsNoHyphens");
    }

    public function testNameCeltsAndToffsSpacedInitials()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsSpacedInitials");
    }

    public function testNameCeltsAndToffsWithHyphens()
    {
        $this->_testRenderTestSuite("name_CeltsAndToffsWithHyphens");
    }

    public function testNameAttrAnd()
    {
        $this->_testRenderTestSuite("nameattr_And");
    }

    public function testNameAttrDelimiterPrecedesEtAl()
    {
        $this->_testRenderTestSuite("nameattr_DelimiterPrecedesEtAl");
    }

    public function testNameAttrDelimiterPrecedesLast()
    {
        $this->_testRenderTestSuite("nameattr_DelimiterPrecedesLast");
    }

    public function testNameAttrEtAlMin()
    {
        $this->_testRenderTestSuite("nameattr_EtAlMin");
    }

    public function testNameAttrEtAlUseFirst()
    {
        $this->_testRenderTestSuite("nameattr_EtAlUseFirst");
    }

    public function testNameEtAlUseLast()
    {
        $this->_testRenderTestSuite("name_EtAlUseLast");
    }


    public function testNameAttrNameDelimiter()
    {
        $this->_testRenderTestSuite("nameattr_NameDelimiter");
    }

    public function testNameAttrNameForm()
    {
        $this->_testRenderTestSuite("nameattr_NameForm");
    }

    public function testNameAttrNameAsSortOrder()
    {
        $this->_testRenderTestSuite("nameattr_NameAsSortOrder");
    }

    public function testNameAttrSortSeparator()
    {
        $this->_testRenderTestSuite("nameattr_SortSeparator");
    }

    public function testNameAttrInitializeWith()
    {
        $this->_testRenderTestSuite("nameattr_InitializeWith");
    }

    public function testNameAttrEtAlSubsequentMin()
    {
        $this->_testRenderTestSuite("nameattr_EtAlSubsequentMin");
    }

    public function testNameAttrEtAlSubsequentUseFirst()
    {
        $this->_testRenderTestSuite("nameattr_EtAlSubsequentUseFirst");
    }

    public function testNameAttrEtAlSubsequentCompleteAll()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_complete-all");
    }

    public function testNameAttrEtAlSubsequentCompleteEach()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_complete-each");
    }

    public function testNameAttrEtAlSubsequentNone()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_none");
    }

    public function testNameAttrEtAlSubsequentPartialEach()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_partial-each");
    }

    public function testNameAttrEtAlSubsequentPartialFirst()
    {
        $this->_testRenderTestSuite("nameattr_subsequent-author-substitute_partial-first");
    }

    public function testFormatSmallCaps()
    {
        $this->_testRenderTestSuite("name_FormatSmallCaps");
    }

    public function testNameNamepartAffixes()
    {
        $this->_testRenderTestSuite("name_namepartAffixes.json");
    }

    public function testNameNamepartAffixesNameAsSortOrder()
    {
        $this->_testRenderTestSuite("name_namepartAffixesNameAsSortOrder");
    }

    public function testNameCitationMacroNoInheritanceFromRoot() {
        $this->_testRenderTestSuite("name_CitationMacroNoInheritanceFromRoot");
    }

    public function testInitializeCyrillicName()
    {
        $this->_testRenderTestSuite("nameattr_initializeCyrillicName");
    }

    public function testEnrichMarkupNames()
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

        $enrichAuthorWithLinkFunction = function($authorItem, $authorName) {
            return isset($authorItem->id) ? '<a href="https://example.org/author/' . $authorItem->id . '" title="' . $authorName . '">'
                . $authorName . '</a>' : $authorName;
        };

        $apa = StyleSheet::loadStyleSheet("apa");
        $citeproc = new CiteProc($apa, "de-DE", [
            'author' => $enrichAuthorWithLinkFunction
        ]);
        $actual = $citeproc->render(json_decode($cslJson), "bibliography");

        $expected = '<div class="csl-bib-body">
  <div class="csl-entry"><a href="https://example.org/author/doe" title="Doe, J.">Doe, J.</a>, &#38; Müller, A. (2001). <i>My Anonymous Heritage</i>.</div>
</div>';
        $this->assertEquals($expected, $actual);
    }

    public function testEnrichMarkupNamesCitationsAndBibliography()
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

        $enrichAuthorWithLinkFunctionBibliography = function($authorItem, $authorName) {
            return isset($authorItem->id) ? '<a href="https://example.org/author/' . $authorItem->id . '" title="' . $authorName . '">'
                . $authorName . '</a>' : $authorName;
        };

        $apa = StyleSheet::loadStyleSheet("apa");
        $citeproc = new CiteProc($apa, "de-DE", [
            "bibliography" => [
                "author" => $enrichAuthorWithLinkFunctionBibliography,
                "csl-entry" => function($item, $renderedItem) {
                    return '<a id="' . $item->id . '"></a>' . $renderedItem;
                }
            ],
            "citation" => [
                "csl-entry" => function($item, $renderedItem) {
                    return '<a href="#' . $item->id . '">' . $renderedItem . '</a>';
                }
            ]
        ]);
        $actualBibliography = $citeproc->render(json_decode($cslJson), "bibliography");
        $actualCitation = $citeproc->render(json_decode($cslJson), "citation");
        $expectedBibliography = '<div class="csl-bib-body">
  <div class="csl-entry"><a id="item-1"></a><a href="https://example.org/author/doe" title="Doe, J.">Doe, J.</a>, &#38; Müller, A. (2001). <i>My Anonymous Heritage</i>.</div>
</div>';
        $expectedCitation = '(<a href="#item-1">Doe &#38; Müller, 2001</a>)';
        $this->assertEquals($expectedCitation, $actualCitation);
        $this->assertEquals($expectedBibliography, $actualBibliography);
    }
}
