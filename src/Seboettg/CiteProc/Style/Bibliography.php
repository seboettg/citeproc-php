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
     * If set, the value of this attribute replaces names in a bibliographic entry that also occur in the preceding
     * entry. The exact method of substitution depends on the value of the subsequent-author-substitute-rule attribute.
     * Substitution is limited to the names of the first cs:names element rendered. (Bibliography-specific option)
     *
     * @var string
     */
    private $subsequentAuthorSubstitute;

    /**
     * Specifies when and how names are substituted as a result of subsequent-author-substitute.
     * (Bibliography-specific option)
     *
     * @var SubsequentAuthorSubstituteRule
     */
    private $subsequentAuthorSubstituteRule;

    private $node;

    /**
     * Bibliography constructor.
     * @param \SimpleXMLElement $node
     */
    public function __construct(\SimpleXMLElement $node, $parent)
    {
        parent::__construct($node, $parent);
        $this->node = $node;

        //<bibliography subsequent-author-substitute="---" subsequent-author-substitute-rule="complete-all">
        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'subsequent-author-substitute':
                    $this->subsequentAuthorSubstitute = (string) $attribute;
                    break;
                case 'subsequent-author-substitute-rule':
                    $this->subsequentAuthorSubstituteRule = new SubsequentAuthorSubstituteRule((string) $attribute);
            }
        }
        $this->initInheritableNameAttributes($node);
    }

    /**
     * @param \stdClass $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        if (!$this->attributesInitialized) {
            $this->initInheritableNameAttributes($this->node);
        }

        if (!empty($this->subsequentAuthorSubstitute) && !empty($this->subsequentAuthorSubstituteRule)) {
            CiteProc::getContext()->getCitationItems()->setSubsequentAuthorSubstitute($this->subsequentAuthorSubstitute);
            CiteProc::getContext()->getCitationItems()->setSubsequentAuthorSubstituteRule($this->subsequentAuthorSubstituteRule);
        }

        return $this->layout->render($data, $citationNumber);
    }

    /**
     * @return string
     */
    public function getSubsequentAuthorSubstitute()
    {
        return $this->subsequentAuthorSubstitute;
    }

    /**
     * @return SubsequentAuthorSubstituteRule
     */
    public function getSubsequentAuthorSubstituteRule()
    {
        return $this->subsequentAuthorSubstituteRule;
    }

}