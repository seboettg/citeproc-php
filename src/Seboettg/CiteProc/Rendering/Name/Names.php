<?php

namespace Seboettg\CiteProc\Rendering\Name;

use Seboettg\CiteProc\Rendering\Label;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Group
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Names implements RenderingInterface
{
    use DelimiterTrait;
    use AffixesTrait;
    use FormattingTrait;

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

    /**
     * Names constructor.
     * @param \SimpleXMLElement $node
     */
    public function __construct(\SimpleXMLElement $node)
    {

        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {

            switch ($child->getName()) {
                case "name":
                    $this->name = Factory::create($child, $this);
                    break;
                case "label":
                    $this->label = Factory::create($child);
                    break;
                case "substitute":
                    $this->substitute = Factory::create($child);
                    break;
                case "et-al":
                    $this->etAl = Factory::create($child);
            }
        }

        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            if ("variable" === $attribute->getName()) {
                $this->variables = new ArrayList(explode(" ", (string)$attribute));
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
     * @param $data
     * @return string
     */
    public function render($data)
    {
        $str = "";
        $matches = [];

        /* when the selection consists of “editor” and “translator”, and when the contents of these two name variables
        is identical, then the contents of only one name variable is rendered. In addition, the “editortranslator”
        term is used if the cs:names element contains a cs:label element, replacing the default “editor” and
        “translator” terms (e.g. resulting in “Doe (editor & translator)”) */
        if ($this->variables->hasValue("editor") && $this->variables->hasValue("translator")) {
            if (isset($data->editor) && isset($data->translator)) {
                if (isset($name)) {
                    $str .= $this->name->render($data->editor);
                } else {
                    $arr = [];
                    foreach ($data->editor as $editor) {
                        $arr[] = $this->format($editor->family . ", " . $editor->given);
                    }
                    $str .= implode($this->delimiter, $arr);
                }
                if (isset($this->label)) {
                    $this->label->setVariable("editortranslator");
                    $str .= $this->label->render("");
                }
                $vars = $this->variables->toArray();
                $vars = array_filter($vars, function($value) {
                    return !($value === "editor" || $value === "translator");
                });
                $this->variables->setArray($vars);
            }
        }

        foreach ($this->variables as $variable) {
            if (isset($data->{$variable}) && (!empty($data->{$variable}))) {
                $matches[] = $variable;
            }
        }



        $results = [];
        foreach ($matches as $var) {

            if (!empty($data->{$var})) {
                if (!empty($this->name)) {
                    $name = $this->name->render($data->{$var});
                    if (!empty($this->label)) {
                        $this->label->setVariable($var);
                        $name .= $this->label->render($data);
                    }
                    $results[] = $this->format($name);
                } else {
                    foreach ($data->{$var} as $name) {
                        $results[] = $name->given . " " . $name->family;
                    }
                }
            }
        }
        $str  .= implode($this->delimiter, $results);
        return $this->addAffixes($str);
    }

    public function hasEtAl()
    {
        return !empty($this->etAl);
    }

    public function getEtAl()
    {
        return $this->etAl;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function getVariables()
    {
        return $this->variables;
    }
}