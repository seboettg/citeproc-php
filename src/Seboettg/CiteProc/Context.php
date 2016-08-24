<?php
/*
 * This file is a part of HDS (HeBIS Discovery System). HDS is an 
 * extension of the open source library search engine VuFind, that 
 * allows users to search and browse beyond resources. More 
 * Information about VuFind you will find on http://www.vufind.org
 * 
 * Copyright (C) 2016 
 * HeBIS Verbundzentrale des HeBIS-Verbundes 
 * Goethe-Universität Frankfurt / Goethe University of Frankfurt
 * http://www.hebis.de
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Seboettg\CiteProc;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\Style\Bibliography;
use Seboettg\CiteProc\Style\Citation;
use Seboettg\CiteProc\Style\Macro;
use Seboettg\Collection\ArrayList;


/**
 * Class Context
 * @package Seboettg\CiteProc
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
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

    public function __construct($locale = null)
    {
        if (!empty($locale)) {
            $this->locale = $locale;
        }

        $this->macros = new ArrayList();
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

}