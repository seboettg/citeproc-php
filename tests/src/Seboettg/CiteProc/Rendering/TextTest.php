<?php

namespace Seboettg\CiteProc\Rendering;


use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\Style\Macro;

class TextTest extends \PHPUnit_Framework_TestCase
{

    private $textXml = ['<text variable="title"/>'];

    private $dataTitle  = '{"title":"Ein herzzerreißendes Werk von umwerfender Genialität","type":"book"}';
    private $dataPublisherPlace = '{"publisher-place":"Frankfurt am Main"}';

    /**
     * @var Text
     */
    private $text;

    public function setUp()
    {
        $xml = new \SimpleXMLElement($this->textXml[0]);
        $this->text = new Text($xml);
    }

    public function testVariable()
    {
        //test variable
        $ret = $this->text->render(json_decode($this->dataTitle));

        $this->assertEquals("Ein herzzerreißendes Werk von umwerfender Genialität", $ret);
        $this->assertEmpty($this->text->render(json_decode($this->dataPublisherPlace)));

    }

    public function testMacro()
    {

        $macroXml = "<macro name=\"title\"><choose><if type=\"book\"><text variable=\"title\" font-style=\"italic\"/></if><else><text variable=\"title\"/></else></choose></macro>";
        $context = new Context();
        $macro = new Macro(new \SimpleXMLElement($macroXml));
        $context->addMacro($macro->getName(), $macro);
        CiteProc::setContext($context);
        $text = new Text(new \SimpleXMLElement("<text macro=\"title\"/>"));

        $this->assertEquals(
            "<i>Ein Buch</i>",
            $text->render(json_decode("{\"title\":\"Ein Buch\", \"type\": \"book\"}"))
        );

        $this->assertEquals(
            "Ein Buch",
            $text->render(json_decode("{\"title\":\"Ein Buch\", \"type\": \"thesis\"}"))
        );
    }

    public function testValue()
    {
        $text = new Text(new \SimpleXMLElement("<text value=\"Ein Titel\"/>"));
        $this->assertEquals("Ein Titel", $text->render(null));
    }

    public function testTerm()
    {
        $context = new Context();
        $context->setLocale(new Locale("de-DE"));
        CiteProc::setContext($context);

        $text = new Text(new \SimpleXMLElement("<text term=\"book\"/>"));
        $this->assertEquals("Buch", $text->render(null));
    }


}
