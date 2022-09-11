<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles\Css;

use Seboettg\Collection\Lists\ListInterface;
use function Seboettg\Collection\Lists\emptyList;

/**
 * Class CssRule
 * @package Seboettg\CiteProc\Styles\Css
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class CssRule
{
    const SELECTOR_TYPE_ID = "#";

    const SELECTOR_TYPE_CLASS = ".";

    private string $selectorType;

    private string $selector;

    private ListInterface $directives;

    public function __construct(string $selector, string $selectorType = self::SELECTOR_TYPE_CLASS)
    {
        $this->selector = $selector;
        $this->selectorType = $selectorType;
        $this->directives = emptyList();
    }

    public function addDirective(string $property, string $value)
    {
        $this->directives->add("$property: $value;");
    }

    public function __toString(): string
    {
        $directives = "\t".implode("\n\t", $this->directives->toArray());
        return $this->selectorType.$this->selector." {\n".$directives."\n}\n";
    }
}
