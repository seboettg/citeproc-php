<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Substitute
 * The optional cs:substitute element, which must be included as the last child element of cs:names, adds substitution
 * in case the name variables specified in the parent cs:names element are empty. The substitutions are specified as
 * child elements of cs:substitute, and must consist of one or more rendering elements (with the exception of cs:layout).
 *
 * A shorthand version of cs:names without child elements, which inherits the attributes values set on the cs:name and
 * cs:et-al child elements of the original cs:names element, may also be used.
 *
 * If cs:substitute contains multiple child elements, the first element to return a non-empty result is used for
 * substitution. Substituted variables are suppressed in the rest of the output to prevent duplication. An example,
 * where an empty “author” name variable is substituted by the “editor” name variable, or, when no editors exist, by
 * the “title” macro:
 * <pre>
 *   <macro name="author">
 *      <names variable="author">
 *        <substitute>
 *          <names variable="editor"/>
 *          <text macro="title"/>
 *        </substitute>
 *      </names>
 *   </macro>
 * </pre>
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Substitute implements RenderingInterface
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
     * @param \SimpleXMLElement $node
     * @param Names $parent
     */
    public function __construct(\SimpleXMLElement $node, Names $parent)
    {
        $this->parent = $parent;
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {

            /** @var \SimpleXMLElement $child */
            if ($child->getName() === "Names") {

                /** @var Names $names */
                $names = Factory::create($child);

                /* A shorthand version of cs:names without child elements, which inherits the attributes values set on
                the cs:name and cs:et-al child elements of the original cs:names element, may also be used. */
                if (!$names->hasChilds()) {
                    if ($this->parent->hasEtAl()) {
                        $names->setEtAl($this->parent->getEtAl());
                    }
                    if ($this->parent->hasName()) {
                        $names->setName($this->parent->getName());
                    }
                }
                $this->children->append($names);

            } else {
                $object = Factory::create($child);
                $this->children->append($object);
            }
        }
    }

    /**
     * @param $data
     * @return string
     */
    public function render($data)
    {
        $str = "";

        /* adds substitution in case the name variables specified in the parent cs:names element are empty. */
        if ($this->parent->getVariables()->count() === 0) {
            /** @var RenderingInterface $child */
            foreach ($this->children as $child) {
                /* If cs:substitute contains multiple child elements, the first element to return a
                non-empty result is used for substitution. */
                $str = $child->render($data);
                if (!empty($str)) {
                    return $str;
                }
            }
            return $str;
        }
    }
}