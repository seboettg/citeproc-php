<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2022 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Test\Rendering\Choose;

use Exception;
use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Exception\ClassNotFoundException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\Choose\Choose;
use SimpleXMLElement;

class ChooseIfTest extends TestCase
{

    /**
     * @throws ClassNotFoundException
     * @throws InvalidStylesheetException
     * @throws Exception
     */
    public function testIfShouldConsiderConstraintsFromBothTypeAndVariable()
    {
        $csl = <<<EOT
            <choose>
                <if type="speech" variable="publisher publisher-place" match="none">
                    <text value="true"/>
                </if>
                <else>
                    <text value="false"/>
                </else>
            </choose>
        EOT;

        $dataString = <<<EOT
            {
                "type": "speech",
                "publisher": "Publisher",
                "publisher-place": "Frankfurt"
            }
        EOT;
        $this->mockContext();
        $expectedResult = "false"; // since match="none"
        $choose = new Choose(new SimpleXMLElement($csl), null);
        $this->assertEquals($expectedResult, $choose->render(json_decode($dataString)));
    }

    private function mockContext(): void
    {
        $mockedContext = $this->createMock(Context::class);
        $mockedContext
            ->method('isModeBibliography')
            ->willReturn(true);
        $mockedContext
            ->method('isModeCitation')
            ->willReturn(false);
        $mockedContext
            ->method('getMode')
            ->willReturn('bibliography');
        $mockedContext
            ->method('getMarkupExtension')
            ->willReturn([]);

        CiteProc::setContext($mockedContext);
    }
}
