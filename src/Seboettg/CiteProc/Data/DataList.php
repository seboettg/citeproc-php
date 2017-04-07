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

/**
 * Class DataList
 *
 * @package Seboettg\CiteProc\Data
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class DataList extends ArrayList
{
    /**
     * @var string
     */
    private $subsequentAuthorSubstitute;

    /**
     * @var SubsequentAuthorSubstituteRule
     */
    private $subsequentAuthorSubstituteRule = "complete-all";

    /**
     * @var Citation
     */
    private $citation;

    public function __construct(array $data = [], $citation = null)
    {
        parent::__construct($data);
        $this->citation = $citation;
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
    public function setSubsequentAuthorSubstituteRule(SubsequentAuthorSubstituteRule $subsequentAuthorSubstituteRule)
    {
        $this->subsequentAuthorSubstituteRule = $subsequentAuthorSubstituteRule;
    }



}