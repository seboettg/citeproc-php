<?php

namespace Seboettg\CiteProc\Rendering\Choose;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\Collection\ArrayList;


/**
 * Class Choose
 * @package Seboettg\CiteProc\Node
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Choose implements RenderingInterface
{

    private $children;


    public  function __construct(\SimpleXMLElement $node)
    {
        $this->children = new ArrayList();

        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                case 'if':
                    $this->children->add("if", new ChooseIf($child));
                    break;
                case 'else-if':
                    $this->children->add("elseif", new ChooseIf($child));
                    break;
                case 'else':
                    $this->children->add("else", new ChooseElse($child));
                    break;
            }
        }
    }

    public function render($data)
    {
        $arr = [];
        if ($this->children->get("if")->match($data)) {
            $arr[] = $this->children->get("if")->render($data);
        } else if ($this->children->hasKey("elseif") && is_object($this->children->get("elseif")) && $this->children->get("elseif")->match($data)) {
            $arr[] = $this->children->get("elseif")->render($data);
        } else {
            if ($this->children->hasKey("else")) {
                $arr[] = $this->children->get("else")->render($data);
            }
        }

        return implode("", $arr);
    }
}

