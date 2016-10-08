<?php

namespace Seboettg\CiteProc\Style;
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Util\Factory;


/**
 * Class Citation
 * @package Seboettg\CiteProc\Node\Style
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Citation extends StyleElement
{
    /**
     * Citation constructor.
     * @param \SimpleXMLElement $node
     */
    public function __construct(\SimpleXMLElement $node)
    {
        parent::__construct($node);
    }
}