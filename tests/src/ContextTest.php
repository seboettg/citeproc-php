<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\Data\DataList;

class ContextTest extends TestCase
{

    private $data = "[{\"author\": [{\"family\": \"Hotho\", \"given\": \"Andreas\"}, {\"family\": \"Benz\", " .
        "\"given\": \"Dominik\"}], \"title\":\"Book\", \"type\":\"book\"}]";

    /**
     * @var CiteProc
     */
    private $citeProc;

    /**
     * @var Context
     */
    private $context;

    public function setUp()
    {
        $style = StyleSheet::loadStyleSheet("din-1505-2");
        $this->citeProc = new CiteProc($style, "de-DE");
        $this->citeProc->init();
        $this->context = $this->citeProc->getContext();
        $this->context->setMode("bibliography");
        $dataList = new DataList(...json_decode($this->data));
        $this->context->setCitationData($dataList);
    }


    /**
     * @coversNothing
     */
    public function testGetMacros()
    {
        $macros = $this->citeProc->getContext()->getMacros();
        static::assertTrue(count($macros) > 0);
        foreach ($macros as $macro) {
            static::assertInstanceOf("Seboettg\\CiteProc\\Style\\Macro", $macro);
        }
    }

    /**
     * @coversNothing
     */
    public function testGetMode()
    {
        static::assertEquals("bibliography", $this->context->getMode());
    }

    /**
     * @coversNothing
     */
    public function testHasCitationItems()
    {
        static::assertTrue($this->citeProc::getContext()->hasCitationItems());
    }

    /**
     * @coversNothing
     */
    public function testGetCitationItems()
    {
        foreach ($this->citeProc->getContext()->getCitationData() as $item) {
            static::assertNotNull($item->{'author'});
            static::assertTrue(is_array($item->{'author'}));
            static::assertNotEmpty($item->{'author'});
        }
    }
}
