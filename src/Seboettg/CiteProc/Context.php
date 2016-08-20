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

    private $locale;

    private $bibliography;

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

    public function getMacro($key)
    {
        return $this->macros->get($key);
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function &getLocale()
    {
        return $this->locale;
    }

    /**
     * @return mixed
     */
    public function getBibliography()
    {
        return $this->bibliography;
    }

    /**
     * @param mixed $bibliography
     */
    public function setBibliography($bibliography)
    {
        $this->bibliography = $bibliography;
    }

    /**
     * @return mixed
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * @param mixed $citation
     */
    public function setCitation($citation)
    {
        $this->citation = $citation;
    }

}