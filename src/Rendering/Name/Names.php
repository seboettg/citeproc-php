<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Rendering\Label;
use Seboettg\CiteProc\Rendering\Rendering;
use Seboettg\CiteProc\RenderingState;
use Seboettg\CiteProc\Style\InheritableNameAttributesTrait;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\CiteProc\Util\NameHelper;
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;
use stdClass;

/**
 * Class Names
 *
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Names implements Rendering, HasParent
{
    use DelimiterTrait,
        AffixesTrait,
        FormattingTrait,
        InheritableNameAttributesTrait;

    /**
     * Variables (selected with the required variable attribute), each of which can contain multiple names (e.g. the
     * “author” variable contains all the author names of the cited item). If multiple variables are selected
     * (separated by single spaces, see example below), each variable is independently rendered in the order specified.
     *
     * @var ArrayList
     */
    private $variables;

    /**
     * The Name element, an optional child element of Names, can be used to describe the formatting of individual
     * names, and the separation of names within a name variable.
     *
     * @var Name
     */
    private $name;

    /**
     * The optional Label element must be included after the Name and EtAl elements, but before
     * the Substitute element. When used as a child element of Names, Label does not carry the variable
     * attribute; it uses the variable(s) set on the parent Names element instead.
     *
     * @var Label
     */
    private $label;

    /**
     * The optional Substitute element, which must be included as the last child element of Names, adds
     * substitution in case the name variables specified in the parent cs:names element are empty. The substitutions
     * are specified as child elements of Substitute, and must consist of one or more rendering elements (with the
     * exception of Layout). A shorthand version of Names without child elements, which inherits the attributes
     * values set on the cs:name and EtAl child elements of the original Names element, may also be used. If
     * Substitute contains multiple child elements, the first element to return a non-empty result is used for
     * substitution. Substituted variables are suppressed in the rest of the output to prevent duplication. An example,
     * where an empty “author” name variable is substituted by the “editor” name variable, or, when no editors exist,
     * by the “title” macro:
     *
     * <macro name="author">
     *     <names variable="author">
     *         <substitute>
     *             <names variable="editor"/>
     *             <text macro="title"/>
     *         </substitute>
     *     </names>
     * </macro>
     *
     * @var Substitute
     */
    private $substitute;

    /**
     * Et-al abbreviation, controlled via the et-al-... attributes (see Name), can be further customized with the
     * optional cs:et-al element, which must follow the cs:name element (if present). The term attribute may be set to
     * either “et-al” (the default) or to “and others” to use either term. The formatting attributes may also be used,
     * for example to italicize the “et-al” term:
     *
     * @var EtAl
     */
    private $etAl;

    /**
     * The delimiter attribute may be set on cs:names to separate the names of the different name variables (e.g. the
     * semicolon in “Doe, Smith (editors); Johnson (translator)”).
     *
     * @var string
     */
    private $delimiter = ", ";

    private $parent;

    /**
     * Names constructor.
     *
     * @param  SimpleXMLElement $node
     * @param  $parent
     * @throws InvalidStylesheetException
     */
    public function __construct(SimpleXMLElement $node, $parent)
    {
        $this->initInheritableNameAttributes($node);
        $this->parent = $parent;
        /**
         * @var SimpleXMLElement $child
         */
        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                case "name":
                    $this->name = Factory::create($child, $this);
                    break;
                case "label":
                    $this->label = Factory::create($child);
                    break;
                case "substitute":
                    $this->substitute = new Substitute($child, $this);
                    break;
                case "et-al":
                    $this->etAl = Factory::create($child);
            }
        }

        /**
         * @var SimpleXMLElement $attribute
         */
        foreach ($node->attributes() as $attribute) {
            if ("variable" === $attribute->getName()) {
                $this->variables = new ArrayList(...explode(" ", (string) $attribute));
                break;
            }
        }

        $this->initDelimiterAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initFormattingAttributes($node);
    }

    /**
     * This outputs the contents of one or more name variables (selected with the required variable attribute), each
     * of which can contain multiple names (e.g. the “author” variable contains all the author names of the cited item).
     * If multiple variables are selected (separated by single spaces), each variable is independently rendered in the
     * order specified, with one exception: when the selection consists of “editor” and “translator”, and when the
     * contents of these two name variables is identical, then the contents of only one name variable is rendered. In
     * addition, the “editortranslator” term is used if the Names element contains a Label element, replacing the
     * default “editor” and “translator” terms (e.g. resulting in “Doe (editor & translator)”).
     *
     * @param  stdClass $data
     * @param  int|null $citationNumber
     * @return string
     * @throws CiteProcException
     */
    public function render($data, $citationNumber = null)
    {
        $str = "";

        /* when the selection consists of “editor” and “translator”, and when the contents of these two name variables
        is identical, then the contents of only one name variable is rendered. In addition, the “editortranslator”
        term is used if the cs:names element contains a cs:label element, replacing the default “editor” and
        “translator” terms (e.g. resulting in “Doe (editor & translator)”) */
        if ($this->variables->hasElement("editor") && $this->variables->hasElement("translator")) {
            if (isset($data->editor)
                && isset($data->translator) && NameHelper::sameNames($data->editor, $data->translator)
            ) {
                if (isset($this->name)) {
                    $str .= $this->name->render($data, 'editor');
                } else {
                    $arr = [];
                    foreach ($data->editor as $editor) {
                        $edt = $this->format($editor->family.", ".$editor->given);
                        $results[] = NameHelper::addExtendedMarkup('editor', $editor, $edt);
                    }
                    $str .= implode($this->delimiter, $arr);
                }
                if (isset($this->label)) {
                    $this->label->setVariable("editortranslator");
                    $str .= $this->label->render($data);
                }
                $vars = $this->variables->toArray();
                $vars = array_filter($vars, function ($value) {
                    return !($value === "editor" || $value === "translator");
                });
                $this->variables->setArray($vars);
            }
        }

        $results = [];
        foreach ($this->variables as $var) {
            if (!empty($data->{$var})) {
                if (!empty($this->name)) {
                    $res = $this->name->render($data, $var, $citationNumber);
                    $name = $res;
                    if (!empty($this->label)) {
                        $name = $this->appendLabel($data, $var, $name);
                    }
                    //add multiple counting values
                    if (is_numeric($name) && $this->name->getForm() === "count") {
                        $results = $this->addCountValues($res, $results);
                    } else {
                        $results[] = $this->format($name);
                    }
                } else {
                    foreach ($data->{$var} as $name) {
                        $formatted = $this->format($name->given." ".$name->family);
                        $results[] = NameHelper::addExtendedMarkup($var, $name, $formatted);
                    }
                }
                // suppress substituted variables
                if (CiteProc::getContext()->getRenderingState()->getValue() === RenderingState::SUBSTITUTION) {
                    unset($data->{$var});
                }
            } else {
                if (!empty($this->substitute)) {
                    $results[] = $this->substitute->render($data);
                }
            }
        }
        $results = $this->filterEmpty($results);
        $str .= implode($this->delimiter, $results);
        return !empty($str) ? $this->addAffixes($str) : "";
    }


    /**
     * @param  $data
     * @param  $var
     * @param  $name
     * @return string
     */
    private function appendLabel($data, $var, $name)
    {
        $this->label->setVariable($var);
        if (in_array($this->label->getForm(), ["verb", "verb-short"])) {
            $name = $this->label->render($data).$name;
        } else {
            $name .= $this->label->render($data);
        }
        return $name;
    }

    /**
     * @param  $res
     * @param  $results
     * @return array
     */
    private function addCountValues($res, $results)
    {
        $lastElement = current($results);
        $key = key($results);
        if (!empty($lastElement)) {
            $lastElement += $res;
            $results[$key] = $lastElement;
        } else {
            $results[] = $res;
        }
        return $results;
    }

    /**
     * @return bool
     */
    public function hasEtAl()
    {
        return !empty($this->etAl);
    }

    /**
     * @return EtAl
     */
    public function getEtAl()
    {
        return $this->etAl;
    }

    /**
     * @param  EtAl $etAl
     * @return $this
     */
    public function setEtAl(EtAl $etAl)
    {
        $this->etAl = $etAl;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasName()
    {
        return !empty($this->name);
    }

    /**
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  Name $name
     * @return $this
     */
    public function setName(Name $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return ArrayList
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @return bool
     */
    public function hasLabel()
    {
        return !empty($this->label);
    }

    /**
     * @return Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param Label $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    private function filterEmpty(array $results)
    {
        return array_filter($results, function ($item) {
            return !empty($item);
        });
    }
}
