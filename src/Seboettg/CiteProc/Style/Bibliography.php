<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style;

use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\Style\Options\BibliographyOptions;
use Seboettg\CiteProc\CiteProc;

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
    private $node;

    /**
     * Bibliography constructor.
     * @param \SimpleXMLElement $node
     * @param Root $parent
     */
    public function __construct(\SimpleXMLElement $node, $parent)
    {
        parent::__construct($node, $parent);
        $this->node = $node;
        $bibliographyOptions = new BibliographyOptions($node);
        CiteProc::getContext()->setBibliographySpecificOptions($bibliographyOptions);
        $this->initInheritableNameAttributes($node);
    }

    /**
     * @param array|DataList $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        if (!$this->attributesInitialized) {
            $this->initInheritableNameAttributes($this->node);
        }
        $subsequentAuthorSubstitute = CiteProc::getContext()
            ->getBibliographySpecificOptions()
            ->getSubsequentAuthorSubstitute();

        $subsequentAuthorSubstituteRule = CiteProc::getContext()
            ->getBibliographySpecificOptions()
            ->getSubsequentAuthorSubstituteRule();

        if ($subsequentAuthorSubstitute !== null && !empty($subsequentAuthorSubstituteRule)) {
            CiteProc::getContext()->getCitationItems()->setSubsequentAuthorSubstitute($subsequentAuthorSubstitute);
            CiteProc::getContext()->getCitationItems()->setSubsequentAuthorSubstituteRule($subsequentAuthorSubstituteRule);
        }

        return $this->layout->render($data, $citationNumber);
    }
}