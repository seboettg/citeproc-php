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
use Seboettg\CiteProc\Node\Macro\Macro;
use Seboettg\CiteProc\Node\Style;
use Seboettg\CiteProc\Node\Style\Bibliography;
use Seboettg\CiteProc\Node\Style\Info;
use Seboettg\Collection\ArrayList;


/**
 * Class CiteProc
 * @package Seboettg\CiteProc
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class CiteProc
{

    /**
     * @var Context
     */
    private static $context;


    /**
     * @return Context
     */
    public static function getContext()
    {
        return self::$context;
    }

    /**
     * @param Context $context
     */
    public static function setContext($context)
    {
        self::$context = $context;
    }


    /**
     * @var string
     */
    private $styleSheet;


    /**
     * @var \SimpleXMLElement
     */
    private $styleSheetXml;


    /**
     * CiteProc constructor.
     * @param string $styleSheet xml formatted csl stylesheet
     */
    public function __construct($styleSheet, $locale = 'en-US')
    {
        $this->styleSheet = $styleSheet;

    }


    /**
     * Render the bibliography form the already injected data or from the given values.
     *
     * @param string $style
     * @param string $input
     * @param string $language
     * @return string
     */
    public function bibliography($data = '', $language = '')
    {
        $this->styleSheetXml = new \SimpleXMLElement($this->styleSheet);
        $this->parse($this->styleSheetXml);
    }


    private function parse(\SimpleXMLElement $style)
    {
        $info = $locale = $macros = $bibliography = $citation = null;

        //$info = new Style\Info($style->info);
        $locale = new Locale\Locale($style->locale);

        $macros = $this->parseMacros($style);
        //$bibliography = new Node\Style\Bibliography($style->bibliography);
        //$citation = new Node\Style\Citation($style->citation);
        //$this->style = new Style($info, $locale, $macros, $bibliography, $citation);
    }

    private function parseMacros($nodes)
    {
        $macroList = new ArrayList();
        foreach ($nodes as $node) {
            if ($node->getName() === "macro") {
                $attrName = (string) $node['name'];
                $macroList[$attrName] = new Macro($node);
            }
        }
        return $macroList;
    }
}