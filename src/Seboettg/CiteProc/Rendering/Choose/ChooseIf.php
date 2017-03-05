<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Choose;
use Seboettg\CiteProc\Constraint\ConstraintInterface;
use Seboettg\CiteProc\Constraint\Factory;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\Collection\ArrayList;


/**
 * Class ChooseIf
 * @package Seboettg\CiteProc\Node\Choose
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class ChooseIf implements RenderingInterface
{

    /**
     * @var ConstraintInterface
     */
    private $constraint;

    /**
     * @var ArrayList
     */
    protected $children;

    private $match;

    public function __construct(\SimpleXMLElement $node)
    {
        $this->children = new ArrayList();

        $this->match = (string) $node['match'];

        foreach ($node->attributes() as $name => $value) {
            if ('match' !== $name) {
                $this->constraint = Factory::createConstraint((string) $name, (string) $value, $this->match);
            }
        }

        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child));
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

        return false;
    }
}
