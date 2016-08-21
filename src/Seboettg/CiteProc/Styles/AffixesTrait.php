<?php

namespace Seboettg\CiteProc\Styles;


trait AffixesTrait
{

    private $prefix;

    private $suffix;

    protected function initAffixesAttributes(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            /** @var string $name */
            $name = (string)$attribute->getName();
            $value = (string)$attribute;

            switch ($name) {
                case 'prefix':
                    $this->prefix = $value;
                    break;
                case 'suffix':
                    $this->suffix = $value;
                    break;
            }
        }
    }

    protected function addAffixes($text)
    {
        return $this->prefix . $text . $this->suffix;
    }
}