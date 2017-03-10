<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles;

/**
 * Class ConsecutivePunctuationCharacterTrait
 * @package Seboettg\CiteProc\Styles
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait ConsecutivePunctuationCharacterTrait
{


    private $childsPrefixes = [];

    private $childsSuffixes = [];

    private $childsDelimiter = [];

    /**
     * @param $punctuationSign
     * @param $subject
     * @return string
     */
    public function removeConsecutivePunctuation($punctuationSign, $subject)
    {
        $pattern = '/\\' . trim($punctuationSign) . '{2,}/';
        if (preg_match($pattern, $subject)) {
            $res = preg_replace($pattern, $punctuationSign, $subject);
            return $res;
        }
        return $subject;
    }

    /**
     * @param $child
     */
    protected function getChildsAffixesAndDelimiter($child)
    {
        if (method_exists($child, "renderPrefix")) {
            if (!empty($child->renderPrefix()) && !in_array($child->renderPrefix(), $this->childsPrefixes)) {
                $this->childsPrefixes[] = $child->renderPrefix();
            }
        }
        if (method_exists($child, "renderSuffix")) {
            if (!empty($child->renderSuffix()) && !in_array($child->renderSuffix(), $this->childsSuffixes)) {
                $this->childsSuffixes[] = $child->renderSuffix();
            }
        }
        if (method_exists($child, "getDelimiter")) {
            if (!empty($child->getDelimiter()) && !in_array($child->getDelimiter(), $this->childsDelimiter)) {
                $this->childsDelimiter[] = $child->getDelimiter();
            }
        }
    }

    /**
     * @param string $string
     * @return string
     */
    protected function removeConsecutiveChars($string)
    {
        foreach ($this->childsPrefixes as $prefix) {
            $string = $this->removeConsecutivePunctuation($prefix, $string);
        }
        foreach ($this->childsSuffixes as $suffix) {
            $string = $this->removeConsecutivePunctuation($suffix, $string);
        }
        foreach ($this->childsDelimiter as $delimiter) {
            $string = $this->removeConsecutivePunctuation($delimiter, $string);
        }

        $string = preg_replace("/\s{2,}/", " ", $string);

        return $string;
    }
}