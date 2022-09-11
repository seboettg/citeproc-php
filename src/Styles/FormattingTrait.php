<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

use Seboettg\Collection\Map\MapInterface;
use SimpleXMLElement;
use function Seboettg\Collection\Map\emptyMap;

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
    private static array $formattingAttributes = [
        'font-style',
        'font-family',
        'font-weight',
        'font-variant',
        'text-decoration',
        'vertical-align'
    ];

    private MapInterface $formattingOptions;
    private bool $stripPeriods = false;
    private string $format;

    /**
     * @param SimpleXMLElement $node
     */
    protected function initFormattingAttributes(SimpleXMLElement $node)
    {
        $this->formattingOptions = emptyMap();

        /** @var SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            $name = $attribute->getName();
            $value = (string) $attribute;
            if (in_array($name, self::$formattingAttributes)) {
                $this->formattingOptions->put($name, $value);
            }
        }
    }


    protected function format($text)
    {
        if (empty($text)) {
            return $text;
        }

        if ($this->formattingOptions->count() > 0) {
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
                $text = sprintf("<span style=\"%s\">%s</span>", $format, $text);
            }
        }
        return $text;
    }
}
