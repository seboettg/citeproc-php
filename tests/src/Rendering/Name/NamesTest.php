<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Test\Rendering\Name;

use Exception;
use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\Rendering\Name\Names;
use Seboettg\CiteProc\Style\Options\DemoteNonDroppingParticle;
use Seboettg\CiteProc\Style\Options\GlobalOptions;
use Seboettg\CiteProc\Style\Options\PageRangeFormats;
use Seboettg\CiteProc\Test\TestSuiteTestCaseTrait;
use SimpleXMLElement;

class NamesTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRenderSubstitute()
    {
        $this->runTestSuite("names_substitute");
    }

    public function testEditorTranslator()
    {
        $this->runTestSuite("name_EditorTranslatorBoth");
    }

    public function testEtAlUseLast()
    {
        $this->runTestSuite("name_EtAlUseLast");
    }
    /*
    public function testEtAlWithCombined()
    {
        //TODO: implement
        //$this->_testRenderTestSuite("name_EtAlWithCombined");
    }
    */

    /**
     * @throws Exception
     */
    public function testRenderLabelBeforeNameShouldBeTrueIfLabelTagBeforeName()
    {
        $csl = <<<EOD
            <names variable="translator">
              <label form="verb-short" prefix=", "/>
              <name name-as-sort-order="first" and="text" sort-separator=", " delimiter=", " delimiter-precedes-last="always"/>
            </names>
        EOD;
        CiteProc::setContext(new Context());
        $style = new SimpleXMLElement($csl);
        $names = new Names($style, null);
        $this->assertTrue($names->isRenderLabelBeforeName());
    }

    /**
     * @throws InvalidStylesheetException
     * @throws Exception
     */
    public function testRenderLabelBeforeNameShouldBeFalseIfLabelTagAfterName()
    {
        $csl = <<<EOD
            <names variable="translator">
              <name name-as-sort-order="first" and="text" sort-separator=", " delimiter=", " delimiter-precedes-last="always"/>
              <label form="verb-short" prefix=", "/>
            </names>
        EOD;
        CiteProc::setContext(new Context());
        $style = new SimpleXMLElement($csl);
        $names = new Names($style, null);
        $this->assertFalse($names->isRenderLabelBeforeName());
    }

    /**
     * @throws CiteProcException
     * @throws InvalidStylesheetException
     * @throws Exception
     */
    public function testLabelShouldAppearAfterNameIfLabelTagAfterNameTag()
    {
        $csl = <<<EOD
            <names variable="translator">
              <name name-as-sort-order="first" and="text" sort-separator=", " delimiter=", " delimiter-precedes-last="always"/>
              <label form="verb-short" prefix=", " />
            </names>
        EOD;
        $data = <<<EOD
              {
                "type": "book",
                "issued": {
                    "date-parts": [
                        [
                            1546
                        ]
                    ]
                },
                "publisher": "Jaques Kerver",
                "translator": [
                    {
                        "given": "Jean",
                        "family": "Martin"
                    }
                ]
              }
            EOD;
        $expectedResult = "Jean Martin, trans. by";
        $this->mockContext();

        $style = new SimpleXMLElement($csl);
        $names = new Names($style, null);
        $actualResult = $names->render(json_decode($data));
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @throws CiteProcException
     * @throws InvalidStylesheetException
     * @throws Exception
     */
    public function testLabelShouldAppearBeforeNameIfLabelTagBeforeNameTag()
    {
        $csl = <<<EOD
            <names variable="translator">
              <label form="verb-short" prefix=", " />
              <name name-as-sort-order="first" and="text" sort-separator=", " delimiter=", " delimiter-precedes-last="always"/>
            </names>
        EOD;
        $data = <<<EOD
              {
                "type": "book",
                "issued": {
                    "date-parts": [
                        [
                            1546
                        ]
                    ]
                },
                "publisher": "Jaques Kerver",
                "translator": [
                    {
                        "given": "Jean",
                        "family": "Martin"
                    }
                ]
              }
            EOD;
        $expectedResult = ", trans. by Jean Martin";
        $this->mockContext();

        $style = new SimpleXMLElement($csl);
        $names = new Names($style, null);
        $actualResult = $names->render(json_decode($data));
        $this->assertEquals($expectedResult, $actualResult);
    }

    private function mockContext()
    {
        $globalOptionsMock = $this->createMock(GlobalOptions::class);
        $globalOptionsMock
            ->method('getDemoteNonDroppingParticles')
            ->willReturn(DemoteNonDroppingParticle::NEVER());
        $globalOptionsMock
            ->method('getPageRangeFormat')
            ->willReturn(PageRangeFormats::CHICAGO());
        $globalOptionsMock
            ->method('isInitializeWithHyphen')
            ->willReturn(false);

        $stub = $this->createMock(Context::class);
        $stub
            ->method('getGlobalOptions')
            ->willReturn($globalOptionsMock);

        $context = new Context();
        $context->setLocale(new Locale());
        $context->setGlobalOptions($globalOptionsMock);
        CiteProc::setContext($context);
    }
}
