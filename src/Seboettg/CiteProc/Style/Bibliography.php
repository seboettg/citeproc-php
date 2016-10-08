<?php

namespace Seboettg\CiteProc\Style;

use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Style\Sort\Sort;


/**
 * Class Bibliography
 * @package Seboettg\CiteProc
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Bibliography extends StyleElement
{
    /**
     * Bibliography constructor.
     * @param \SimpleXMLElement $node
     */
    public function __construct(\SimpleXMLElement $node)
    {
        parent::__construct($node);
    }
}