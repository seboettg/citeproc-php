<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Choose;
use Seboettg\CiteProc\Constraint\ConstraintInterface;
use Seboettg\CiteProc\Constraint\Factory;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\Collection\ArrayList;


/**
 * Class ChooseIf
 * @package Seboettg\CiteProc\Node\Choose
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class ChooseIf implements RenderingInterface, HasParent
{

    /**
     * @var ArrayList<ConstraintInterface>
     */
    private $constraints;

    /**
     * @var ArrayList
     */
    protected $children;

    private $match;

    /**
     * @var
     */
    protected $parent;

    /**
     * @param Choose $parent
     */
    public function __construct(\SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;
        $this->constraints = new ArrayList();
        $this->children = new ArrayList();
        $this->match = (string) $node['match'];

        if (empty($this->match)) {
            $this->match = "all";
        }

        foreach ($node->attributes() as $name => $value) {
            if ('match' !== $name) {
                $this->constraints->append(Factory::createConstraint((string)$name, (string)$value));
            }
        }

        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child, $this));
        }
    }

    public function render($data, $citationNumber = null)
    {
        $ret = "";
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $ret .= $child->render($data);
        }
        return $ret;
    }

    public function match($data)
    {
        if (isset($this->constraint)) {
            return $this->constraint->validate($data);
        }

        $result = $this->match === "none" ? false : true;

        /** @var ConstraintInterface $constraint */
        foreach ($this->constraints as $constraint) {
            if ($this->match === "any") {
                if ($constraint->validate($data)) {
                    return true;
                }
            } else {
                $result &= $constraint->validate($data);
            }
        }

        if ($this->match === "all") {
            return $result;
        } else if ($this->match === "none") {
            return !$result;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }


}
