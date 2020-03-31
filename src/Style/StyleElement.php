<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Root\Root;
use Seboettg\CiteProc\Style\Sort\Sort;
use SimpleXMLElement;

/**
 * Class StyleElement
 *
 * StyleElement is an abstract class which must be extended by Citation and Bibliography class. The constructor
 * of StyleElement class parses the cs:layout element (necessary for cs:citation and cs:bibliography) and the optional
 * cs:sort element.
 *
 * @package Seboettg\CiteProc\Style
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
abstract class StyleElement
{

    use InheritableNameAttributesTrait;
    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var bool
     */
    protected $doNotSort;

    protected $parent;

    /**
     * Parses the configuration.
     *
     * @param SimpleXMLElement $node
     * @param Root $parent
     * @throws InvalidStylesheetException
     */
    protected function __construct(SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;
        // init child elements
        /** @var SimpleXMLElement $child */
        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                /* The cs:layout rendering element is a required child element of cs:citation and cs:bibliography. It
                 * must contain one or more of the other rendering elements described below, and may carry affixes and
                 * formatting attributes.
                 */
                case 'layout':
                    $this->layout = new Layout($child, $this);
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

    /**
     * @return Root
     */
    public function getParent()
    {
        return $this->parent;
    }
}
