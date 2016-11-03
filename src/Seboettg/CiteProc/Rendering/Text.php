<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Styles\AffixesTrait;
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
        DisplayTrait;

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

    public function render($data)
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
                if (isset($data->{$this->toRenderTypeValue})) {
                    $renderedText = $this->applyTextCase($data->{$this->toRenderTypeValue}, $lang);
                }
                break;
            case 'macro':
                $renderedText = CiteProc::getContext()->getMacro($this->toRenderTypeValue)->render($data);
                break;
            case 'term':
                $renderedText = $this->applyTextCase(CiteProc::getContext()->getLocale()->filter("terms", $this->toRenderTypeValue, $this->form)->single, $lang);
        }
        $text = $this->format($renderedText);
        return $this->addAffixes($text, $this->quotes);
    }
}