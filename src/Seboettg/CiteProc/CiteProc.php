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
use Seboettg\CiteProc\Style\Bibliography;
use Seboettg\CiteProc\Style\Citation;
use Seboettg\CiteProc\Style\Macro;
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
    public function __construct($styleSheet, $lang = "en-US")
    {
        $this->styleSheet = $styleSheet;
        self::$context = new Context();
        self::$context->setLocale(new Locale\Locale($lang)); //init locale
        $this->styleSheetXml = new \SimpleXMLElement($this->styleSheet);
        $this->parse($this->styleSheetXml);
    }

    private function parse(\SimpleXMLElement $style)
    {

        foreach ($style as $node) {
            $name = $node->getName();
            switch ($name) {
                case 'info':
                    break;
                case 'locale':
                    self::$context->getLocale()->addXml($node);
                    break;
                case 'macro':
                    $macro = new Macro($node);
                    self::$context->addMacro($macro->getName(), $macro);
                    break;
                case 'bibliography':
                    $bibliography = new Bibliography($node);
                    self::$context->setBibliography($bibliography);
                    break;
                case 'citation':
                    $citation = new Citation($node);
                    self::$context->setCitation($citation);
                    break;
            }
        }
    }


    public function bibliography($data = '')
    {
        return self::$context->getBibliography()->render($data);
    }

}