<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Test\Locale;

use PHPUnit\Framework\TestCase;
use Seboettg\CiteProc\Locale\Locale;

class LocaleTest extends TestCase
{
    /**
     * @var Locale
     */
    protected $object;

    public function testFilterTerms()
    {
        $a1 = $this->object->filter("terms", "no date");
        $a2 = $this->object->filter("terms", "no date", "short");
        static::assertEquals("ohne Datum", $a1->{'single'});
        static::assertEquals("o. J.", $a2->{'single'});
    }

    protected function setUp(): void
    {
        $this->object = new Locale("de-DE");
    }
}
