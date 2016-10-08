<?php

namespace Seboettg\CiteProc\Rendering\Choose;
use Seboettg\CiteProc\Rendering\RenderingInterface;


/**
 * Class ChooseElse
 * @package Seboettg\CiteProc\Node\Choose
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class ChooseElse extends ChooseIf implements RenderingInterface
{
    public function render($data)
    {
        $ret = "";
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $ret .= $child->render($data);
        }
        return $ret;
    }
}