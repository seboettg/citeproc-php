<?php
/*
 * citeproc-php
 *
 * @link      http://github.com/seboettg/citeproc-php for the source repository
 * @copyright Copyright (c) 2016 Sebastian Böttger.
 * @license   https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

use Seboettg\CiteProc\Util\StringHelper;
use SimpleXMLElement;

/**
 * Trait TextCase
 *
 * @package Seboettg\CiteProc\Styles
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait TextCaseTrait
{

    private $textCase;

    protected function initTextCaseAttributes(SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            /**
             * @var string $name
             */
            $name = $attribute->getName();
            $value = (string) $attribute;

            switch ($name) {
                case 'text-case':
                    $this->textCase = $value;
                    return;
            }
        }
    }

    /**
     * @param  string $text
     * @param  string $lang
     * @return string
     */
    public function applyTextCase($text, $lang = "en")
    {

        switch ($this->textCase) {
            case 'uppercase':
                $text = $this->keepNoCase(mb_strtoupper($text), $text);
                break;
            case 'lowercase':
                $text = $this->keepNoCase(mb_strtolower($text), $text);
                break;
            case 'sentence':
                if (StringHelper::checkUpperCaseString($text)) {
                    $text = mb_strtolower($text);
                    return StringHelper::mb_ucfirst($text);
                } else {
                    return StringHelper::mb_ucfirst($text);
                }
                break;
            case 'capitalize-all':
                $text = $this->keepNoCase(StringHelper::capitalizeAll($text), $text);
                break;
            case 'title':
                if ($lang === "en") {
                    $text = $this->keepNoCase(StringHelper::capitalizeForTitle($text), $text);
                }
                break;
            case 'capitalize-first':
                $text = $this->keepNoCase(StringHelper::mb_ucfirst($text), $text);
                break;
            default:
                break;
        }

        return $text;
    }


    /**
     * @param  string $render
     * @param  string $original
     * @return string|string[]|null
     */
    private function keepNoCase($render, $original)
    {
        if (preg_match('/<span class=\"nocase\">(\p{L}+)<\/span>/i', $original, $match)) {
            return preg_replace('/(<span class=\"nocase\">\p{L}+<\/span>)/i', $match[1], $render);
        }
        return $render;
    }
}
