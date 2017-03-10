<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;


use PHPUnit_Framework_ExpectationFailedException;
use Seboettg\CiteProc\Exception\CiteProcException;

class CiteProcTest extends \PHPUnit_Framework_TestCase implements TestSuiteTests
{

    use TestSuiteTestCaseTrait;

    private $data = "{
   \"DOI\":\"\",
   \"ISBN\":\"\",
   \"ISSN\":\"\",
   \"URL\":\"http://www.uni-kassel.de/~seboettg/ba-thesis.pdf\",
   \"abstract\":\"Ein Abstract.\",
   \"annote\":\"\",
   \"author\":[
      {
         \"family\":\"B\u00f6ttger\",
         \"given\":\"Sebastian\"
      }
   ],
   \"citation-label\":\"bottger2012konzept\",
   \"collection-editor\":[

   ],
   \"collection-title\":\"\",
   \"container-author\":[

   ],
   \"container-title\":\"\",
   \"edition\":\"\",
   \"editor\":[

   ],
   \"event-date\":{
      \"date-parts\":[
         [
            \"2012\",
            \"04\"
         ]
      ],
      \"literal\":\"2012\"
   },
   \"event-place\":\"Kassel\",
   \"genre\":\"Master thesis\",
   \"id\":\"3c2ffd52e7081b66bf420f993d9144bbseboettg\",
   \"issue\":\"\",
   \"issued\":{
      \"date-parts\":[
         [
            \"2012\",
            \"04\"
         ]
      ],
      \"literal\":\"2012\"
   },
   \"keyword\":\"ba-thesis bathesis folksonomy myown recommender tagging tagrecommendation thesis univideo video\",
   \"note\":\"\",
   \"number\":\"\",
   \"page\":\"\",
   \"page-first\":\"\",
   \"publisher\":\"Universit\u00e4t Kassel\",
   \"publisher-place\":\"Kassel\",
   \"status\":\"\",
   \"title\":\"Konzept und Umsetzung eines Tag-Recommenders f\u00fcr Video-Ressourcen am Beispiel UniVideo\",
   \"type\":\"thesis\",
   \"volume\":\"\"
}";

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
        $style = StyleSheet::loadStyleSheet("apa");
        $this->citeProc = new CiteProc($style, "de-DE");
        $obj = json_decode($this->data);
        $error = json_last_error();
        if ($error) {
            throw new CiteProcException(json_last_error_msg());
        }
        $this->dataObj = [$obj];
    }

    public function testFullStyleBibliography1()
    {
        $this->_testRenderTestSuite("fullstyles_APABibliography");
    }




    public function testRenderTestSuite()
    {
        //$this->_testRenderTestSuite(".json", ['bugreports_', 'number_FailingDelimiters', 'number_LeadingZeros.json']);
    }
}
