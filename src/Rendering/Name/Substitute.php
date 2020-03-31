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
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\Rendering;
use Seboettg\CiteProc\RenderingState;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;
use stdClass;

/**
 * Class Substitute
 * The optional cs:substitute element, which must be included as the last child element of cs:names, adds substitution
 * in case the name variables specified in the parent cs:names element are empty. The substitutions are specified as
 * child elements of cs:substitute, and must consist of one or more rendering elements (with the exception of
 * cs:layout).
 *
 * A shorthand version of cs:names without child elements, which inherits the attributes values set on the cs:name and
 * cs:et-al child elements of the original cs:names element, may also be used.
 *
 * If cs:substitute contains multiple child elements, the first element to return a non-empty result is used for
 * substitution. Substituted variables are suppressed in the rest of the output to prevent duplication. An example,
 * where an empty “author” name variable is substituted by the “editor” name variable, or, when no editors exist, by
 * the “title” macro:
 *   <macro name="author">
 *      <names variable="author">
 *        <substitute>
 *          <names variable="editor"/>
 *          <text macro="title"/>
 *        </substitute>
 *      </names>
 *   </macro>
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Substitute implements Rendering
{

    /**
     * @var ArrayList
     */
    private $children;

    /**
     * @var Names
     */
    private $parent;

    /**
     * Substitute constructor.
     * @param SimpleXMLElement $node
     * @param Names $parent
     * @throws InvalidStylesheetException
     * @throws InvalidStylesheetException
     */
    public function __construct(SimpleXMLElement $node, Names $parent)
    {
        $this->parent = $parent;
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {

            /** @var SimpleXMLElement $child */
            if ($child->getName() === "names") {

                /** @var Names $names */
                $names = Factory::create($child, $this);

                /* A shorthand version of cs:names without child elements, which inherits the attributes values set on
                the cs:name and cs:et-al child elements of the original cs:names element, may also be used. */
                if (!$names->hasEtAl()) {
                    // inherit et-al
                    if ($this->parent->hasEtAl()) {
                        $names->setEtAl($this->parent->getEtAl());
                    }
                }
                if (!$names->hasName()) {
                    // inherit name
                    if ($this->parent->hasName()) {
                        $names->setName($this->parent->getName());
                    }
                }
                // inherit label
                if (!$names->hasLabel() && $this->parent->hasLabel()) {
                    $names->setLabel($this->parent->getLabel());
                }

                $this->children->append($names);
            } else {
                $object = Factory::create($child, $this);
                $this->children->append($object);
            }
        }
    }

    /**
     * @param stdClass $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $ret = [];
        if (CiteProc::getContext()->getRenderingState()->getValue() !== RenderingState::SORTING) {
            CiteProc::getContext()->setRenderingState(new RenderingState(RenderingState::SUBSTITUTION));
        }

        /** @var Rendering $child */
        foreach ($this->children as $child) {
            /* If cs:substitute contains multiple child elements, the first element to return a
            non-empty result is used for substitution. */
            $res = $child->render($data, $citationNumber);
            if (!empty($res)) {
                $ret[] = $res;
                break;
            }
        }
        if (CiteProc::getContext()->getRenderingState()->getValue() === RenderingState::SUBSTITUTION) {
            CiteProc::getContext()->setRenderingState(new RenderingState(RenderingState::RENDERING));
        }
        return implode("", $ret);
    }
}
