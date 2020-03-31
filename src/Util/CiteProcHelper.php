<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

use Seboettg\CiteProc\CiteProc;
use stdClass;

class CiteProcHelper
{

    /**
     * Applies additional functions for markup extension
     *
     * @param stdClass $dataItem the actual item
     * @param string $valueToRender value the has to apply on
     * @param string $renderedText actual by citeproc rendered text
     * @return string
     */
    public static function applyAdditionMarkupFunction($dataItem, $valueToRender, $renderedText)
    {
        $markupExtension = CiteProc::getContext()->getMarkupExtension();
        if (array_key_exists($valueToRender, $markupExtension)) {
            $function = $markupExtension[$valueToRender];
            if (is_callable($function)) {
                $renderedText = $function($dataItem, $renderedText);
            }
        } elseif (array_key_exists($mode = CiteProc::getContext()->getMode(), $markupExtension)) {
            if (array_key_exists($valueToRender, $markupExtension[$mode])) {
                $function = CiteProc::getContext()->getMarkupExtension()[$mode][$valueToRender];
                if (is_callable($function)) {
                    $renderedText = $function($dataItem, $renderedText);
                }
            }
        }
        return $renderedText;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function cloneArray(array $array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$key] = clone $value;
        }
        return $newArray;
    }
}
