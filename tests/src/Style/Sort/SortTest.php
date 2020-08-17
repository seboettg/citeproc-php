<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style\Sort;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class SortTest extends TestCase
{
    use TestSuiteTestCaseTrait;

    public function testSortVariable()
    {
        $xml = "<style><bibliography><sort><key variable=\"author\"/></sort><layout><names variable=\"author\"><name/></names><text prefix=\", \" variable=\"title\"/></layout></bibliography></style>";

        $json = "[
            {
                \"title\": \"Book 1\",
                \"author\": [
                    {
                        \"family\": \"Roe\", 
                        \"given\": \"Jane\"
                    }
                ], 
                \"id\": \"ITEM-1\", 
                \"type\": \"book\"
            }, 
            {
                \"title\": \"Book 2\",
                \"author\": [
                    {
                        \"family\": \"Doe\", 
                        \"given\": \"John\"
                    }
                ], 
                \"id\": \"ITEM-2\", 
                \"type\": \"book\"
            }, 
            {
                \"title\": \"Book 3\",
                \"author\": [
                    {
                        \"family\": \"Flinders\", 
                        \"given\": \"Jacob\"
                    }
                ], 
                \"id\": \"ITEM-3\", 
                \"type\": \"book\"
            }
        ]";
        $jsonObject = json_decode($json);
        $citeProc = new CiteProc($xml, "en-US");
        $result = $citeProc->render($jsonObject);

        $expected = "<div class=\"csl-bib-body\">
  <div class=\"csl-entry\">John Doe, Book 2</div>
  <div class=\"csl-entry\">Jacob Flinders, Book 3</div>
  <div class=\"csl-entry\">Jane Roe, Book 1</div>
</div>";

        $this->assertEquals($expected, $result);
    }


    public function testSortVariableWithTwoKeys()
    {
        $xml = "<style><bibliography><sort><key variable=\"issued\" sort=\"descending\"/><key variable=\"author\"/></sort><layout><names variable=\"author\"><name/></names><text prefix=\", \" variable=\"title\"/><date prefix=\" (\" suffix=\").\" variable=\"issued\" date-parts=\"year\" /></layout></bibliography></style>";

        $json = "[
            {
                \"title\": \"Book 3\",
                \"author\": [
                    {
                        \"family\": \"Zeppelin\", 
                        \"given\": \"Graf\"
                    }
                ], 
                \"issued\": {\"date-parts\":[[\"2000\"]]},
                \"id\": \"ITEM-3\", 
                \"type\": \"book\"
            },
            {
                \"title\": \"Book 4\",
                \"author\": [
                    {
                        \"family\": \"Anderson\", 
                        \"given\": \"Jakob\"
                    }
                ], 
                \"issued\": {\"date-parts\":[[\"2003\"]]},
                \"id\": \"ITEM-4\", 
                \"type\": \"book\"
            },
            {
                \"title\": \"Book 1\",
                \"author\": [
                    {
                        \"family\": \"Roe\", 
                        \"given\": \"Jane\"
                    }
                ],
                \"issued\": {\"date-parts\":[[\"2003\"]]},
                \"id\": \"ITEM-1\", 
                \"type\": \"book\"
                
            }, 
            {
                \"title\": \"Book 2\",
                \"author\": [
                    {
                        \"family\": \"Doe\", 
                        \"given\": \"John\"
                    }
                ], 
                \"issued\": {\"date-parts\":[[\"2003\"]]},
                \"id\": \"ITEM-2\", 
                \"type\": \"book\"
            }
        ]";

        $jsonObject = json_decode($json);
        $citeProc = new CiteProc($xml, "en-US");
        $result = $citeProc->render($jsonObject);

        $expected = "<div class=\"csl-bib-body\">
  <div class=\"csl-entry\">Jakob Anderson, Book 4 (2003).</div>
  <div class=\"csl-entry\">John Doe, Book 2 (2003).</div>
  <div class=\"csl-entry\">Jane Roe, Book 1 (2003).</div>
  <div class=\"csl-entry\">Graf Zeppelin, Book 3 (2000).</div>
</div>";

        $this->assertEquals($expected, $result);
    }


    public function testSortCaseInsensitiveBibliography()
    {
        $this->runTestSuite("sort_CaseInsensitiveBibliography");
    }

    public function testSortBibliographyCitationNumberDescending()
    {
        $this->runTestSuite("sort_BibliographyCitationNumberDescending.json");
    }

    public function testSortBibliographyCitationNumberDescendingViaMacro()
    {
        $this->runTestSuite("sort_BibliographyCitationNumberDescendingViaMacro");
    }

    public function testSortCaseInsensitiveCitation()
    {
        $this->runTestSuite("sort_CaseInsensitiveCitation");
    }

    public function testSortCitation()
    {
        $this->runTestSuite("sort_Citation.json");
    }

    /*
    public function testSortConditionalMacroDates()
    {
        $this->_testRenderTestSuite("sort_ConditionalMacroDates");
    }
    */

    public function testSortDateVariable()
    {
        $this->runTestSuite("sort_DateVariable.json");
    }

    public function testSortDateVariableMixedElements()
    {
        $this->runTestSuite("sort_DateVariableMixedElements");
    }

    public function testSortDateVariableRange()
    {
        $this->runTestSuite("sort_DateVariableRange.json");
    }


    public function testSortDateVariableRangeMixed()
    {
        $this->runTestSuite("sort_DateVariableRangeMixed");
    }


    public function testSortEtAlUseLast()
    {
        $this->runTestSuite("sort_EtAlUseLast");
    }


    public function testSortStripMarkup()
    {
        $this->runTestSuite("sort_StripMarkup");
    }

    public function testSortSubstituteTitle()
    {
        $this->runTestSuite("sort_SubstituteTitle");
    }

    public function testSortVariousNameMacros1()
    {
        $this->runTestSuite("sort_VariousNameMacros1");
    }

    public function testSortVariousNameMacros2()
    {
        $this->runTestSuite("sort_VariousNameMacros2");
    }

    public function testSortVariousNameMacros3()
    {
        $this->runTestSuite("sort_VariousNameMacros3");
    }

    public function testSortDateMacroSortWithSecondFieldAlign()
    {
        $this->runTestSuite("sort_DateMacroSortWithSecondFieldAlign");
    }
}
