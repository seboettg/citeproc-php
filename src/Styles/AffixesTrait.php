<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

use Seboettg\CiteProc\CiteProc;
use SimpleXMLElement;

/**
 * Trait AffixesTrait
 * @package Seboettg\CiteProc\Styles
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait AffixesTrait
{

    /**
     * @var string
     */
    private $prefix = "";

    /**
     * @var string
     */
    private $suffix = "";

    /**
     * @var bool
     */
    private $quote = false;

    /**
     * @param SimpleXMLElement $node
     */
    protected function initAffixesAttributes(SimpleXMLElement $node)
    {
        /** @var SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            /** @var string $name */
            $name = (string) $attribute->getName();
            $value = (string) $attribute;

            switch ($name) {
                case 'prefix':
                    $this->prefix = $value;
                    break;
                case 'suffix':
                    $this->suffix = $value;
                    break;
                case 'quote':
                    $this->quote = (bool) $attribute;
            }
        }
    }

    /**
     * @param $text
     * @return string
     */
    protected function addAffixes($text)
    {
        $prefix = $this->prefix;
        $suffix = $this->suffix;

        if (!empty($suffix)) { // guard against repeated suffixes...
            $no_tags = strip_tags($text);
            if (strlen($no_tags) && ($no_tags[(strlen($no_tags) - 1)] == $suffix[0])) {
                $suffix = substr($suffix, 1);
            }

            // punctuation in quote?
            $piq = CiteProc::getContext()
                ->getLocale()
                ->filter('options', 'punctuation-in-quote');
            $punctuationInQuote = is_array($piq) ? current($piq) : $piq;

            if ($punctuationInQuote && in_array($suffix, [',', ';', '.'])) {
                $closeQuote = CiteProc::getContext()->getLocale()->filter("terms", "close-quote")->single;
                $lastChar = mb_substr($text, -1, 1);
                if ($closeQuote === $lastChar) { // last char is closing quote?
                    $text = mb_substr($text, 0, mb_strlen($text) - 1); //set suffix before
                    return $text . $suffix . $lastChar;
                }
            }
        }

        return $prefix . $text . $suffix;
    }

    /**
     * @return string
     */
    public function renderPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function renderSuffix()
    {
        return $this->suffix;
    }
}
