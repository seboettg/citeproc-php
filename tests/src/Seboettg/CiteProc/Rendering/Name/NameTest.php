<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;


class NameTest extends \PHPUnit_Framework_TestCase
{


    public function testGetOptions()
    {
        $xml = "<name delimiter=\"; \" form=\"short\" name-as-sort-order=\"all\" sort-separator=\", \" and=\"symbol\" et-al-min=\"4\" et-al-use-first=\"2\"/>";

        $name = new Name(new \SimpleXMLElement($xml), new Names(new \SimpleXMLElement("<names />")));

        $options = $name->getOptions();

        $this->assertEquals("; ", $options['delimiter']);
        $this->assertEquals("short", $options['form']);
        $this->assertEquals("all", $options['name-as-sort-order']);
        $this->assertEquals(", ", $options['sort-separator']);
        $this->assertEquals("symbol", $options['and']);
        $this->assertEquals("4", $options['et-al-min']);
        $this->assertEquals("2", $options['et-al-use-first']);
        $this->assertArrayNotHasKey("parent", $options);
        $this->assertArrayNotHasKey("namePart", $options);
    }
}
