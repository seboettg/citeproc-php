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
     * @var string
     */
    private $display;

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
        $this->quotes = new ArrayList();

        foreach ($node->attributes() as $attribute) {
            /** @var string $name */
            $name = (string)$attribute->getName();
            $value = (string)$attribute;
            if (in_array($name, self::$formattingAttributes)) {
                $this->formattingOptions->add($name, $value);
                continue;
            }

            switch ($name) {
                case 'display':
                    $this->display = $value;
                    break;
                case 'text-case':
                    $this->textCase = $value;
                    break;
                case 'strip-periods':
                    $this->stripPeriods = $value;
                    break;
            }

        }
        $this->initFormattingOptions();
    }

    protected function initFormattingOptions()
    {
        $this->noOp = true;
        $this->format = '';
        //if (isset($this->quotes) && strtolower($this->quotes) == "true") {
            //TODO: locales in static context
            /*
            $this->quotes = array();
            $this->quotes['punctuation-in-quote'] = $this->citeProc->getLocale()->locale('style_option', 'punctuation-in-quote');
            $this->quotes['open-quote'] = $this->citeProc->getLocale()->locale('term', 'open-quote');
            $this->quotes['close-quote'] = $this->citeProc->getLocale()->locale('term', 'close-quote');
            $this->quotes['open-inner-quote'] = $this->citeProc->getLocale()->locale('term', 'open-inner-quote');
            $this->quotes['close-inner-quote'] = $this->citeProc->getLocale()->locale('term', 'close-inner-quote');
            $this->noOp = false;
            */
        //}
        /*
        if (isset($this->{'prefix'})) {
            $this->noOp = false;
        }
        if (isset($this->{'suffix'})) {
            $this->noOp = false;
        }
        if (isset($this->{'display'})) {
            $this->noOp = false;
        }
        */

        foreach ($this->formattingOptions as $key => $value) {
            $this->format .= $key . ": " . $value;
        }


        if (isset($this->{'text-case'}) || !empty($this->format) || !empty($this->span_class) || !empty($this->div_class)) {
            $this->noOp = false;
        }
    }

    protected function format($text) {

        if (empty($text)) {
            return $text;
        }
        /*
        $quotes = $this->{'quotes'};
        $quotes = is_array($quotes) ? $quotes : array();

        if (isset($this->{'text-case'})) {
            switch ($this->{'text-case'}) {
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
            }
        }

        */

        /*
        $prefix = $this->prefix;
        $prefix .= isset($quotes['open-quote']) ? $quotes['open-quote'] : '';

        $suffix = $this->suffix;


        if (isset($quotes['close-quote']) && !empty($suffix) && isset($quotes['punctuation-in-quote'])) {
            if (strpos($suffix, '.') !== FALSE || strpos($suffix, ',') !== FALSE) {
                $suffix = $suffix . $quotes['close-quote'];
            }
        } elseif (isset($quotes['close-quote'])) {
            $suffix = $quotes['close-quote'] . $suffix;
        }

        if (!empty($suffix)) { // gaurd against repeaded suffixes...
            $no_tags = strip_tags($text);
            if (strlen($no_tags) && ($no_tags[(strlen($no_tags) - 1)] == $suffix[0])) {
                $suffix = substr($suffix, 1);
            }
        }
        */

        if (!empty($this->format) || !empty($this->span_class)) {
            $style = (!empty($this->format)) ? 'style="' . $this->format . '" ' : '';
            $class = (!empty($this->span_class)) ? 'class="' . $this->span_class . '"' : '';
            $text = '<span ' . $class . $style . '>' . $text . '</span>';
        }

        /*
        $divClass = (!empty($this->divClass)) ? 'class="' . $this->divClass . '"' : '';
        $divStyle = ($this->display === "indent") ? 'style="text-indent: 0px; padding-left: 45px;"' : '';

        if (!empty($divClass) || !empty($divStyle)) {
            return '<div ' . $divClass . $divStyle . '>' . $prefix . $text . $suffix . '</div>';
        }
        */
        return $text;
    }
}