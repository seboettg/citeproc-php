<?php

namespace Seboettg\CiteProc\Style;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Style\Sort\Sort;


/**
 * Class StyleElement
 * @package Seboettg\CiteProc\Style
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
abstract class StyleElement implements RenderingInterface
{

    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var bool
     */
    private $doNotSort;


    /**
     * Parses the configuration.
     *
     * @throws \ErrorException If layout is missing
     */
    protected function __construct(\SimpleXMLElement $node)
    {
        // init child elements
        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {
            switch ($child->getName()) {

                /* The cs:layout rendering element is a required child element of cs:citation and cs:bibliography. It
                 * must contain one or more of the other rendering elements described below, and may carry affixes and
                 * formatting attributes.
                 */
                case 'layout':
                    $this->layout   =   new Layout($child);
                    break;

                /* cs:citation and cs:bibliography may include a cs:sort child element before the cs:layout element to
                 * specify the sorting order of respectively cites within citations, and bibliographic entries within
                 * the bibliography. In the absence of cs:sort, cites and bibliographic entries appear in the order in
                 * which they are cited.
                 */
                case 'sort':
                    $sorting = new Sort($child);
                    CiteProc::getContext()->setSorting($sorting);
            }
        }
    }

    public function render($data)
    {
        return $this->layout->render($data);
    }
}