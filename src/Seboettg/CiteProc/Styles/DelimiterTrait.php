<?php

namespace Seboettg\CiteProc\Styles;


trait DelimiterTrait
{

    private $delimiter = "";

    protected function initDelimiterAttributes(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            /** @var string $name */
            $name = (string)$attribute->getName();
            $value = (string)$attribute;

            switch ($name) {
                case 'delimiter':
                    $this->delimiter = $value;
                    return;
            }
        }
    }
}