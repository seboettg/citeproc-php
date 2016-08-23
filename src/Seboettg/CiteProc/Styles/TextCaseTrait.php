<?php

namespace Seboettg\CiteProc\Styles;


/**
 * Trait TextCase
 * @package Seboettg\CiteProc\Styles
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
trait TextCaseTrait
{

    private $textCase;

    protected function initTextCaseAttributes(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            /** @var string $name */
            $name = $attribute->getName();
            $value = (string) $attribute;

            switch ($name) {
                case 'text-case':
                    $this->textCase = $value;
                    return;
            }
        }
    }

    public function applyTextCase($text)
    {
        switch ($this->textCase) {
            case 'uppercase':
                $text = mb_strtoupper($text);
                break;
            case 'lowercase':
                $text = mb_strtolower($text);
                break;
            case 'capitalize-all':
            case 'title':
                $text = mb_convert_case($text, MB_CASE_TITLE);
                break;
            case 'capitalize-first':
                $chr1 = mb_strtoupper(mb_substr($text, 0, 1));
                $text = $chr1 . mb_substr($text, 1);
                break;
            default:

        }

        return $text;
    }
}