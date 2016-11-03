<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style;
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Style\Sort\Sort;


/**
 * Class Bibliography
 *
 * The cs:bibliography element describes the formatting of bibliographies, which list one or more bibliographic sources.
 * The required cs:layout child element describes how each bibliographic entry should be formatted. cs:layout may be
 * preceded by a cs:sort element, which can be used to specify how references within the bibliography should be sorted
 * (see Sorting).
 *
 * @package Seboettg\CiteProc
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
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