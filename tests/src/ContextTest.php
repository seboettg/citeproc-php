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

    private $data = "[{\"author\": [{\"family\": \"Hotho\", \"given\": \"Andreas\"}, {\"family\": \"Benz\", \"given\": \"Dominik\"}], \"title\":\"Book\", \"type\":\"book\"}]";

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
        $dataList = new DataList(json_decode($this->data));
        $this->context->setCitationItems($dataList);
    }


    /**
     * @coversNothing
     */
    public function testGetMacros()
    {
        $macros = $this->citeProc->getContext()->getMacros();
        $this->assertTrue(count($macros) > 0);
        foreach ($macros as $macro) {
            $this->assertInstanceOf("Seboettg\\CiteProc\\Style\\Macro", $macro);
        }

    }

    /**
     * @coversNothing
     */
    public function testGetMode()
    {
        $this->assertEquals("bibliography", $this->context->getMode());
    }

    /**
     * @coversNothing
     */
    public function testHasCitationItems()
    {
        $this->assertTrue($this->citeProc->getContext()->hasCitationItems());
    }

    /**
     * @coversNothing
     */
    public function testGetCitationItems()
    {
        foreach ($this->citeProc->getContext()->getCitationItems() as $item) {
            $this->assertNotNull($item->{'author'});
            $this->assertTrue(is_array($item->{'author'}));
            $this->assertNotEmpty($item->{'author'});
        }
    }
}
