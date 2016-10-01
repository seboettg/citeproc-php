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

    /**
     * Selects the form of the term, with allowed values:
     *
     *   - “long” - (default), e.g. “page”/”pages” for the “page” term
     *   - “short” - e.g. “p.”/”pp.” for the “page” term
     *   - “symbol” - e.g. “§”/”§§” for the “section” term
     *
     * @var string
     */
    private $form = "";

    /**
     * Sets pluralization of the term, with allowed values:
     *
     *   - “contextual” - (default), the term plurality matches that of the variable content. Content is considered
     *     plural when it contains multiple numbers (e.g. “page 1”, “pages 1-3”, “volume 2”, “volumes 2 & 4”), or, in
     *     the case of the “number-of-pages” and “number-of-volumes” variables, when the number is higher than 1
     *     (“1 volume” and “3 volumes”).
     *   - “always” - always use the plural form, e.g. “pages 1” and “pages 1-3”
     *   - “never” - always use the singular form, e.g. “page 1” and “page 1-3”
     *
     * @var string
     */
    private $plural = "contextual";

    /**
     * Label constructor.
     * @param \SimpleXMLElement $node
     */
    public function __construct(\SimpleXMLElement $node)
    {
        /** @var \SimpleXMLElement $attribute */
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
        $text = '';
        $variables = explode(' ', $this->variable);
        $form = !empty($this->form) ? $this->form : 'long';
        $plural = "";
        switch ($this->plural) {
            case 'never':
                $plural = 'single';
                break;
            case 'always':
                $plural = 'multiple';
                break;
            case 'contextual':
            default:
        }
        foreach ($variables as $variable) {

            if (isset($data->{$variable})) {
                if ((!isset($this->plural) || empty($plural)) && is_array($data->{$variable})) {
                    $count = count($data->{$variable});
                    if ($count == 1) {
                        $plural = 'single';
                    } elseif ($count > 1) {
                        $plural = 'multiple';
                    }
                } else {
                    if ($this->plural != "always") {
                        $plural = $this->evaluateStringPluralism($data, $variable);
                    }
                }
                $term = CiteProc::getContext()->getLocale()->filter('terms', $variable, $form);
                $var = $data->{$variable};
                $pluralForm = $term->{$plural};
                if (!empty($var) && !empty($pluralForm)) {
                    $text = $pluralForm;
                    break;
                }
            }
        }
        if (empty($text)) {
            return "";
        }
        if ($this->stripPeriods) {
            $text = str_replace('.', '', $text);
        }
        $text = $this->format($this->applyTextCase($text));
        return $this->addAffixes($text);
    }


    private function evaluateStringPluralism($data, $variable)
    {
        $str = $data->{$variable};
        $plural = 'single';
        if (!empty($str)) {
//      $regex = '/(?:[0-9],\s*[0-9]|\s+and\s+|&|([0-9]+)\s*[\-\x2013]\s*([0-9]+))/';
            switch ($variable) {
                case 'page':
                    $pageRegex = "/([a-zA-Z]*)([0-9]+)\s*(?:–|-)\s*([a-zA-Z]*)([0-9]+)/";
                    $err = preg_match($pageRegex, $str, $m);
                    if ($err !== false && count($m) == 0) {
                        $plural = 'single';
                    } elseif ($err !== false && count($m)) {
                        $plural = 'multiple';
                    }
                    break;
                default:
                    if (is_numeric($str)) {
                        return $str > 1 ? 'multiple' : 'single';
                    }
            }
        }
        return $plural;
    }

    /**
     * @param string $variable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
    }
}