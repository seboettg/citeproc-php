<?php

namespace Seboettg\CiteProc\Styles;

use Seboettg\CiteProc\CiteProc;

trait AffixesTrait
{

    private $prefix = "";

    private $suffix = "";

    private $quotes = false;

    protected function initAffixesAttributes(\SimpleXMLElement $node)
    {
        /** @var \SimpleXMLElement $attribute */
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
                case 'quote':
                    $this->quotes = (bool) $attribute;
            }
        }
    }

    protected function addAffixes($text)
    {
        if ($this->quotes) {
            $openQuotes = CiteProc::getContext()->getLocale()->filter("terms", "open-quote")->single;
            $closeQuote = CiteProc::getContext()->getLocale()->filter("terms", "close-quote")->sinlge;
            $punctuationInQuote = CiteProc::getContext()->getLocale()->filter("terms", "punctuation-in-quote")->single;
        }


        $prefix = $this->prefix;
        $prefix .= isset($openQuotes) ? $openQuotes : '';
        $suffix = $this->suffix;
        if (isset($closeQuote) && !empty($suffix) && isset($punctuationInQuote)) {
            if (strpos($suffix, '.') !== false || strpos($suffix, ',') !== false) {
                $suffix = $suffix . $closeQuote;
            }
        } elseif (isset($closeQuote)) {
            $suffix = $closeQuote . $suffix;
        }
        if (!empty($suffix)) { // guard against repeated suffixes...
            $no_tags = strip_tags($text);
            if (strlen($no_tags) && ($no_tags{(strlen($no_tags) - 1)} == $suffix{0})) {
                $suffix = substr($suffix, 1);
            }
        }

        return $prefix . $text . $suffix;
    }
}