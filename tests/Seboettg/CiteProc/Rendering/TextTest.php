<?php

namespace Seboettg\CiteProc\Rendering;


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

    }

    public function testValue()
    {

    }

    public function testTerm()
    {

    }


}
