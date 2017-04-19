<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style\Options;

/**
 * Class GlobalOptionsTrait
 * @package Seboettg\CiteProc\Style
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class BibliographyOptions
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

    /**
     * If set to “true” (“false” is the default), bibliographic entries are rendered with hanging-indents.
     * @var string
     */
    private $hangingIndent = false;

    /**
     * If set, subsequent lines of bibliographic entries are aligned along the second field. With “flush”, the first
     * field is flush with the margin. With “margin”, the first field is put in the margin, and subsequent lines are
     * aligned with the margin.
     * @var string
     */
    private $secondFieldAlign;

    /**
     * Specifies vertical line distance. Defaults to “1” (single-spacing), and can be set to any positive integer to
     * specify a multiple of the standard unit of line height (e.g. “2” for double-spacing).
     * @var string
     */
    private $lineSpacing;

    /**
     * Specifies vertical distance between bibliographic entries. By default (with a value of “1”), entries are
     * separated by a single additional line-height (as set by the line-spacing attribute). Can be set to any
     * non-negative integer to specify a multiple of this amount.
     * @var string
     */
    private $entrySpacing;

    public function __construct(\SimpleXMLElement $node)
    {

        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'subsequent-author-substitute':
                    $this->subsequentAuthorSubstitute = (string) $attribute;
                    break;
                case 'subsequent-author-substitute-rule':
                    $this->subsequentAuthorSubstituteRule = new SubsequentAuthorSubstituteRule((string) $attribute);
                    break;
                case 'hanging-indent':
                    $this->hangingIndent = "true" === (string) $attribute ? true : false;
                    break;
                case 'second-field-align':
                    $this->secondFieldAlign = (string) $attribute;
                    break;
                case 'line-spacing':
                    $this->lineSpacing = (string) $attribute;
                    break;
                case 'entry-spacing':
                    $this->entrySpacing = (string) $attribute;
            }
        }
        if (empty($this->subsequentAuthorSubstituteRule)) {
            $this->subsequentAuthorSubstituteRule = new SubsequentAuthorSubstituteRule("complete-all");
        }
    }

    /**
     * @return string
     */
    public function getSubsequentAuthorSubstitute()
    {
        return $this->subsequentAuthorSubstitute;
    }

    /**
     * @param string $subsequentAuthorSubstitute
     */
    public function setSubsequentAuthorSubstitute($subsequentAuthorSubstitute)
    {
        $this->subsequentAuthorSubstitute = $subsequentAuthorSubstitute;
    }

    /**
     * @return SubsequentAuthorSubstituteRule
     */
    public function getSubsequentAuthorSubstituteRule()
    {
        return $this->subsequentAuthorSubstituteRule;
    }

    /**
     * @param SubsequentAuthorSubstituteRule $subsequentAuthorSubstituteRule
     */
    public function setSubsequentAuthorSubstituteRule($subsequentAuthorSubstituteRule)
    {
        $this->subsequentAuthorSubstituteRule = $subsequentAuthorSubstituteRule;
    }

    /**
     * @return string
     */
    public function getHangingIndent()
    {
        return $this->hangingIndent;
    }

    /**
     * @param string $hangingIndent
     */
    public function setHangingIndent($hangingIndent)
    {
        $this->hangingIndent = $hangingIndent;
    }

    /**
     * @return string
     */
    public function getSecondFieldAlign()
    {
        return $this->secondFieldAlign;
    }

    /**
     * @param string $secondFieldAlign
     */
    public function setSecondFieldAlign($secondFieldAlign)
    {
        $this->secondFieldAlign = $secondFieldAlign;
    }

    /**
     * @return string
     */
    public function getLineSpacing()
    {
        return $this->lineSpacing;
    }

    /**
     * @param string $lineSpacing
     */
    public function setLineSpacing($lineSpacing)
    {
        $this->lineSpacing = $lineSpacing;
    }

    /**
     * @return string
     */
    public function getEntrySpacing()
    {
        return $this->entrySpacing;
    }

    /**
     * @param string $entrySpacing
     */
    public function setEntrySpacing($entrySpacing)
    {
        $this->entrySpacing = $entrySpacing;
    }


}