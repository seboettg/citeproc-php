<?php

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
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
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

    public function __construct(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            $name = $attribute->getName();
            if (in_array($name, ['value', 'variable', 'macro', 'term'])) {
                $this->toRenderType = $name;
                $this->toRenderTypeValue = (string) $attribute;
                break;
            }
        }
        $this->initFormattingAttributes($node);
        $this->initDisplayAttributes($node);
        $this->initTextCaseAttributes($node);
        $this->initAffixesAttributes($node);
    }

    public function render($data)
    {
        $renderedText = "";
        switch ($this->toRenderType) {
            case 'value':
                $renderedText = $this->applyTextCase($this->toRenderTypeValue);
                break;
            case 'variable':
                if (isset($data->{$this->toRenderTypeValue})) {
                    $renderedText = $this->applyTextCase($data->{$this->toRenderTypeValue});
                }
                break;
            case 'macro':
                $renderedText = CiteProc::getContext()->getMacro($this->toRenderTypeValue)->render($data);
                break;
            case 'term':
                $renderedText = $this->applyTextCase(CiteProc::getContext()->getLocale()->filter("terms", $this->toRenderTypeValue)->single);
        }
        $text = $this->format($renderedText);
        return $this->addAffixes($text, $this->quotes);
    }
}