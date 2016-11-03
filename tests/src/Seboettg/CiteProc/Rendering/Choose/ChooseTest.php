<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Node\Choose\Choose;


use Seboettg\CiteProc\Rendering\Choose\Choose;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;

class ChooseTest extends \PHPUnit_Framework_TestCase
{
    use TestSuiteTestCaseTrait;

    private $chooseXml = [
        '<choose><if type="book"><text variable="title" font-style="italic"/></if><else><text variable="title"/></else></choose>',
        '<choose><if is-numeric="volume"><text variable="title"/><text value="; "/><text variable="volume"/></if><else><text variable="title"/></else></choose>'
    ];

    private $dataThesis  = '{"title":"Ein herzzerreißendes Werk von umwerfender Genialität","type":"thesis"}';
    private $dataWithoutType  = '{"title":"Ein herzzerreißendes Werk von umwerfender Genialität"}';
    private $dataBook    = '{"title":"Ein herzzerreißendes Werk von umwerfender Genialität","type":"book"}';


    /**
     * @var Choose
     */
    private $choose;

    public function setUp()
    {
        $xml = new \SimpleXMLElement($this->chooseXml[0]);
        $this->choose = new Choose($xml);
    }

    public function testRender()
    {
        $this->_testIf();
        $this->_testElse();
        //TODO: test elseif
    }

    public function _testIf()
    {
        $json = json_decode($this->dataBook);
        $ret = $this->choose->render($json);
        $this->assertRegExp('/^\<i\>(.+)\<\/i\>$/', $ret);
        $this->assertEquals("<i>Ein herzzerreißendes Werk von umwerfender Genialität</i>", $ret);
    }

    public function _testElse()
    {
        $this->assertEquals("Ein herzzerreißendes Werk von umwerfender Genialität", $this->choose->render(json_decode($this->dataThesis)));
        $this->assertEquals("Ein herzzerreißendes Werk von umwerfender Genialität", $this->choose->render(json_decode($this->dataWithoutType)));

    }

    public function testIsNumeric()
    {
        $xml = new \SimpleXMLElement($this->chooseXml[1]);
        $choose = new Choose($xml);
        $ret1 = $choose->render(json_decode('{"title":"Ein herzzerreißendes Werk von umwerfender Genialität","volume":2}'));
        $ret2 = $choose->render(json_decode('{"title":"Ein herzzerreißendes Werk von umwerfender Genialität","volume":"non-numeric value"}'));
        $ret3 = $choose->render(json_decode('{"title":"Ein herzzerreißendes Werk von umwerfender Genialität"}'));

        $this->assertEquals("Ein herzzerreißendes Werk von umwerfender Genialität; 2", $ret1);
        $this->assertEquals("Ein herzzerreißendes Werk von umwerfender Genialität", $ret2);
        $this->assertEquals("Ein herzzerreißendes Werk von umwerfender Genialität", $ret3);
    }

    public function testRenderTestSuite()
    {

    }

}
