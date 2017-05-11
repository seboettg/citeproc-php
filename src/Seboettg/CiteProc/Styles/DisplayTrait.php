<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

/**
 * Trait DisplayTrait
 * @package Seboettg\CiteProc\Styles
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait DisplayTrait
{

    /**
     * @var array
     */
    private static $allowedValues = [
        "block",
        "left-margin",
        "right-inline",
        "indent"
    ];

    /**
     * @var string
     */
    private $display;

    /**
     * @param $node
     */
    public function initDisplayAttributes(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'display':
                    $this->display = (string) $attribute;
                    return;
            }
        }
    }

    /**
     * @param $text
     * @return string
     */
    public function wrapDisplayBlock($text)
    {
        if (!in_array($this->display, self::$allowedValues)) {
            return $text;
        }
        $divStyle = "class=\"csl-" . $this->display . "\"";
        return "<div $divStyle>$text</div>";
    }
}