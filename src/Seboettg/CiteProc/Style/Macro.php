<?php

namespace Seboettg\CiteProc\Style;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Macro
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
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

    public function __construct($node)
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