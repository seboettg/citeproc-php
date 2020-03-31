<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

use Seboettg\Collection\ArrayList;
use SimpleXMLElement;

/**
 * Trait FormattingTrait
 * @package Seboettg\CiteProc\Styles
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait FormattingTrait
{

    /**
     * @var array
     */
    private static $formattingAttributes = [
        'font-style',
        'font-family',
        'font-weight',
        'font-variant',
        'text-decoration',
        'vertical-align'
    ];

    /**
     * @var ArrayList
     */
    private $formattingOptions;

    /**
     * @var bool
     */
    private $stripPeriods = false;

    /**
     * @var string
     */
    private $format;

    /**
     * @param SimpleXMLElement $node
     */
    protected function initFormattingAttributes(SimpleXMLElement $node)
    {
        $this->formattingOptions = new ArrayList();

        /** @var SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {

            /** @var string $name */
            $name = (string) $attribute->getName();
            $value = (string) $attribute;

            if (in_array($name, self::$formattingAttributes)) {
                $this->formattingOptions->add($name, $value);
                continue;
            }
        }
    }


    protected function format($text)
    {
        if (empty($text)) {
            return $text;
        }

        if (!empty($this->formattingOptions)) {
            $format = "";
            foreach ($this->formattingOptions as $option => $optionValue) {
                if ($optionValue === "italic") {
                    $text = "<i>$text</i>";
                } elseif ($optionValue === "bold") {
                    $text = "<b>$text</b>";
                } elseif ($optionValue === "normal") {
                    $text = "$text";
                } elseif ($option === "vertical-align") {
                    if ($optionValue === "sub") {
                        $text = "<sub>$text</sub>";
                    } elseif ($optionValue === "sup") {
                        $text = "<sup>$text</sup>";
                    }
                } elseif ($option === "text-decoration" && $optionValue === "none") {
                    $format .= "";
                } else {
                    $format .= "$option: $optionValue;";
                }
            }
            if (!empty($format)) {
                $text = '<span style="' . $format . '">' . $text . '</span>';
            }
        }
        return $text;
    }
}
