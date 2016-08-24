<?php

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;


/**
 * Class Label
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Label implements RenderingInterface
{

    use AffixesTrait,
        FormattingTrait,
        TextCaseTrait;

    private $variable;

    private $form = "";

    private $plural = "contextual";

    public function __construct($node)
    {
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case "variable":
                    $this->variable = (string) $attribute;
                    break;
                case "form":
                    $this->form = (string) $attribute;
                    break;
                case "plural":
                    $this->plural = (string) $attribute;
                    break;
            }
        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initTextCaseAttributes($node);
    }

    /**
     * @param $data
     * @return string
     */
    public function render($data)
    {
        $label = "";
        $term = CiteProc::getContext()->getLocale()->filter("terms", $this->variable, $this->form);

        if (!is_string($term)) {
            $label = $term->{'single'};;

            if ((is_array($data->{$this->variable}) && $this->plural !== "never") || $this->plural === "always") {
                $label = $term->{'multiple'};
            }
        }
        return $this->addAffixes($this->format($this->applyTextCase($label)));
    }
}