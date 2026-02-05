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
use Seboettg\CiteProc\Util\StringHelper;
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

        // Handle suffix with consecutive punctuation prevention
        do {
            if (empty($suffix)) break;
            // Get first non-space char of suffix
            $suffixFirst = mb_substr(preg_replace('/\p{Z}/u', '', $suffix), 0, 1);
            if (empty($suffixFirst)) break;
            $noTags = strip_tags($text);
            if (empty($noTags)) break;
            // Normalize equivalent punctuation characters
            if (isset(StringHelper::PUN_SAME[$suffixFirst])) {
                $noTags = strtr($noTags, StringHelper::PUN_SAME[$suffixFirst]);
            }
            if (empty($noTags)) break;
            // If last char of text equals first non-space char of delimiter
            if (mb_substr($noTags, -1) == $suffixFirst) {
                // Strip first non-space char of suffix to avoid duplication
                $suffix = mb_substr($suffix, mb_strpos($suffix, $suffixFirst) + 1);
                break;
            }

            // Check punctuation-in-quote locale option
            $piq = CiteProc::getContext()
                ->getLocale()
                ->filter('options', 'punctuation-in-quote');
            $punctuationInQuote = is_array($piq) ? current($piq) : $piq;

            if ($punctuationInQuote && in_array($suffix, [',', ';', '.'])) {
                $closeQuote = CiteProc::getContext()->getLocale()->filter("terms", "close-quote")->single;
                $lastChar = mb_substr($text, -1, 1);
                if ($closeQuote === $lastChar) { // Last char is closing quote
                    $text = mb_substr($text, 0, mb_strlen($text) - 1); // Insert suffix before closing quote
                    return $prefix . $text . $suffix . $lastChar;
                }
            }
        } while (false);

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
