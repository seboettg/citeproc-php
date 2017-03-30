<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\CiteProc;

class MacroTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testRender()
    {
        $xml = '<style><macro name="title"><choose><if type="book"><text variable="title" font-style="italic"/></if><else><text variable="title"/></else></choose></macro><citation><layout delimiter="; "><text macro="title"/></layout></citation></style>';
        $data = json_decode('[{"title":"Ein herzzerreißendes Werk von umwerfender Genialität","type":"book"},{"title":"Ein nicht so wirklich herzzerreißendes Werk von umwerfender Genialität","type":"thesis"}]');

        $styleNode = new \SimpleXMLElement($xml);

        $citeProc = new CiteProc($xml);

        $actual = $citeProc->render($data, 'citation');

        $expected = '<i>Ein herzzerreißendes Werk von umwerfender Genialität</i>; '.
            'Ein nicht so wirklich herzzerreißendes Werk von umwerfender Genialität';

        $this->assertEquals($expected, $actual);
    }


}
