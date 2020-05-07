<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Choose;

use Seboettg\CiteProc\Constraint\Constraint;
use Seboettg\CiteProc\Constraint\Factory;
use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\Exception\ClassNotFoundException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\Group;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Rendering\Rendering;
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;

/**
 * Class ChooseIf
 * @package Seboettg\CiteProc\Node\Choose
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class ChooseIf implements Rendering, HasParent
{
    /**
     * @var ArrayList<Constraint>
     */
    private $constraints;

    /**
     * @var ArrayList
     */
    protected $children;

    /**
     * @var string
     */
    private $match;

    /**
     * @var
     */
    protected $parent;
    /**
     * @param SimpleXMLElement $node
     * @param Choose $parent
     * @throws InvalidStylesheetException
     * @throws ClassNotFoundException
     */
    public function __construct(SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;
        $this->constraints = new ArrayList();
        $this->children = new ArrayList();
        $this->match = (string) $node['match'];
        if (empty($this->match)) {
            $this->match = Constraint::MATCH_ALL;
        }
        foreach ($node->attributes() as $name => $value) {
            if ('match' !== $name) {
                $this->constraints->append(Factory::createConstraint((string) $name, (string) $value, $this->match));
            }
        }
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child, $this));
        }
    }
    /**
     * @param array|DataList $data
     * @param null|int $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $ret = [];
        /** @var Rendering $child */
        foreach ($this->children as $child) {
            $ret[] = $child->render($data, $citationNumber);
        }
        $glue = "";
        $parent = $this->parent->getParent();
        if ($parent instanceof Group && $parent->hasDelimiter()) {
            $glue = $parent->getDelimiter();
        }
        return implode($glue, array_filter($ret));
    }
    /**
     * @param $data
     * @param null|int $citationNumber
     * @return bool
     */
    public function match($data, $citationNumber = null)
    {
        if ($this->constraints->count() === 1) {
            return $this->constraints->current()->validate($data);
        }
        $result = true;
        /** @var Constraint $constraint */
        foreach ($this->constraints as $constraint) {
            if ($this->match === "any") {
                if ($constraint->validate($data, $citationNumber)) {
                    return true;
                }
            } else {
                $result &= $constraint->validate($data, $citationNumber);
            }
        }
        if ($this->constraints->count() > 1 && $this->match === "all") {
            return (bool) $result;
        } elseif ($this->match === "none") {
            return !((bool) $result);
        }
        return false;
    }


    /**
     * @noinspection PhpUnused
     * @return Choose
     */
    public function getParent()
    {
        return $this->parent;
    }
}
