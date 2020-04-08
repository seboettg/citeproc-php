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
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;
use SimpleXMLElement;
use stdClass;

/**
 * Class Label
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Label implements Rendering
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
     * @param SimpleXMLElement $node
     */
    public function __construct(SimpleXMLElement $node)
    {
        /** @var SimpleXMLElement $attribute */
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
     * @param stdClass $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $lang = (isset($data->language) && $data->language != 'en') ? $data->language : 'en';

        $text = '';
        $variables = explode(' ', $this->variable);
        $form = !empty($this->form) ? $this->form : 'long';
        $plural = $this->defaultPlural();

        if ($this->variable === "editortranslator") {
            if (isset($data->editor) && isset($data->translator)) {
                $plural = $this->getPlural($data, $plural, "editortranslator");
                $term = CiteProc::getContext()->getLocale()->filter('terms', "editortranslator", $form);
                $pluralForm = $term->{$plural};
                if (!empty($pluralForm)) {
                    $text = $pluralForm;
                }
            }
        } elseif ($this->variable === "locator") {
            $citationItem = CiteProc::getContext()->getCitationItemById($data->id);
            if (!empty($citationItem->label)) {
                $plural = $this->evaluateStringPluralism($citationItem->locator, $citationItem->label);
                $term = CiteProc::getContext()->getLocale()->filter('terms', $citationItem->label, $form);
                $pluralForm = $term->{$plural};
                if (!empty($citationItem->locator) && !empty($pluralForm)) {
                    $text = $pluralForm;
                }
            }
        } else {
            foreach ($variables as $variable) {
                if (isset($data->{$variable})) {
                    $plural = $this->getPlural($data, $plural, $variable);
                    $term = CiteProc::getContext()->getLocale()->filter('terms', $variable, $form);
                    $pluralForm = $term->{$plural};
                    if (!empty($data->{$variable}) && !empty($pluralForm)) {
                        $text = $pluralForm;
                        break;
                    }
                }
            }
        }

        return $this->formatting($text, $lang);
    }

    /**
     * @param string $str
     * @param string $variable
     * @return string
     */
    private function evaluateStringPluralism($str, $variable)
    {
        $plural = 'single';
        if (!empty($str)) {
            switch ($variable) {
                case 'page':
                case 'chapter':
                case 'folio':
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

    /**
     * @param $data
     * @param $plural
     * @param $variable
     * @return string
     */
    protected function getPlural($data, $plural, $variable)
    {

        if ($variable === "editortranslator" && isset($data->editor)) {
            $var = $data->editor;
        } else {
            $var = $data->{$variable};
        }
        if (((!isset($this->plural) || empty($plural))) && !empty($var)) {
            if (is_array($var)) {
                $count = count($var);
                if ($count == 1) {
                    $plural = 'single';
                    return $plural;
                } elseif ($count > 1) {
                    $plural = 'multiple';
                    return $plural;
                }
                return $plural;
            } else {
                return $this->evaluateStringPluralism($data->{$variable}, $variable);
            }
        } else {
            if ($this->plural != "always") {
                $plural = $this->evaluateStringPluralism($data->{$variable}, $variable);
                return $plural;
            }
            return $plural;
        }
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param string $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @param $text
     * @param $lang
     * @return string
     */
    protected function formatting($text, $lang)
    {
        if (empty($text)) {
            return "";
        }
        if ($this->stripPeriods) {
            $text = str_replace('.', '', $text);
        }

        $text = preg_replace("/\s&\s/", " &#38; ", $text); //replace ampersands by html entity
        $text = $this->format($this->applyTextCase($text, $lang));
        return $this->addAffixes($text);
    }

    /**
     * @return string
     */
    protected function defaultPlural()
    {
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
        return $plural;
    }
}
