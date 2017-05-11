<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles\Css;

use Seboettg\Collection\ArrayList;

/**
 * Class CssRule
 * @package Seboettg\CiteProc\Styles\Css
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class CssRule
{
    const SELECTOR_TYPE_ID = "#";

    const SELECTOR_TYPE_CLASS = ".";

    private $selectorType;

    private $selector;

    private $directives;

    public function __construct($selector, $selectorType = self::SELECTOR_TYPE_CLASS)
    {
        $this->selector = $selector;
        $this->selectorType = $selectorType;
        $this->directives = new ArrayList();
    }

    public function addDirective($property, $value)
    {
        $this->directives->append("$property: $value;");
    }

    public function __toString()
    {
        $directives = "\t" . implode("\n\t", $this->directives->toArray());
        return $this->selectorType . $this->selector . " {\n" . $directives . "\n}\n";
    }
}