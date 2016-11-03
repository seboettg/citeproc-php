<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\Style\Bibliography;
use Seboettg\CiteProc\Style\Citation;
use Seboettg\CiteProc\Style\Macro;
use Seboettg\CiteProc\Style\Sort\Sort;
use Seboettg\Collection\ArrayList;


/**
 * Class Context
 * @package Seboettg\CiteProc
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Context
{
    /**
     * @var ArrayList
     */
    private $macros;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var Bibliography
     */
    private $bibliography;

    /**
     * @var Citation
     */
    private $citation;

    /**
     * @var Sort
     */
    private $sorting;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var ArrayList
     */
    private $citationItems;

    public function __construct($locale = null)
    {
        if (!empty($locale)) {
            $this->locale = $locale;
        }

        $this->macros = new ArrayList();
        $this->citationItems = new ArrayList();
    }

    public function addMacro($key, $macro)
    {
        $this->macros->add($key, $macro);
    }

    /**
     * @param $key
     * @return Macro
     */
    public function getMacro($key)
    {
        return $this->macros->get($key);
    }

    /**
     * @param Locale $locale
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return Bibliography
     */
    public function getBibliography()
    {
        return $this->bibliography;
    }

    /**
     * @param Bibliography $bibliography
     */
    public function setBibliography(Bibliography $bibliography)
    {
        $this->bibliography = $bibliography;
    }

    /**
     * @return Citation
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * @param Citation $citation
     */
    public function setCitation($citation)
    {
        $this->citation = $citation;
    }

    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function isModeCitation()
    {
        return $this->mode === "citation";
    }

    public function isModeBibliography()
    {
        return $this->mode === "bibliography";
    }

    /**
     * @return ArrayList
     */
    public function getCitationItems()
    {
        return $this->citationItems;
    }

    /**
     * @param ArrayList $citationItems
     */
    public function setCitationItems($citationItems)
    {
        $this->citationItems = $citationItems;
    }

    public function hasCitationItems()
    {
        return (count($this->citationItems) > 0);
    }
}