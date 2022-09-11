<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Data;

use Seboettg\CiteProc\Style\Options\SubsequentAuthorSubstituteRule;
use Seboettg\Collection\ArrayList;

class DataList extends ArrayList
{
    private ?string $subsequentAuthorSubstitute;
    private SubsequentAuthorSubstituteRule $subsequentAuthorSubstituteRule;


    public function __construct()
    {
        parent::__construct();
        $this->subsequentAuthorSubstitute = null;
        $this->subsequentAuthorSubstituteRule = SubsequentAuthorSubstituteRule::COMPLETE_ALL();
    }

    public function getSubsequentAuthorSubstitute(): ?string
    {
        return $this->subsequentAuthorSubstitute;
    }

    /**
     * @param string $subsequentAuthorSubstitute
     */
    public function setSubsequentAuthorSubstitute(string $subsequentAuthorSubstitute)
    {
        $this->subsequentAuthorSubstitute = $subsequentAuthorSubstitute;
    }

    /**
     * @return SubsequentAuthorSubstituteRule
     */
    public function getSubsequentAuthorSubstituteRule(): SubsequentAuthorSubstituteRule
    {
        return $this->subsequentAuthorSubstituteRule;
    }

    /**
     * @param SubsequentAuthorSubstituteRule $subsequentAuthorSubstituteRule
     */
    public function setSubsequentAuthorSubstituteRule(SubsequentAuthorSubstituteRule $subsequentAuthorSubstituteRule)
    {
        $this->subsequentAuthorSubstituteRule = $subsequentAuthorSubstituteRule;
    }
}
