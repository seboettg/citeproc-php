<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style;

use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Rendering\Rendering;
use Seboettg\CiteProc\Styles\ConsecutivePunctuationCharacterTrait;
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
class Macro implements Rendering, HasParent
{
    use ConsecutivePunctuationCharacterTrait;

    /**
     * @var ArrayList
     */
    private $children;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Root
     */
    private $parent;
    /**
     * Macro constructor.
     * @param \SimpleXMLElement $node
     * @param Root $parent
     * @throws CiteProcException
     */
    public function __construct(\SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;
        $attr = $node->attributes();
        if (!isset($attr['name'])) {
            throw new CiteProcException("Attribute \"name\" needed.");
        }
        $this->name = (string) $attr['name'];

        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child, $this));
        }
    }

    /**
     * @param \stdClass $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $ret = [];
        /** @var Rendering $child */
        foreach ($this->children as $child) {
            $res = $child->render($data, $citationNumber);
            $this->getChildsAffixesAndDelimiter($child);
            if (!empty($res)) {
                $ret[] = $res;
            }
        }
        $res = implode("", $ret);
        if (!empty($res)) {
            $res = $this->removeConsecutiveChars($res);
        }
        return $res;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Root
     */
    public function getParent()
    {
        return $this->parent;
    }

}