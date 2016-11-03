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

    private $data = "{\"DOI\":\"\",\"ISBN\":\"\",\"ISSN\":\"\",\"URL\":\"http://www.uni-kassel.de/~seboettg/ba-thesis.pdf\",\"abstract\":\"Kollaborative Verschlagwortungssysteme bieten Nutzern die Möglichkeit zur freien Verschlagwortung von Ressourcen im World Wide Web. Sie ermöglichen dem Nutzer beliebige Ressourcen mit frei wählbaren Schlagwörtern – so genannten Tags – zu versehen (Social Tagging). Im weiteren Sinne ist Social Tagging nichts anderes als das Indexieren von Ressourcen durch die Nutzenden selbst. Dabei sind die Tag-Zuordnungen für den einzelnen Nutzer und für die gesamte Community in vielerlei Hinsicht hilfreich. So können durch Tags persönliche Ideen oder Wertungen für eine Ressource ausgedrückt werden.  Außerdem können Tags als Kommunikationsmittel von den Nutzern oder Nutzergruppen untereinander verwendet werden. Tags helfen zudem bei der Navigation, beim Suchen und beim zufälligen Entdecken von neuen Ressourcen. Das Verschlagworten der Ressourcen ist für unbedarfte Anwender eine kognitiv anspruchsvolle Aufgabe. Als Unterstützung können Tag-Recommender eingesetzt werden, die Nutzern passende Tags vorschlagen sollen.\r\n\r\nUniVideo ist das Videoportal der Universität Kassel, das jedem Mitglied der Hochschule ermöglicht Videos bereitzustellen und weltweit über das WWW abrufbar zu machen. Die bereitgestellten Videos müssen von ihren Eigentümern beim Hochladen verschlagwortet werden. Die dadurch entstehende Struktur dient wiederum als Grundlage für die Navigation in UniVideo. In dieser Arbeit werden vier verschiedene Ansätze für Tag-Recommender theoretisch diskutiert und deren praktische Umsetzung für UniVideo untersucht und bewertet. Dabei werden zunächst die Grundlagen des Social Taggings erläutert und der Aufbau von UniVideo erklärt, bevor die Umsetzung der vier einzelnen Tag-Recommender beschrieben wird. Anschließend wird gezeigt wie aus den einzelnen Tag-Recommendern durch Verschmelzung ein hybrider Tag-Recommender umgesetzt werden kann.\",\"annote\":\"\",\"author\":[{\"family\":\"Böttger\",\"given\":\"Sebastian\"}],\"citation-label\":\"bottger2012konzept\",\"collection-editor\":[],\"collection-title\":\"\",\"container-author\":[],\"container-title\":\"\",\"documents\":[{\"date\":{\"date\":2,\"day\":3,\"hours\":21,\"minutes\":18,\"month\":11,\"seconds\":51,\"time\":1449087531000,\"timezoneOffset\":-60,\"year\":115},\"fileHash\":\"eb0a7f01b54675a22ea73db949022023\",\"fileName\":\"ba-thesis.pdf\",\"md5hash\":\"43293a5595d8e5fd95504b25a8afdae0\",\"temp\":false,\"userName\":\"seboettg\"}],\"edition\":\"\",\"editor\":[],\"event-date\":{\"date-parts\":[[\"2012\",\"04\"]],\"literal\":\"2012\"},\"event-place\":\"Kassel\",\"genre\":\"Master thesis\",\"id\":\"3c2ffd52e7081b66bf420f993d9144bbseboettg\",\"interhash\":\"8fd8ce9278d61f8bd5292d7aeab9aacd\",\"intrahash\":\"3c2ffd52e7081b66bf420f993d9144bb\",\"issue\":\"\",\"issued\":{\"date-parts\":[[\"2012\",\"04\"]],\"literal\":\"2012\"},\"keyword\":\"ba-thesis bathesis folksonomy myown recommender tagging tagrecommendation thesis univideo video\",\"misc\":{\"slides\":\"http://eine-url.de/publikation.pdf\",\"pdf\":\"http://eine-url.de/publikation.pdf\"},\"note\":\"\",\"number\":\"\",\"page\":\"\",\"page-first\":\"\",\"publisher\":\"Universität Kassel\",\"publisher-place\":\"Kassel\",\"status\":\"\",\"title\":\"Konzept und Umsetzung eines Tag-Recommenders für Video-Ressourcen am Beispiel UniVideo\",\"type\":\"thesis\",\"username\":\"seboettg\",\"version\":\"\",\"volume\":\"\"}";


    public function setUp()
    {
        //$this->citeProc = new CiteProc($this->style);
    }



    public function testRenderTestSuite()
    {
        $this->_testRenderTestSuite(".json", ['bugreports_', 'number_FailingDelimiters', 'number_LeadingZeros.json']);
    }
}
