<?php

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Styles\FormattingTrait;


/**
 * Class Term
 * @package Seboettg\CiteProc\Node\Style
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Text implements RenderingInterface
{
    use FormattingTrait;

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
    }

    public function render($data)
    {
        $renderedText = "";
        switch ($this->toRenderType) {
            case 'value':
                $renderedText = $this->toRenderTypeValue;
                break;
            case 'variable':
                if (isset($data->{$this->toRenderTypeValue})) {
                    $renderedText = $data->{$this->toRenderTypeValue};
                }
                break;
            case 'macro':
                $renderedText = CiteProc::getContext()->getMacro($this->toRenderTypeValue)->render($data);
                break;
            case 'term':
                $renderedText = CiteProc::getContext()->getLocale()->filter("terms", $this->toRenderTypeValue)->single;
        }

        return $this->format($renderedText);
    }
}