<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Macro
 *
 * Macros, defined with cs:macro elements, contain formatting instructions. Macros can be called with cs:text from
 * within other macros and the cs:layout element of cs:citation and cs:bibliography, and with cs:key from within cs:sort
 * of cs:citation and cs:bibliography. It is recommended to place macros after any cs:locale elements and before the
 * cs:citation element.
 *
 * Macros are referenced by the value of the required name attribute on cs:macro. The cs:macro element must contain one
 * or more rendering elements.
 *
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Macro implements RenderingInterface
{

    /**
     * @var ArrayList
     */
    private $children;

    /**
     * @var string
     */
    private $name;

    /**
     * Macro constructor.
     * @param \SimpleXMLElement $node
     * @throws CiteProcException
     */
    public function __construct(\SimpleXMLElement $node)
    {
        $attr = $node->attributes();
        if (!isset($attr['name'])) {
            throw new CiteProcException("Attribute \"name\" needed.");
        }
        $this->name = (string) $attr['name'];

        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child));
        }
    }

    public function render($data)
    {
        $ret = "";
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $ret .= $child->render($data);
        }
        return $ret;
    }

    public function getName()
    {
        return $this->name;
    }
}