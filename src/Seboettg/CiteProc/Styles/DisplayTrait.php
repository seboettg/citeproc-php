<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;


trait DisplayTrait
{

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

    public function wrapDisplayBlock($text)
    {
        $divStyle = "";
        if ($this->display == "indent") {
            $divStyle = "style=\"text-indent: 0px; padding-left: 45px;\"";
        }
        if ($this->display == "block") {
            $divStyle = "style=\"display: block;\"";
        }

        return empty($divStyle) ? $text : "<div $divStyle>$text</div>";
    }
}