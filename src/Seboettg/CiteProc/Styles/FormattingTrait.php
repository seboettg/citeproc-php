<?php

namespace Seboettg\CiteProc\Styles;


use Seboettg\Collection\ArrayList;

trait FormattingTrait
{

    /**
     * @var array
     */
    static $formattingAttributes = ['font-style', 'font-family', 'font-weight', 'font-variant', 'text-decoration', 'vertical-align'];

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

    protected function initFormattingAttributes(\SimpleXMLElement $node)
    {
        $this->formattingOptions = new ArrayList();

        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {

            /** @var string $name */
            $name = (string)$attribute->getName();
            $value = (string)$attribute;

            if (in_array($name, self::$formattingAttributes)) {
                $this->formattingOptions->add($name, $value);
                continue;
            }
        }
        $this->initFormattingOptions();
    }

    protected function initFormattingOptions()
    {
        $this->format = "";
        foreach ($this->formattingOptions as $key => $value) {
            $this->format .= $key . ": " . $value;
        }
    }

    protected function format($text)
    {
        if (empty($text)) {
            return $text;
        }

        if (!empty($this->format) || !empty($this->span_class)) {
            $style = (!empty($this->format)) ? 'style="' . $this->format . '" ' : '';
            $class = (!empty($this->span_class)) ? 'class="' . $this->span_class . '"' : '';
            $text = '<span ' . $class . $style . '>' . $text . '</span>';
        }
        return $text;
    }
}