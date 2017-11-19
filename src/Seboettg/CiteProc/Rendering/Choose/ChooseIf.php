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
use Seboettg\CiteProc\Rendering\Rendering;
use Seboettg\CiteProc\Rendering\RendersEmptyVariables;
use Seboettg\CiteProc\Rendering\RendersEmptyVariablesTrait;
use Seboettg\Collection\ArrayList;


/**
 * Class ChooseIf
 * @package Seboettg\CiteProc\Node\Choose
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class ChooseIf implements Rendering, HasParent
{

    /**
     * @var ArrayList<ConstraintInterface>
     */
    private $constraints;

    /**
     * @var ConstraintInterface
     */
    private $constraint;

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
     * @param \SimpleXMLElement $node
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
                $this->constraints->append(Factory::createConstraint((string) $name, (string) $value));
            }
        }

        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child, $this));
        }
    }

    /**
     * @param array|\Seboettg\CiteProc\Data\DataList $data
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
        return implode("", $ret);
    }

    /**
     * @param $data
     * @param null|int $citationNumber
     * @return bool
     */
    public function match($data, $citationNumber = null)
    {
        if (isset($this->constraint)) {
            return $this->constraint->validate($data);
        }

        $result = true;

        /** @var ConstraintInterface $constraint */
        foreach ($this->constraints as $constraint) {
            if ($this->match === "any") {
                if ($constraint->validate($data, $citationNumber)) {
                    return true;
                }
            } else {
                $result &= $constraint->validate($data, $citationNumber);
            }
        }

        if ($this->match === "all") {
            return (bool) $result;
        } else if ($this->match === "none") {
            return !$result;
        }
        return false;
    }

    /**
     * @return Choose
     */
    public function getParent()
    {
        return $this->parent;
    }

}
