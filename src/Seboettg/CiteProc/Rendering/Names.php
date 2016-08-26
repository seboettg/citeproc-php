<?php

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\Rendering\Name\Name;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Util\Factory;



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

    /**
     * @var string
     */
    private $variable;

    /**
     * @var Name
     */
    private $name;

    /**
     * @var Label
     */
    private $label;

    /**
     * The optional cs:substitute element, which must be included as the last child element of cs:names, adds
     * substitution in case the name variables specified in the parent cs:names element are empty. The substitutions
     * are specified as child elements of cs:substitute, and must consist of one or more rendering elements (with the
     * exception of cs:layout). A shorthand version of cs:names without child elements, which inherits the attributes
     * values set on the cs:name and cs:et-al child elements of the original cs:names element, may also be used. If
     * cs:substitute contains multiple child elements, the first element to return a non-empty result is used for
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
     * @var Substitude
     */
    private $substitute;

    public function __construct(\SimpleXMLElement $node)
    {

        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {
            if ("name" === $child->getName()) {
                $this->name = Factory::create($child);
            }
            if ("label" === $child->getName()) {
                $this->label = new Label($child);
            }


        }

        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            if ("variable" === $attribute->getName()) {
                $this->variable = (string)$attribute;
                break;
            }
        }

        $this->initDelimiterAttributes($node);
        $this->initAffixesAttributes($node);
    }

    /**
     * This outputs the contents of one or more name variables (selected with the required variable attribute), each
     * of which can contain multiple names (e.g. the “author” variable contains all the author names of the cited item).
     * If multiple variables are selected (separated by single spaces), each variable is independently rendered in the
     * order specified, with one exception: when the selection consists of “editor” and “translator”, and when the
     * contents of these two name variables is identical, then the contents of only one name variable is rendered. In
     * addition, the “editortranslator” term is used if the cs:names element contains a cs:label element, replacing the
     * default “editor” and “translator” terms (e.g. resulting in “Doe (editor & translator)”).
     *
     * @param $data
     * @return string
     */
    public function render($data)
    {
        $arr = [];

        $names = explode(" ", $this->variable);

        $variables = explode(' ', $this->variable);
        foreach ($variables as $var) {
            if (isset($data->{$var}) && (!empty($data->{$var}))) {
                $matches[] = $var;
            }
        }

        if (empty($matches)) { // we don't have any primary suspects, so lets check the substitutes...
            if (isset($this->substitutes)) {
                foreach ($this->substitutes->getChildren() as $element) {
                    if (($element instanceof Names)) { //test to see if any of the other names variables has content
                        $sub_variables = explode(' ', $element->variable);
                        foreach ($sub_variables as $var) {
                            if (isset($data->{$var})) {
                                $matches[] = $var;
                                $this->citeProc->quash[] = $var;
                            }
                        }
                    } else { // if it's not a "names" element, just render it
                        $text = $element->render($data, $mode);
                        $this->citeProc->quash[] = isset($element->variable) ? $element->variable : $element->var;
                        if (!empty($text))
                            $variable_parts[] = $text;
                    }
                    if (!empty($matches))
                        break;
                }
            }
        }

        $ret = $this->name->render($data->{$this->variable});

        return "";
    }
}