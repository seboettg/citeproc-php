<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\ConsecutivePunctuationCharacterTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\QuotesTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;


/**
 * Class Term
 * @package Seboettg\CiteProc\Node\Style
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Text implements RenderingInterface
{
    use FormattingTrait,
        AffixesTrait,
        TextCaseTrait,
        DisplayTrait,
        ConsecutivePunctuationCharacterTrait;

    /**
     * @var string
     */
    private $toRenderType;

    /**
     * @var string
     */
    private $toRenderTypeValue;

    private $form = "long";

    public function __construct(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            $name = $attribute->getName();
            if (in_array($name, ['value', 'variable', 'macro', 'term'])) {
                $this->toRenderType = $name;
                $this->toRenderTypeValue = (string) $attribute;
            }
            if ($name === "form") {
                $this->form = (string) $attribute;
            }
        }
        $this->initFormattingAttributes($node);
        $this->initDisplayAttributes($node);
        $this->initTextCaseAttributes($node);
        $this->initAffixesAttributes($node);
    }

    /**
     * @param \stdClass $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $lang = (isset($data->language) && $data->language != 'en') ? $data->language : 'en';

        $renderedText = "";
        switch ($this->toRenderType) {
            case 'value':
                $renderedText = $this->applyTextCase($this->toRenderTypeValue, $lang);
                break;
            case 'variable':
                // check if there is an attribute with prefix short or long e.g. shortTitle or longAbstract
                // test case group_ShortOutputOnly.json
                if (in_array($this->form, ["short", "long"])) {
                    $attrWithPrefix = $this->form . ucfirst($this->toRenderTypeValue);
                    if (isset($data->{$attrWithPrefix}) && !empty($data->{$attrWithPrefix})) {
                        $renderedText = $this->applyTextCase($data->{$attrWithPrefix}, $lang);
                    }
                }
                if (!empty($data->{$this->toRenderTypeValue})) {
                    $renderedText = $this->applyTextCase($data->{$this->toRenderTypeValue}, $lang);
                }
                // for test sort_BibliographyCitationNumberDescending.json
                if ($this->toRenderTypeValue === "citation-number" && !is_null($citationNumber)) {
                    $renderedText = strval($citationNumber + 1);
                }
                break;
            case 'macro':
                $macro = CiteProc::getContext()->getMacro($this->toRenderTypeValue);
                if (is_null($macro)) {
                    try {
                        throw new CiteProcException("Macro \"" . $this->toRenderTypeValue . "\" does not exist.");
                    } catch (CiteProcException $e) {
                        $renderedText = "";
                    }
                } else {
                    $renderedText = $macro->render($data);
                }
                break;
            case 'term':
                $term = CiteProc::getContext()->getLocale()->filter("terms", $this->toRenderTypeValue, $this->form)->single;
                $renderedText = !empty($term) ? $this->applyTextCase($term, $lang) : "";
        }
        if (!empty($renderedText)) {
            //$renderedText = $this->applyTextCase($renderedText);
            $text = $this->format($renderedText);
            $res = $this->addAffixes($text, $this->quotes);
            if (!empty($res)) {
                $res = $this->removeConsecutiveChars($res);
            }
            return $res;
        }
        return "";
    }
}