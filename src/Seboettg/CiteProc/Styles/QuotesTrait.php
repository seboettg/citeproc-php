<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Util\StringHelper;

/**
 * Trait QuotesTrait
 *
 * The quotes attribute may set on cs:text. When set to “true” (“false” is default), the rendered text is wrapped in
 * quotes (the quotation marks used are terms). The localized punctuation-in-quote option controls whether an adjoining
 * comma or period appears outside (default) or inside the closing quotation mark.
 *
 * @package Seboettg\CiteProc\Styles
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait QuotesTrait
{

    /**
     * @var bool
     */
    private $quotes = false;

    public function initQuotesAttributes(\SimpleXMLElement $node)
    {
        if (isset($node['quotes']) && "true" === (string) $node['quotes']) {
            $this->quotes = true;
        }
    }

    /**
     * @param string $text
     * @return string
     */
    public function addSurroundingQuotes($text)
    {
        $punctuationInQuote = CiteProc::getContext()
            ->getLocale()
            ->filter('options', 'punctuation-in-quote');
        if ($this->quotes) {
            $openQuote = CiteProc::getContext()->getLocale()->filter("terms", "open-quote")->single;
            $closeQuote = CiteProc::getContext()->getLocale()->filter("terms", "close-quote")->single;
            $text = $this->replaceOuterQuotes($text, $openQuote, $closeQuote);

            if ($punctuationInQuote) {
                return $openQuote . $text . $closeQuote;
            } else {
                $lastChar = substr($text, -1);
                if (preg_match("/[,\.;]/", $text)) {
                    $subStr = substr($text, 0, strlen($text) - 1);
                    return $openQuote . $subStr . $closeQuote . $lastChar;
                }
            }
        }
        return $text;
    }

    /**
     * @param $text
     * @param $outerOpenQuote
     * @param $outerCloseQuote
     * @return string
     */
    private function replaceOuterQuotes($text, $outerOpenQuote, $outerCloseQuote)
    {

        $innerOpenQuote = CiteProc::getContext()->getLocale()->filter("terms", "open-inner-quote")->single;
        $innerCloseQuote = CiteProc::getContext()->getLocale()->filter("terms", "close-inner-quote")->single;

        $text = StringHelper::replaceOuterQuotes($text, "\"", "\"", $innerOpenQuote, $innerCloseQuote);
        $text = StringHelper::replaceOuterQuotes($text, $outerOpenQuote, $outerCloseQuote, $innerOpenQuote, $innerCloseQuote);
        return $text;
    }
}